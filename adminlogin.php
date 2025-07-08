<?php
session_start();
include 'connect.php'; // Ensure $conn is a PDO connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = :username AND password = :password");
    $stmt->execute([
        ':username' => $username,
        ':password' => $password
    ]);

    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        $_SESSION['admin'] = $admin['username'];
        header("Location: adminpanel.php");
        exit();
    } else {
        header("Location: adminlogin.html?error=1");
        exit();
    }
}
?>
