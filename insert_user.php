<?php
session_start();
require_once('dbconnect.php');

if (isset($_POST['fname'], $_POST['lname'], $_POST['username'], $_POST['email'], $_POST['NID'], $_POST['Birthdate'], $_POST['pass'])) {
    $f = $_POST['fname'];
    $l = $_POST['lname'];
    $u = $_POST['username'];
    $e = $_POST['email'];
    $b = $_POST['Birthdate'];
    $n = $_POST['NID'];
    $p = $_POST['pass'];

    $sql = "INSERT INTO User (First_name, Last_name, Username, Password, Email, NID, Birthdate) 
            VALUES ('$f', '$l', '$u', '$p', '$e', '$n', '$b')";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        echo "Inserted successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
