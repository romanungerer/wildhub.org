<?php
session_start();
include('db_config.php');

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit();
}

$username = $_SESSION['user'];

// Fetch ONG data
$stmt = $conn->prepare("SELECT * FROM projects WHERE created_by = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$ong = $result->fetch_assoc();

if (!$ong) {
  echo "<p style='text-align:center; margin-top:2rem;'>You haven’t created an organization yet. <a href='upload.php'>Create one here</a>.</p>";
  exit;
}

// Handle updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'];
  $email = $_POST['email'];
  $description = $_POST['description'];
  $mission = $_POST['mission'];
  $actions = $_POST['actions'];
  $donation_use = $_POST['donation_use'];
  $social_media_link = $_POST['social_media_link'];
  $link = $_POST['link'];
  $country = $_POST['country'];
  $exact_location = $_POST['exact_location'] ?? null;
  $species = $_POST['species'] ?? null;
  $legally_constituted = $_POST['legally_constituted'] ?? 'No';
  $area_conserved_m2 = !empty($_POST['area_conserved_m2']) ? intval($_POST['area_conserved_m2']) : null;
  $organisms_protected = !empty($_POST['organisms_protected']) ? intval($_POST['organisms_protected']) : null;

  $imagePath = $ong['image'];
  $additionalImagesStr = $ong['additional_images'];

  $targetDir = "uploads/";
  if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

  // New main image
  if (!empty($_FILES['image']['name'])) {
    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . time() . "_" . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($fileType, $allowedTypes) && move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
      $imagePath = $targetFilePath;
    }
  }

  // New additional images
  if (!empty($_FILES['additional_images']['name'][0])) {
    $additionalImages = [];
    foreach ($_FILES['additional_images']['name'] as $key => $imgName) {
      $imgTmp = $_FILES['additional_images']['tmp_name'][$key];
      $imgType = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
      if (in_array($imgType, ['jpg', 'jpeg', 'png', 'gif'])) {
        $newPath = $targetDir . time() . "_" . basename($imgName);
        if (move_uploaded_file($imgTmp, $newPath)) {
          $additionalImages[] = $newPath;
        }
      }
    }
    $additionalImagesStr = implode(",", $additionalImages);
  }

  // Update query including all fields
  $stmt = $conn->prepare("UPDATE projects SET 
    title=?, description=?, mission=?, actions=?, email=?, donation_use=?, social_media_link=?, image=?, link=?, 
    country=?, exact_location=?, species=?, legally_constituted=?, area_conserved_m2=?, organisms_protected_per_month=?, additional_images=?
    WHERE created_by=?");

  $stmt->bind_param("sssssssssssssiiss",
    $title, $description, $mission, $actions, $email, $donation_use,
    $social_media_link, $imagePath, $link, $country, $exact_location,
    $species, $legally_constituted, $area_conserved_m2, $organisms_protected,
    $additionalImagesStr, $username
  );

  if ($stmt->execute()) {
    echo "<p style='color:green; text-align:center;'>✅ Organization updated successfully!</p>";
    $stmt = $conn->prepare("SELECT * FROM projects WHERE created_by = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $ong = $stmt->get_result()->fetch_assoc();
  } else {
    echo "<p style='color:red; text-align:center;'>❌ Error updating: " . htmlspecialchars($conn->error) . "</p>";
  }
}
?>
<?php
session_start();
include('db_config.php');

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit();
}

$username = $_SESSION['user'];

