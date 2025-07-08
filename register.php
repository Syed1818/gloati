<?php
session_start();
include 'connect.php'; // ✅ Should use PDO

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $email    = trim($_POST["email"]);
    $password = trim($_POST["password"], PASSWORD_DEFAULT);
    $address  = trim($_POST["address"]);
    $phone    = trim($_POST["phone"]);

    // Check if username or email already exists
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
    $checkStmt->execute([':username' => $username, ':email' => $email]);
    if ($checkStmt->rowCount() > 0) {
        echo "Username or Email already exists!";
        exit();
    }

    // Insert new user
    $insertStmt = $conn->prepare("
        INSERT INTO users (username, email, password, address, phone)
        VALUES (:username, :email, :password, :address, :phone)
    ");
    $success = $insertStmt->execute([
        ':username' => $username,
        ':email'    => $email,
        ':password' => $password,
        ':address'  => $address,
        ':phone'    => $phone
    ]);

    if ($success) {
        $_SESSION['user'] = $username;
        header("Location: homepage.html");
        exit();
    } else {
        echo "Registration failed!";
    }
}
?>
