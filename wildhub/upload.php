<?php
session_start();
include('db_config.php');

// Verificar si el usuario NO está logeado
if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
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
    align-items: center; /* centra horizontalmente el contenido */
    justify-content: center; /* centra verticalmente si el contenido es más pequeño */
  }

  h2 {
    text-align: center;
    color: #004d40;
    margin-bottom: 1.5rem;
  }

  form {
    display: flex;
    flex-direction: column;
    align-items: center; /* centra los elementos dentro del form */
    gap: 1rem;
    width: 100%; /* mantiene el ancho completo dentro del contenedor */
    max-width: 400px; /* limita el ancho del formulario para centrarlo mejor */
  }

  input, textarea {
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
      <div class="logo-container">
        <img src="logo.png" alt="WildHub Logo" class="logo-img">
      </div>
      <h1 style="font-size: 50px;">wildhub</h1>
    </div>
</header>

<div class="container">
    <h2>Add a New Organization</h2>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $title = $_POST['title'];
      $description = $_POST['description'];
      $link = $_POST['link'];
      $country = $_POST['country'];
      $exact_location = $_POST['exact_location'] ?? null;
      $species = $_POST['species'] ?? null;

      // Handle image upload
      $targetDir = "uploads/";
      if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

      $fileName = basename($_FILES["image"]["name"]);
      $targetFilePath = $targetDir . time() . "_" . $fileName;
      $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

      $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
      if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
          // Insert into database
          $stmt = $conn->prepare("INSERT INTO projects (title, description, image, link, country, exact_location, species) VALUES (?, ?, ?, ?, ?, ?, ?)");
          $stmt->bind_param("sssssss", $title, $description, $targetFilePath, $link, $country, $exact_location, $species);

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

    <form method="POST" enctype="multipart/form-data">
      <input type="text" name="title" placeholder="Organization Name" required>
      <textarea name="description" placeholder="Short Description" rows="4" required></textarea>
      <input type="url" name="link" placeholder="Donation Link" required>
      
      <input type="text" name="country" placeholder="Country" required>
      <input type="text" name="exact_location" placeholder="Exact Location* (google maps link)">
      <input type="text" name="species" placeholder="Remarkable Species (Example: Gorila, Whale, Baobab)">

      <input type="file" name="image" accept="image/*" required>
      <button type="submit">Upload Organization</button>
    </form>

    <p style="text-align:center; margin-top:1rem;">
      <a href="index.php" style="color:#00796b; text-decoration:none;">← Back to Home</a>
    </p>
</div>
</body>
</html>
