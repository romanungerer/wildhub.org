<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>WildHub - Support Conservation</title>
  
  <meta name="description" content="WildHub connects people with wildlife conservation projects. Support conservation efforts worlwide. Discover organizations protecting nature, donate to wildlife conservation causes you care about, or create a page for your own organization to share your mission and receive donations. Join the movement for preserving life on Earth." />
  
  <!-- Standard favicon -->
<link rel="icon" href="/favicon.png" type="image/png">

<link rel="icon" href="/favicon.ico" type="image/x-icon">

<!-- Apple touch icon -->
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">

  
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <style>
    .visually-hidden {
  position: absolute !important;
  width: 1px; 
  height: 1px; 
  margin: -1px; 
  padding: 0; 
  border: 0; 
  clip: rect(0 0 0 0); 
  overflow: hidden;
  white-space: nowrap;
}
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
    <a href="index.php">
      <div class="logo-container">
        <img src="logo.png" alt="WildHub Logo" class="logo-img">
      </div>
      </a>
      <h1 style="font-size: 40px;">wildhub</h1>
    </div>

    <div class="menu-icon" id="menuIcon">
      <div></div><div></div><div></div>
    </div>