<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = htmlspecialchars(trim($_POST['name']));
    $email   = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    // You can send email or store in DB here
    // Example response:
    echo "<script>alert('Thank you, $name! Your message has been received.'); window.location.href='contact.php';</script>";
} else {
    header("Location: contact.php");
    exit();
}
