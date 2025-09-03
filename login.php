<?php
require_once('dbconnect.php');
session_start();

if (isset($_POST['username']) && isset($_POST['pass'])) {
    $u = $_POST['username'];
    $p = $_POST['pass'];

    $sql = "SELECT id, Username FROM User WHERE Username='$u' AND Password='$p'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Save login info in session
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['Username'];

        header("Location: home.php");
        exit;
    } else {
        echo "Username or Password is wrong.";
    }
}
