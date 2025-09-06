
<?php
include("dbconnect.php");

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html"); // Redirect to login page if not logged in
    exit;
}

$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chol | Activity Poll</title>
    <link rel="stylesheet" href="activity_poll.css">  <!-- Link to the updated CSS file -->
</head>
<body>
    <!-- Navbar -->
    <nav>
        <div class="logo">CHOL</div>
        <ul>
            <li><a href="home.php">HOME</a></li>
        </ul>
    </nav>

    <div class="content">
        <h1 class="choose-activity">Activity Poll</h1>
    </div>

    <!-- Poll Options -->
    <div class="poll-options-container">
        <a href="create_poll.php" class="btn">Create Poll</a>
        <a href="vote_poll.php" class="btn">Vote</a>
    </div>

</body>
</html>
