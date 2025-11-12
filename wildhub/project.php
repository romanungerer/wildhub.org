<?php
session_start();
include('db_config.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("Invalid project ID.");
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM projects WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
  die("Project not found.");
}

$project = $result->fetch_assoc();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($project['title']); ?> - WildHub</title>
  <link rel="icon" type="image/png" href="logo.png">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body { 
      font-family: 'Roboto', sans-serif; 
      margin: 0; 
      padding: 0; 
      background: #f0f4f8; 
    }
    header {
      background: #004d40; 
      color: white; 
      padding: 1rem 2rem; 
      padding-left: 15px;
      text-align: center; 
      display: flex; 
      justify-content: space-between; 
      align-items: center;
      height: 30px; 
      min-height: 40px; 
      position: relative;
    }
    .menu-icon {
      display: flex; 
      flex-direction: column; 
      justify-content: center; 
      align-items: center;
      gap: 5px; 
      cursor: pointer; 
      height: 24px; 
      z-index: 20;
    }
    .menu-icon div { 
      width: 25px; 
      height: 3px; 
      background-color: white; 
      border-radius: 2px; 
    }
    .logo-container { display: flex; align-items: center; gap: 5px; }
    .logo-img { height: 60px; width: auto; }
    .dropdown-menu {
      display: none; 
      position: absolute; 
      top: 60px; 
      right: 20px; 
      background-color: white;
      border-radius: 10px; 
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      overflow: hidden; 
      z-index: 10; 
      width: 170px;
    }
    .dropdown-menu a {
      display: block; 
      padding: 12px; 
      color: #004d40; 
      text-decoration: none; 
      font-weight: 500;
      transition: background 0.2s;
    }
    .dropdown-menu a:hover { background-color: #e0f2f1; }
    .dropdown-menu.show { display: block; }

    .container {
      max-width: 900px; 
      margin: 2rem auto; 
      background: white;
      padding: 2rem; 
      border-radius: 20px; 
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .main-img {
      width: 100%; 
      height: 350px; 
      object-fit: cover; 
      border-radius: 15px;
      margin-bottom: 1rem;
    }
    h1 { 
      color: #004d40; 
      margin-bottom: 1rem;
      text-align: center;
    }
    .info p { 
      margin: 0.5rem 0; 
      font-size: 1rem;
    }
    .section-title {
      margin-top: 1.5rem;
      font-weight: bold;
      color: #004d40;
    }
    .button-group {
      margin-top: 2rem;
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 1rem;
    }
    .btn {
      display: inline-block; 
      background: #004d40; 
      color: white; 
      padding: 0.75rem 1.5rem;
      border-radius: 10px; 
      text-decoration: none; 
      transition: background 0.2s;
      font-weight: 500;
    }
    .btn:hover { background: #00695c; }
    .gallery {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 1rem;
      justify-content: center;
    }
    .gallery img {
      width: 180px;
      height: 130px;
      object-fit: cover;
      border-radius: 10px;
    }
  </style>
</head>
<body>
  <header>
    <div style="display: flex; align-items: center; gap: 10px;">
    <a href="index.php">
      <div class="logo-container">
        <img src="logo.png" alt="WildHub Logo" class="logo-img">
      </div>
      </a>
      <h4 style="font-size: 40px;">wildhub</h4>
    </div>
    <div class="menu-icon" id="menuIcon">
      <div></div><div></div><div></div>
    </div>

    <div class="dropdown-menu" id="dropdownMenu">
      <a href="index.php">Support Wildlife</a>
      <a href="upload.php">Create Organization</a>

      <?php if(isset($_SESSION['user'])): ?>
        <a href="logout.php">Log Out (<?php echo htmlspecialchars($_SESSION['user']); ?>)</a>
      <?php else: ?>
        <a href="login.php">Log In / Sign Up</a>
      <?php endif; ?>
    </div>
  </header>

  <div class="container">
    <img src="<?php echo htmlspecialchars($project['image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" class="main-img">
    
    <h1><?php echo htmlspecialchars($project['title']); ?></h1>
    
    <div class="info">
      <p><strong>Species Protected:</strong> <?php echo htmlspecialchars($project['species'] ?: '—'); ?></p>
      <p><strong>Country:</strong> <?php echo htmlspecialchars($project['country'] ?: '—'); ?></p>
      <p><strong>Legally Constituted ONG:</strong> <?php echo htmlspecialchars($project['legally_constituted'] ?: 'No'); ?></p>
      <p><strong>Area Conserved:</strong> <?php echo htmlspecialchars($project['area_conserved_m2'] ? number_format($project['area_conserved_m2']) . ' m²' : '—'); ?></p>
      <p><strong>Organisms Protected per Month:</strong> <?php echo htmlspecialchars($project['organisms_protected_per_month'] ?: '—'); ?></p>

      <?php if (!empty($project['exact_location'])): ?>
        <p><strong>Exact Location:</strong> <a href="<?php echo htmlspecialchars($project['exact_location']); ?>" target="_blank">View on Google Maps</a></p>
      <?php endif; ?>

      <p class="section-title">Description:</p>
      <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>

      <?php if (!empty($project['mission'])): ?>
        <p class="section-title">Mission:</p>
        <p><?php echo nl2br(htmlspecialchars($project['mission'])); ?></p>
      <?php endif; ?>

      <?php if (!empty($project['actions'])): ?>
        <p class="section-title">Actions:</p>
        <p><?php echo nl2br(htmlspecialchars($project['actions'])); ?></p>
      <?php endif; ?>

      <?php if (!empty($project['donation_use'])): ?>
        <p class="section-title">Use of Donations:</p>
        <p><?php echo nl2br(htmlspecialchars($project['donation_use'])); ?></p>
      <?php endif; ?>
    </div>

    <?php if (!empty($project['additional_images'])): ?>
      <div class="section-title">Gallery</div>
      <div class="gallery">
        <?php 
          $images = explode(",", $project['additional_images']);
          foreach ($images as $img) {
            echo '<img src="' . htmlspecialchars($img) . '" alt="Project photo">';
          }
        ?>
      </div>
    <?php endif; ?>

    <div class="button-group">
      <a href="<?php echo htmlspecialchars($project['link']); ?>" target="_blank" class="btn">Donate Now</a>

      <?php if (!empty($project['email'])): ?>
        <a href="mailto:<?php echo htmlspecialchars($project['email']); ?>" class="btn">Contact</a>
      <?php endif; ?>

      <?php if (!empty($project['exact_location'])): ?>
        <a href="<?php echo htmlspecialchars($project['exact_location']); ?>" target="_blank" class="btn">Visit Location</a>
      <?php endif; ?>
    </div>
  </div>

  <script>
    const menuIcon = document.getElementById("menuIcon");
    const dropdownMenu = document.getElementById("dropdownMenu");
    menuIcon.addEventListener("click", () => {
      dropdownMenu.classList.toggle("show");
    });
  </script>
</body>
</html>
