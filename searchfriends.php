<?php
session_start();
$conn = new mysqli("localhost", "root", "", "chol");

// Current logged-in user (assume you stored it in session)
$current_user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search = $_POST['search'];
    $sql = "SELECT id, username FROM user WHERE username LIKE ? AND id != ?";
    $stmt = $conn->prepare($sql);
    $like = "%" . $search . "%";
    $stmt->bind_param("si", $like, $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo $row['username'] . " 
        <form action='add_friend.php' method='POST' style='display:inline;'>
            <input type='hidden' name='friend_id' value='" . $row['id'] . "'>
            <button type='submit'>Add Friend</button>
        </form><br>";
    }
}
?>
