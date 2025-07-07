<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $user_name = mysqli_real_escape_string($conn, $_SESSION['user']);
    $rating = intval($_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);

    if ($rating < 1 || $rating > 5 || empty($comment)) {
        die("Invalid review input.");
    }

    $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_name, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $product_id, $user_name, $rating, $comment);
    $stmt->execute();
    $stmt->close();

    header("Location: product.php?id=$product_id#reviews");
    exit();
}
?>
