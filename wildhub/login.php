<?php
session_start();
include('db_config.php');

$message = "";
$showSignUp = false; // Whether to show extra signup fields

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['username']); // Can be username or email
    $password = trim($_POST['password']);
    $email = trim($_POST['email'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    // Check both username and email in one query
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // User exists -> login
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['username'];
            header("Location: index.php");
            exit;
        } else {
            $message = "Incorrect password.";
        }
    } else {
        // User does not exist -> show signup fields
        $showSignUp = true;

        // If email + confirmPassword are filled, try to create account
        if ($email && $confirmPassword) {
            if ($password !== $confirmPassword) {
                $message = "Passwords do not match.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = "Invalid email address.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $insert = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
                $insert->bind_param("sss", $identifier, $hashedPassword, $email);

                if ($insert->execute()) {
                    $_SESSION['user'] = $identifier;
                    header("Location: upload.php");
                    exit;
                } else {
                    $message = "Error creating account.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login / Sign Up - WildHub</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<style>
body { font-family: 'Roboto', sans-serif; margin: 0; background: #f0f4f8; }
h1 { font-size: 2rem; margin: 0; }
.login-box { background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 350px; margin: 3rem auto; }
h2 { text-align: center; color: #004d40; }
input { width: 92.2%; padding: 0.75rem; margin-top: 10px; border: 1px solid #ccc; border-radius: 10px; }
button { background: #00796b; color: white; border: none; padding: 0.75rem; border-radius: 10px; width: 100%; margin-top: 15px; cursor: pointer; }
button:hover { background: #005f56; }
.message { color: red; text-align: center; margin-top: 10px; }
.login-box {
  background: white;
  padding: 2rem;
  border-radius: 15px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  width: 350px;
  align-items: center;
}
main {
  flex: 1;
  display: flex;
  justify-content: none
  align-items: center;
}
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

<main>
    <div class="login-box">
      <h2>Log In / Sign Up</h2>
      <form method="POST">
        <input type="text" name="username" placeholder="Username or Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <?php if ($showSignUp): ?>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <?php endif; ?>

        <button type="submit"><?php echo $showSignUp ? 'Sign Up' : 'Continue'; ?></button>
      </form>

      <?php if ($message) echo "<p class='message'>$message</p>"; ?>
      <p style="text-align:center; margin-top:1rem;">
        <a href="index.php" style="color:#00796b; text-decoration:none;">← Back to Home</a>
      </p>
    </div>
  </main>



</body>
</html>
