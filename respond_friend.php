<?php
require_once "auth.php";
require_once "dbconnect.php";

$me      = $_SESSION['user_id'];
$from_id = isset($_POST['requester_id']) ? (int)$_POST['requester_id'] : 0;
$action  = $_POST['action'] ?? '';

if ($from_id <= 0) { header("Location: friends.php"); exit; }

if ($action === 'accept') {
  $conn->begin_transaction();
  // 1) mark their pending as accepted
  $sql = "UPDATE Friends SET status='accepted' WHERE user_id=? AND friend_id=? AND status='pending'";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $from_id, $me);
  $stmt->execute(); $stmt->close();

  // 2) insert my reverse accepted
  $sql = "INSERT IGNORE INTO Friends (user_id, friend_id, status, requested_by)
          VALUES (?, ?, 'accepted', ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("iii", $me, $from_id, $from_id); 
  $stmt->execute(); $stmt->close();

  $conn->commit();
} else {
  // decline
  $sql = "DELETE FROM Friends WHERE user_id=? AND friend_id=? AND status='pending'";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $from_id, $me);
  $stmt->execute(); $stmt->close();
}

$conn->close();
header("Location: friends.php");
exit;
