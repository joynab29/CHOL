<?php
include("dbconnect.php");

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chol | Choose Activity</title>
    <link rel="stylesheet" href="activity.css">  <!-- Link to the new CSS file -->
</head>
<body>
    <!-- Navbar -->
    <nav>
        <div class="logo">CHOL</div>
        <ul>
            <li><a href="home.php">HOME</a></li>
            <li><a href="#">ABOUT US</a></li>
            <li><a href="logout.php">LOG OUT</a></li>
        </ul>
    </nav>

    <div class="content">
        <h1 class="choose-activity">Choose Activity</h1>
    </div>

    <!-- Activity Options -->
    <div class="go-to-options">
        <a href="activity_poll.php" class="btn">Activity Poll</a>
        <a href="activity_wheel.php" class="btn">Activity Wheel</a>
    </div>

</body>
</html>