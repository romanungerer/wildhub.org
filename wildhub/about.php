<?php
session_start();
include('db_config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WildHub - Support Conservation</title>
  <link rel="icon" type="image/png" href="logo.png">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Roboto', sans-serif; margin: 0; padding: 0; background: #f0f4f8; }
    header {
      background: #004d40; color: white; padding: 1rem 2rem; padding-left: 15px;
      text-align: center; display: flex; justify-content: space-between; align-items: center;
      height: 30px; min-height: 40px; position: relative;
    }
    .search-bar { margin: 2rem auto; text-align: center; }
    .search-bar input {
      padding: 0.5rem; width: 80%; max-width: 500px; border: 1px solid #ccc;
      border-radius: 10px; font-size: 1rem;
    }
    .projects {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem; padding: 2rem;
    }
    .project {
      background: white; border-radius: 20px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      overflow: hidden; display: flex; flex-direction: column;
    }
    .project img { width: 100%; height: 200px; object-fit: cover; }
    .project-content { padding: 1rem; flex: 1; }
    .project h3 { margin-top: 0; }
    .donate-button {
      display: block; background: #00796b; color: white; padding: 0.75rem;
      text-align: center; border: none; border-radius: 10px; text-decoration: none; margin: 1rem;
    }
    .menu-icon {
      display: flex; flex-direction: column; justify-content: center; align-items: center;
      gap: 5px; cursor: pointer; height: 24px; z-index: 20;
    }
    .logo-container { display: flex; align-items: center; gap: 5px; }
    .logo-img { height: 60px; width: auto; }
    .menu-icon div { width: 25px; height: 3px; background-color: white; border-radius: 2px; }
    .dropdown-menu {
      display: none; position: absolute; top: 60px; right: 20px; background-color: white;
      border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      overflow: hidden; z-index: 10; width: 170px;
    }
    .dropdown-menu a {
      display: block; padding: 12px; color: #004d40; text-decoration: none; font-weight: 500;
      transition: background 0.2s;
    }
    .dropdown-menu a:hover { background-color: #e0f2f1; }
    .dropdown-menu.show { display: block; }
  </style>
</head>
<body>
  <header>
    <div style="display: flex; align-items: center; gap: 10px;">
      <div class="logo-container">
        <img src="logo.png" alt="WildHub Logo" class="logo-img">
      </div>
      <h1 style="font-size: 50px;">wildhub</h1>
    </div>
    <div class="menu-icon" id="menuIcon">
      <div></div><div></div><div></div>
    </div>

    <!-- ✅ DROPDOWN MENU WITH LOGIN/LOGOUT LOGIC -->
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

  

  <div class="projects" id="projectsContainer">
    WildHub is a global platform dedicated to connecting people and organizations in the fight to protect life on Earth.
Its mission is to unify conservation efforts worldwide, providing a space where anyone can easily discover, support, and engage with real projects that safeguard biodiversity.
WildHub transforms scattered conservation initiatives into accessible, actionable opportunities — empowering individuals and institutions to make transparent donations, join volunteer programs, and contribute to meaningful environmental impact.<br><br>

At its core, WildHub developes technology to protect the wildlife, helping build a future where all species can have a voice in this noisy society and where biodiversity can be preserved and thrive.
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
