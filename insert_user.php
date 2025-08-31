<?php
require_once('dbconnect.php');

if (isset($_POST['fname']) AND isset($_POST['lname']) AND isset($_POST['username']) AND isset($_POST['email']) AND isset($_POST['NID']) AND isset($_POST['Birthdate']) AND isset($_POST['pass']) ){
    $u=$_POST['username'];
    $p=$_POST['pass'];
    $sql="SELECT * FROM Users WHERE Username='$u' AND Password='$p'";
    $result=mysqli_query($conn,$sql);

    if (mysqli_num_rows($result)!=0){
        header("Location:home.php");
    }
    else{
        echo "Username or Password is wrong.";
        //header("Location: index.php");

    }



}




?>