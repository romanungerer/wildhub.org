<?php
$servername = "localhost";
$username = "houskosb_wildhub";   // El usuario MySQL que creaste en cPanel
$password = "Bailaro1997+";
$dbname = "houskosb_wildhub";     // El nombre de tu base de datos

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");
if ($conn->connect_error) {
  die("Error de conexión: " . $conn->connect_error);
}
?>