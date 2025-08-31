<?php
$servername='localhost';
$username='root';
$password='';
$dbname='chol';

$conn= new mysqli($servername,$username,$password);

if ($conn-> connect_error){
    die (" Connection failed". $conn->connect_error);
}
else{
    echo "Connection Successful";
    mysqli_select_db($conn,$dbname);
}


?>