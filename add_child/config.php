<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "child_management";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB Connection Failed");
}
?>
