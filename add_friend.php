<?php
require_once "auth.php";
require_once "dbconnect.php";

$me        = $_SESSION['user_id'];
$friend_id = isset($_POST['friend_id']) ? (int)$_POST['friend_id'] : 0;

if ($friend_id <= 0 || $friend_id == $me) {
  header("Location: friends.php"); exit;
}

/* If they already sent me a pending request, auto-accept */
$sql = "SELECT status, requested_by FROM Friends WHERE user_id=? AND friend_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $friend_id, $me);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($row && $row['status']==='pending' && (int)$row['requested_by']===$friend_id) {
  $conn->begin_transaction();
  // accept their pending
  $sql = "UPDATE Friends SET status='accepted' WHERE user_id=? AND friend_id=?";
  $u = $conn->prepare($sql); $u->bind_param("ii", $friend_id, $me); $u->execute(); $u->close();
  // insert my reverse accepted
  $sql = "INSERT IGNORE INTO Friends (user_id, friend_id, status, requested_by)
          VALUES (?, ?, 'accepted', ?)";
  $i = $conn->prepare($sql); $i->bind_param("iii", $me, $friend_id, $friend_id); $i->execute(); $i->close();
  $conn->commit();
  header("Location: friends.php"); exit;
}

/* Otherwise send new pending (ignore duplicates) */
$sql = "INSERT IGNORE INTO Friends (user_id, friend_id, status, requested_by)
        VALUES (?, ?, 'pending', ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $me, $friend_id, $me);
$stmt->execute();
$stmt->close();

$conn->close();
header("Location: friends.php");
exit;
