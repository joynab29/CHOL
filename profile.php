<?php
// profile.php
session_start();

// If user isn't logged in, send them to login page
if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) {
    header("Location: index.html"); // your login page
    exit;
}

require_once "dbconnect.php"; // must define $conn (mysqli) connected to DB 'chol'

// Prefer id if you saved it at login; fallback to username
$userId = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? null;

// Fetch the user
if ($userId) {
    $stmt = $conn->prepare("SELECT id, First_name, Last_name, Username, Email, NID, Birthdate 
                            FROM User WHERE id = ?");
    $stmt->bind_param("i", $userId);
} else {
    $stmt = $conn->prepare("SELECT id, First_name, Last_name, Username, Email, NID, Birthdate 
                            FROM User WHERE Username = ?");
    $stmt->bind_param("s", $username);
}

$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    // If somehow no record found, log out and send to login
    header("Location: logout.php");
    exit;
}

// Format values safely
$first  = htmlspecialchars($user['First_name'] ?? '');
$last   = htmlspecialchars($user['Last_name'] ?? '');
$uname  = htmlspecialchars($user['Username'] ?? '');
$email  = htmlspecialchars($user['Email'] ?? '');
$nid    = htmlspecialchars($user['NID'] ?? '');
$birth  = htmlspecialchars($user['Birthdate'] ?? '');
$uid    = (int)($user['id'] ?? 0);

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Chol | Profile</title>
  <link rel="stylesheet" href="profile.css"> 
</head>
<body>
  <nav>
    <div class="logo">CHOL</div>
    <ul>
      <li><a href="home.php">Home</a></li>
      <li><a href="profile.php" style="color:#ff1add;">Profile</a></li>
      <li><a href="friends.php">Friends</a></li>
      <li><a href="groups.php">Groups</a></li>
      <li><a href="events.php">Events</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </nav>

  <main class="container">
    <h1 class="heading">Your Profile</h1>
    <section class="card">
      <div class="grid">
        <div class="field">
          <div class="label">First name</div>
          <div class="value"><?= $first ?></div>
        </div>
        <div class="field">
          <div class="label">Last name</div>
          <div class="value"><?= $last ?></div>
        </div>
        <div class="field">
          <div class="label">Username</div>
          <div class="value"><?= $uname ?></div>
        </div>
        <div class="field">
          <div class="label">Email</div>
          <div class="value"><?= $email ?></div>
        </div>
        <div class="field">
          <div class="label">NID</div>
          <div class="value"><?= $nid ?></div>
        </div>
        <div class="field">
          <div class="label">Birthdate</div>
          <div class="value"><?= $birth ?></div>
        </div>
      </div>

      <div class="row">
        <span class="label">User ID:</span>
        <span class="uid">#<?= $uid ?></span>
      </div>

      <div class="actions">
        <a class="btn primary" href="home.php">Back to Home</a>
        <a class="btn" href="logout.php">Logout</a>
      </div>
    </section>
  </main>
</body>
</html>
