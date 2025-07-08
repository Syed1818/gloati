<?php
session_start();
include 'connect.php'; // Make sure this defines $conn using PDO

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Use prepared statements securely with PDO
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
    $stmt->execute([
        ':username' => $username,
        ':password' => $password
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user'] = $user['username'];
        header("Location: homepage.html");
        exit();
    } else {
        header("Location: login.html?error=1");
        exit();
    }
}
?>

