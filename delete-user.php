<?php
session_start();
include 'connect.php';

if ($_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

$id = intval($_POST['id']);
if ($id == $_SESSION['id']) {
    die("You cannot delete your own account.");
}

mysqli_query($conn, "DELETE FROM users WHERE id = $id");
header("Location: manage-users.php");