// Fetch the ONG created by this user
$stmt = $conn->prepare("SELECT * FROM projects WHERE created_by = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$ong = $result->fetch_assoc();

if (!$ong) {
  echo "<p style='text-align:center; margin-top:2rem;'>You haven’t created an organization yet. <a href='upload.php'>Create one here</a>.</p>";
  exit;
}

// Handle form update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'];
  $description = $_POST['description'];
  $link = $_POST['link'];
  $country = $_POST['country'];
  $exact_location = $_POST['exact_location'] ?? null;
  $species = $_POST['species'] ?? null;
  $legally_constituted = $_POST['legally_constituted'] ?? 'No';
  $area_conserved_m2 = !empty($_POST['area_conserved_m2']) ? intval($_POST['area_conserved_m2']) : null;
  $organisms_protected = !empty($_POST['organisms_protected']) ? intval($_POST['organisms_protected']) : null;

  $imagePath = $ong['image'];
  $additionalImagesStr = $ong['additional_images'];

  $targetDir = "uploads/";
  if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

  // If new main image uploaded
  if (!empty($_FILES['image']['name'])) {
    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . time() . "_" . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($fileType, $allowedTypes) && move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
      $imagePath = $targetFilePath;
    }
  }

  // If new additional images uploaded
  if (!empty($_FILES['additional_images']['name'][0])) {
    $additionalImages = [];
    foreach ($_FILES['additional_images']['name'] as $key => $imgName) {
      $imgTmp = $_FILES['additional_images']['tmp_name'][$key];
      $imgType = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
      if (in_array($imgType, ['jpg','jpeg','png','gif'])) {
        $newPath = $targetDir . time() . "_" . basename($imgName);
        if (move_uploaded_file($imgTmp, $newPath)) {
          $additionalImages[] = $newPath;
        }
      }
    }
    $additionalImagesStr = implode(",", $additionalImages);
  }

  $stmt = $conn->prepare("UPDATE projects 
    SET title=?, description=?, image=?, link=?, country=?, exact_location=?, species=?, legally_constituted=?, area_conserved_m2=?, organisms_protected_per_month=?, additional_images=?
    WHERE created_by=?");
  $stmt->bind_param("ssssssssisss",
    $title, $description, $imagePath, $link, $country, $exact_location,
    $species, $legally_constituted, $area_conserved_m2, $organisms_protected,
    $additionalImagesStr, $username
  );

  if ($stmt->execute()) {
    echo "<p style='color:green; text-align:center;'>✅ Organization updated successfully!</p>";
    // Refresh ONG data
    $stmt = $conn->prepare("SELECT * FROM projects WHERE created_by = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $ong = $stmt->get_result()->fetch_assoc();
  } else {
    echo "<p style='color:red; text-align:center;'>Error updating: " . htmlspecialchars($conn->error) . "</p>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit My Organization - WildHub</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<style>
body { font-family: 'Roboto', sans-serif; background: #f0f4f8; margin: 0; }
.container {
  max-width: 600px; background: white; margin: 3rem auto; padding: 2rem 3rem;
  border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
input, textarea, select {
  width: 100%; padding: 0.75rem; margin-top: 0.5rem;
  border: 1px solid #ccc; border-radius: 10px;
}
button {
  background: #00796b; color: white; border: none;
  padding: 0.75rem; border-radius: 10px; cursor: pointer; width: 100%;
}
button:hover { background: #005f56; }
img.preview { width: 100%; max-height: 200px; object-fit: cover; border-radius: 10px; margin-bottom: 10px; }
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
<h2>Edit My Organization</h2>
<form method="POST" enctype="multipart/form-data">
  <input type="text" name="title" value="<?= htmlspecialchars($ong['title']) ?>" placeholder="Organization Name" required>
  <input type="email" name="email" value="<?= htmlspecialchars($ong['email']) ?>" placeholder="E-mail" required>
  <input type="url" name="social_media_link" value="<?= htmlspecialchars($ong['social_media_link']) ?>" placeholder="Social Media Link" required>
  <input type="url" name="link" value="<?= htmlspecialchars($ong['link']) ?>" placeholder="Main Donation Link" required>
  <input type="text" name="country" value="<?= htmlspecialchars($ong['country']) ?>" placeholder="Country" required>
  <input type="text" name="exact_location" value="<?= htmlspecialchars($ong['exact_location']) ?>" placeholder="Exact Location (Google Maps link)">
  <input type="text" name="species" value="<?= htmlspecialchars($ong['species']) ?>" placeholder="Protected Species (Example: Whale, Gorilla)">
  
  <textarea name="description" rows="3" placeholder="Short Description" required><?= htmlspecialchars($ong['description']) ?></textarea>
  <textarea name="mission" rows="3" placeholder="What is your mission?" required><?= htmlspecialchars($ong['mission']) ?></textarea>
  <textarea name="actions" rows="3" placeholder="Specific actions being made?" required><?= htmlspecialchars($ong['actions']) ?></textarea>
  <textarea name="donation_use" rows="3" placeholder="How is donation money spent?" required><?= htmlspecialchars($ong['donation_use']) ?></textarea>

  <label>Are you a legally constituted ONG?</label>
  <select name="legally_constituted">
    <option value="Yes" <?= $ong['legally_constituted']=='Yes'?'selected':'' ?>>Yes</option>
    <option value="No" <?= $ong['legally_constituted']=='No'?'selected':'' ?>>No</option>
  </select>

  <input type="number" name="area_conserved_m2" value="<?= htmlspecialchars($ong['area_conserved_m2']) ?>" placeholder="Area conserved (m²)">
  <input type="number" name="organisms_protected" value="<?= htmlspecialchars($ong['organisms_protected_per_month']) ?>" placeholder="Organisms protected per month">

  <label>Current Main Photo:</label>
  <img src="<?= htmlspecialchars($ong['image']) ?>" alt="Current Image" class="preview">
  <input type="file" name="image" accept="image/*">

  <label>Additional Photos:</label>
  <input type="file" name="additional_images[]" accept="image/*" multiple>

  <button type="submit">Save Changes</button>
</form>

<p style="text-align:center; margin-top:1rem;">
  <a href="index.php" style="color:#00796b; text-decoration:none;">← Back to Home</a>
</p>
</div>
</body>
</html>
