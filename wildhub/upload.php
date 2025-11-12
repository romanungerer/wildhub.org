<?php
session_start();
include('db_config.php');

// Verificar si el usuario NO está logeado
if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Organization - WildHub</title>
  <link rel="icon" type="image/png" href="logo.png">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <style>
  body {
    font-family: 'Roboto', sans-serif;
    background: #f0f4f8;
    margin: 0;
    padding: 0;
  }

  .container {
    max-width: 600px;
    background: white;
    margin: 3rem auto;
    padding: 2rem 3rem;
    border-radius: 15px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    align-items: center; 
    justify-content: center;
  }

  h2 {
    text-align: center;
    color: #004d40;
    margin-bottom: 1.5rem;
  }

  form {
    display: flex;
    flex-direction: column;
    align-items: center; 
    gap: 1rem;
    width: 100%; 
    max-width: 400px;
  }

  input, textarea, select {
    padding: 0.75rem;
    border: 1px solid #ccc;
    border-radius: 10px;
    font-size: 1rem;
    width: 100%;
  }

  button {
    background: #00796b;
    color: white;
    padding: 0.75rem;
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.3s;
    width: 100%;
  }

  button:hover {
    background: #005f56;
  }

  .success, .error {
    text-align: center;
    font-weight: bold;
    margin-top: 1rem;
  }

  .success { color: green; }
  .error { color: red; }
</style>

<link rel="stylesheet" href="style.css">
</head>

<body>
<header>
    <div style="display: flex; align-items: center; gap: 10px;">
    <a href="index.php">
      <div class="logo-container">
        <img src="logo.png" alt="WildHub Logo" class="logo-img">
      </div>
      </a>
      <h1 style="font-size: 40px;">wildhub</h1>
    </div>
   
</header>

<div class="container">
    <h2>Add a New Organization</h2>

<?php
$hasOrg = false;
if ($user !== 'wildhub') {
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM projects WHERE created_by = ?");
    $checkStmt->bind_param("s", $user);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        $hasOrg = true;
        echo "<p class='error'>You already have an organization registered. Contact us for more info</p>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$hasOrg) {
  $title = $_POST['title'];
  $description = $_POST['description'];
  $mission = $_POST['mission'];
  $actions = $_POST['actions'];
  $email = $_POST['email'];
  $donation_use = $_POST['donation_use'];
  $social_media_link = $_POST['social_media_link'];
  $link = $_POST['link'];
  $country = $_POST['country'];
  $exact_location = $_POST['exact_location'] ?? null;
  $species = $_POST['species'] ?? null;
  $legally_constituted = $_POST['legally_constituted'] ?? 'No';
  $area_conserved_m2 = !empty($_POST['area_conserved_m2']) ? intval($_POST['area_conserved_m2']) : null;
  $organisms_protected = !empty($_POST['organisms_protected']) ? intval($_POST['organisms_protected']) : null;
  $created_by = $_SESSION['user'];

  // Handle image upload
  $targetDir = "uploads/";
  if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

  $fileName = basename($_FILES["image"]["name"]);
  $targetFilePath = $targetDir . time() . "_" . $fileName;
  $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

  $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
  $additionalImages = [];

  // handle additional images
  if (!empty($_FILES['additional_images']['name'][0])) {
    foreach ($_FILES['additional_images']['name'] as $key => $imgName) {
      $imgTmp = $_FILES['additional_images']['tmp_name'][$key];
      $imgType = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
      if (in_array($imgType, $allowedTypes)) {
        $newPath = $targetDir . time() . "_" . basename($imgName);
        if (move_uploaded_file($imgTmp, $newPath)) {
          $additionalImages[] = $newPath;
        }
      }
    }
  }

  if (in_array($fileType, $allowedTypes)) {
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
      $additionalImagesStr = implode(",", $additionalImages);

      $stmt = $conn->prepare("INSERT INTO projects 
        (title, description, email, mission, actions, donation_use, social_media_link, image, link, country, exact_location, species, legally_constituted, area_conserved_m2, organisms_protected_per_month, additional_images, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("sssssssssssssiiss",
        $title,
        $description,
        $email,
        $mission,
        $actions,
        $donation_use,
        $social_media_link,
        $targetFilePath,
        $link,
        $country,
        $exact_location,
        $species,
        $legally_constituted,
        $area_conserved_m2,
        $organisms_protected,
        $additionalImagesStr,
        $created_by
      );

      if ($stmt->execute()) {
        echo "<p class='success'>Organization added successfully!</p>";
      } else {
        echo "<p class='error'>Database error: " . htmlspecialchars($conn->error) . "</p>";
      }

      $stmt->close();
    } else {
      echo "<p class='error'>Error uploading image.</p>";
    }
  } else {
    echo "<p class='error'>Invalid file type. Only JPG, PNG, and GIF allowed.</p>";
  }
}
?>

<?php if (!$hasOrg || $user === 'wildhub') : ?>
<form method="POST" enctype="multipart/form-data">
  <input type="text" name="title" placeholder="Organization Name" required>
  <input type="email" name="email" placeholder="E-mail"  required>     
  <input type="url" name="social_media_link" placeholder="Social Media Link (Instagram, Facebook, etc.)" required>
  <input type="url" name="link" placeholder="Main Donation Link" required>
  

  <input type="text" name="country" placeholder="Country" required>
  <input type="text" name="exact_location" placeholder="Exact Location (Google Maps link)">
  <input type="text" name="species" placeholder="Protected Species (Example: Gorilla, Whale, Baobab)">
  
  <textarea name="description" placeholder="Short Description" rows="3" required></textarea>

  <textarea name="mission" placeholder="¿What is your mission?" rows="3" required></textarea>
  <textarea name="actions" placeholder="¿What specific actions are being made to achieve it?" rows="3" required></textarea>
  <textarea name="donation_use" placeholder="¿How exactly is the money from donations going to be spent?" rows="3" required></textarea>

  <label>¿Are you a legally constituted ONG?</label>
  <select name="legally_constituted" required>
    <option value="Yes">Yes</option>
    <option value="No">No</option>
  </select>

  <input type="number" name="area_conserved_m2" placeholder="Number of m² conserved">
  <input type="number" name="organisms_protected" placeholder="Nº of individual organisms protected">

  <label>Main photo:</label>
  <input type="file" name="image" accept="image/*" required>
  <label>Additional photos:</label>
  <input type="file" name="additional_images[]" accept="image/*" multiple>

  <button type="submit">Upload Organization</button>
</form>
<?php endif; ?>

<p style="text-align:center; margin-top:1rem;">
  <a href="index.php" style="color:#00796b; text-decoration:none;">← Back to Home</a>
</p>
</div>
</body>
</html>
