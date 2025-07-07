<?php
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $address = mysqli_real_escape_string($conn, $_POST["address"]);
    $phone = mysqli_real_escape_string($conn, $_POST["phone"]);

    // Check for duplicate username/email
    $check = $conn->query("SELECT * FROM users WHERE username = '$username' OR email = '$email'");
    if ($check->num_rows > 0) {
        echo "Username or Email already exists!";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, address, phone) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $email, $password, $address, $phone);

    if ($stmt->execute()) {
        $_SESSION['user'] = $username;
        header("Location: homepage.html");
    } else {
        echo "Registration failed!";
    }

    $stmt->close();
    $conn->close();
}
?>
