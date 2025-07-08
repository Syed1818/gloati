<?php
session_start();
include 'connect.php'; // defines $conn as PDO

if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $user_name = $_SESSION['user'];
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    if ($rating < 1 || $rating > 5 || empty($comment)) {
        die("Invalid review input.");
    }

    try {
        $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_name, rating, comment) 
                                VALUES (:product_id, :user_name, :rating, :comment)");
        $stmt->execute([
            ':product_id' => $product_id,
            ':user_name' => $user_name,
            ':rating' => $rating,
            ':comment' => $comment
        ]);
    } catch (PDOException $e) {
        die("Error saving review: " . $e->getMessage());
    }

    header("Location: product.php?id=$product_id#reviews");
    exit();
}
?>
