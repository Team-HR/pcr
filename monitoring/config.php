<?php
$host = "db";
$user = "admin";
$pass = "teamhrmo2019";
$dbname = "ihris";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
