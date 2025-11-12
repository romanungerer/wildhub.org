<?php
session_start();
include('db_config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>WildHub - Support Conservation</title>
  <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
  <meta name="description" content="WildHub connects people with wildlife conservation projects. Discover and support organizations protecting nature, donate to causes you care about, or create a page for your own conservation organization to share your mission and receive donations. Join the movement for wildlife protection." />

  <link rel="icon" type="image/png" sizes="192x192" href="/favicon.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
  
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Roboto', sans-serif; margin: 0; padding: 0; background: #f0f4f8; }
    header { background: #004d40; color: white; padding: 1rem 2rem; padding-left: 15px;
      text-align: center; display: flex; justify-content: space-between; align-items: center;
      height: 30px; min-height: 40px; position: relative; }
    .search-bar { margin: 2rem auto; text-align: center; }
    .search-bar input { padding: 0.5rem; width: 80%; max-width: 500px; border: 1px solid #ccc;
      border-radius: 10px; font-size: 1rem; }
    .projects { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem; padding: 2rem; }
    .project { background: white; border-radius: 20px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      overflow: hidden; display: flex; flex-direction: column; transition: transform 0.2s; }
    .project:hover { transform: scale(1.01); }
    .project img { width: 100%; height: 200px; object-fit: cover; }
    .project-content { padding: 1rem; flex: 1; }
    .project h3 { margin-top: 0; }
    .project-content p { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;
      overflow: hidden; text-overflow: ellipsis; max-height: 4.5em; }
    .donate-button { display: block; background: #00796b; color: white; padding: 0.75rem;
      text-align: center; border: none; border-radius: 10px; text-decoration: none; margin: 1rem; }
    .menu-icon { display: flex; flex-direction: column; justify-content: center; align-items: center;
      gap: 5px; cursor: pointer; height: 24px; z-index: 20; }
    .logo-container { display: flex; align-items: center; gap: 5px; }
    .logo-img { height: 60px; width: auto; }
    .menu-icon div { width: 25px; height: 3px; background-color: white; border-radius: 2px; }
    .dropdown-menu { display: none; position: absolute; top: 60px; right: 20px; background-color: white;
      border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); overflow: hidden; z-index: 10; width: 220px; }
    .dropdown-menu a { display: block; padding: 12px; color: #004d40; text-decoration: none; font-weight: 500;
      transition: background 0.2s; }
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

    <div class="dropdown-menu" id="dropdownMenu">
      <a href="about.php">About Us</a>
      <a href="upload.php">Create Organization</a>

      <?php
      if (isset($_SESSION['user'])):
        $creator = $_SESSION['user'];
        if ($stmt = $conn->prepare("SELECT id, title FROM projects WHERE created_by = ? LIMIT 1")) {
          $stmt->bind_param("s", $creator);
          $stmt->execute();
          $res = $stmt->get_result();
          if ($res && $res->num_rows > 0) {
            echo '<a href="edit_my_project.php">✏️ Edit My Organization</a>';
          }
          $stmt->close();
        }
      ?>
        <a href="logout.php">Log Out (<?php echo htmlspecialchars($_SESSION['user']); ?>)</a>
      <?php else: ?>
        <a href="login.php">Log In / Sign Up</a>
      <?php endif; ?>
    </div>
  </header>

  <div class="search-bar">
    <input type="text" id="searchInput" placeholder="Search for an organization..." onkeyup="filterProjects()">
  </div>

  <div class="projects" id="projectsContainer">
    <?php
      $sql = "SELECT * FROM projects";
      $result = $conn->query($sql);
      $projects = [];

      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          $img = htmlspecialchars($row['image']);
          $title = htmlspecialchars($row['title']);
          $desc = htmlspecialchars($row['description']);
          $id = intval($row['id']);
          $link = htmlspecialchars($row['link']);
          $species = htmlspecialchars($row['species']);
          $country = htmlspecialchars($row['country']);

          // ✅ Compute completeness score
          $fields = [$img, $title, $desc, $link, $species, $country];
          $filled = 0;
          foreach ($fields as $f) { if (trim($f) !== '') $filled++; }
          $row['score'] = $filled;
          $projects[] = $row;
        }

        // ✅ Sort by completeness (desc), randomize ties
        usort($projects, function($a, $b) {
          if ($a['score'] === $b['score']) {
            return rand(-1, 1); // random order if equal score
          }
          return $b['score'] - $a['score'];
        });

        // ✅ Display projects after sorting
        foreach ($projects as $row) {
          $img = htmlspecialchars($row['image']);
          $title = htmlspecialchars($row['title']);
          $desc = htmlspecialchars($row['description']);
          $id = intval($row['id']);
          $link = htmlspecialchars($row['link']);
          $species = htmlspecialchars($row['species']);
          $country = htmlspecialchars($row['country']);
          $score = intval($row['score']);

          echo "
          <div class='project' data-score='{$score}'>
            <img src='{$img}' alt='{$title}'>
            <div class='project-content'>
              <h3>{$title}</h3>
              <p>{$desc}</p>
              <p class='meta' style='display:none;'>{$species} {$country}</p>
              <div style='display:flex; justify-content:space-between; gap:0.1rem; margin:0.1rem;'>
                <a href='{$link}' class='donate-button' target='_blank' style='flex:1; text-align:center;'>Donate Now</a>
                <a href='project.php?id={$id}' class='donate-button' style='background:#00796b; flex:1; text-align:center;'>More Info</a>
              </div>
            </div>
          </div>";
        }
      } else {
        echo "<p style='text-align:center;'>No projects found.</p>";
      }
      $conn->close();
    ?>
  </div>

  <script>
    function filterProjects() {
      const search = document.getElementById("searchInput").value.toLowerCase();
      const projects = Array.from(document.querySelectorAll(".project"));

      const filtered = projects.filter(p => {
        const title = p.querySelector("h3").textContent.toLowerCase();
        const meta = p.querySelector(".meta").textContent.toLowerCase();
        const match = (title.includes(search) || meta.includes(search));
        p.style.display = match ? "block" : "none";
        return match;
      });

      // ✅ Still sort filtered by completeness for relevance
      filtered.sort((a, b) => {
        const scoreA = parseInt(a.dataset.score) || 0;
        const scoreB = parseInt(b.dataset.score) || 0;
        return scoreB - scoreA;
      });

      const container = document.getElementById("projectsContainer");
      filtered.forEach(p => container.appendChild(p));
    }

    const menuIcon = document.getElementById("menuIcon");
    const dropdownMenu = document.getElementById("dropdownMenu");
    menuIcon.addEventListener("click", () => dropdownMenu.classList.toggle("show"));
  </script>
</body>
</html>
