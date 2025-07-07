<?php
$host = '127.0.0.1'; // Database host (localhost in most cases)
$db = 'Gloati_users'; // Your database name
$user = 'myadmin'; // MySQL username
$pass = 'syedshahid@123'; // MySQL password

// Create a MySQLi connection
$conn = new mysqli($host, $user, $pass, $db);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>