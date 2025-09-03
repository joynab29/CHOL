<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html"); // or your login page
    exit;
}
$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Chol | Home</title>
  <link rel="stylesheet" href="home.css">   
</head>
<body>
  <nav>
    <div class="logo">CHOL</div>
    <ul>
      <li><a href="home.php">HOME</a></li>
      <li><a href="#">ABOUT US</a></li>
      <li><a href="logout.php">LOG OUT</a></li>
    </ul>
  </nav>

  <div class="content">
    <h1 class="welcome">Welcome, <span id='username'><?php echo $username; ?>!</span></h1>
    <p>Ready to plan hangouts?</p>
  </div>
  <div class='go-to-options'>
      <a href="profile.php" class='btn'>PROFILE</a>
      <a href="friends.php" class='btn'>FRIENDS</a>
      <a href="#" class='btn'>GROUPS</a>
      <a href="#" class='btn'>EVENTS</a>
  </div>
</body>
</html>
