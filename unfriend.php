<?php
require_once "auth.php";
require_once "dbconnect.php";

$me        = $_SESSION['user_id'];
$friend_id = isset($_POST['friend_id']) ? (int)$_POST['friend_id'] : 0;

if ($friend_id <= 0) { header("Location: friends.php"); exit; }

$sql = "DELETE FROM Friends
        WHERE (user_id=? AND friend_id=?)
           OR (user_id=? AND friend_id=?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $me, $friend_id, $friend_id, $me);
$stmt->execute(); $stmt->close();

$conn->close();
header("Location: friends.php");
exit;
