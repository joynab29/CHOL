<?php
require_once "auth.php";       
require_once "dbconnect.php";  

$me = $_SESSION['user_id'];


$search_results = [];
$q = '';
if (!empty($_GET['q'])) {
  $q = trim($_GET['q']);
  $like = "%{$q}%";

  $sql = "SELECT u.id, u.Username
          FROM User u
          WHERE (u.Username LIKE ? OR u.First_name LIKE ? OR u.Last_name LIKE ?)
            AND u.id <> ?
            AND u.id NOT IN (SELECT friend_id FROM Friends WHERE user_id = ?)
          ORDER BY u.Username
          LIMIT 25";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssii", $like, $like, $like, $me, $me);
  $stmt->execute();
  $search_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
}

/* ---------- Requests I received ---------- */
$requests = [];
$sql = "SELECT f.user_id AS requester_id, u.Username
        FROM Friends f
        JOIN User u ON u.id = f.user_id
        WHERE f.friend_id = ? AND f.status = 'pending'
        ORDER BY u.Username";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $me);
$stmt->execute();
$requests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/* ---------- My accepted friends ---------- */
$friends = [];
$sql = "SELECT u.id, u.Username
        FROM Friends f
        JOIN User u ON u.id = f.friend_id
        WHERE f.user_id = ? AND f.status = 'accepted'
        ORDER BY u.Username";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $me);
$stmt->execute();
$friends = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Chol | Friends</title>
  <link rel="stylesheet" href="friends.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
  <nav class="topnav">
    <div class="logo">CHOL</div>
    <ul>
      <li><a href="home.php">Home</a></li>
      <li><a href="profile.php">Profile</a></li>
      <li><a class="active" href="friends.php">Friends</a></li>
      <li><a href="create_group.php">Groups</a></li>
      <li><a href="events.php">Events</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </nav>

  <main class="wrap">
    <!-- Left: Search -->
    <section class="panel search-panel">
      <h2 style="color: rgb(241, 90, 48);">Search Users</h2>
      <form class="searchbar" method="get" action="friends.php">
        <input type="text" name="q" placeholder="Search by username" value="<?= htmlspecialchars($q) ?>">
        <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
      </form>

      <div class="list">
        <?php if ($q && empty($search_results)): ?>
          <p class="muted">No users found.</p>
        <?php endif; ?>

        <?php foreach ($search_results as $u): ?>
          <div class="row">
            <div class="uname"><?= htmlspecialchars($u['Username']) ?></div>
            <form action="add_friend.php" method="post">
              <input type="hidden" name="friend_id" value="<?= (int)$u['id'] ?>">
              <button class="btn add" type="submit">Add Friend</button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Middle: Requests -->
    <section class="panel requests-panel">
      <h2 style="color: rgb(241, 90, 48);;">Requests</h2>
      <div class="scroll">
        <?php if (empty($requests)): ?>
          <p class="muted">No pending requests.</p>
        <?php else: ?>
          <?php foreach ($requests as $r): ?>
            <div class="card">
              <div class="uname large"><?= htmlspecialchars($r['Username']) ?></div>
              <div class="actions">
                <form action="respond_friend.php" method="post">
                  <input type="hidden" name="requester_id" value="<?= (int)$r['requester_id'] ?>">
                  <button class="btn accept" type="submit" name="action" value="accept">Accept</button>
                </form>
                <form action="respond_friend.php" method="post">
                  <input type="hidden" name="requester_id" value="<?= (int)$r['requester_id'] ?>">
                  <button class="btn reject" type="submit" name="action" value="decline">Reject</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>

    <!-- Right: My Friends -->
    <section class="panel friends-panel">
      <h2 style='color: rgb(241, 90, 48);'>My Friends</h2>
      <div class="scroll">
        <?php if (empty($friends)): ?>
          <p class="muted">No friends yet.</p>
        <?php else: ?>
          <?php foreach ($friends as $f): ?>
            <div class="friend-tile">
              <div class="uname"><?= htmlspecialchars($f['Username']) ?></div>
              <form action="unfriend.php" method="post">
                <input type="hidden" name="friend_id" value="<?= (int)$f['id'] ?>">
                <button class="btn tiny" type="submit">Unfriend</button>
              </form>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>
  </main>
</body>
</html>