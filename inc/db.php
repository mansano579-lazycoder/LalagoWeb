<?php
$servername = "localhost";
$username = "db_username";
$password = "db_password";
$dbname = "database_name";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
