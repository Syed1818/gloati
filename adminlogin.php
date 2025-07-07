<?php
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Only check in the admins table
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin) {
        // Use a dedicated admin session key
        $_SESSION['admin'] = $admin['username'];

        header("Location: adminpanel.php");
        exit();
    } else {
        header("Location: adminlogin.html?error=1");
        exit();
    }
}
?>
