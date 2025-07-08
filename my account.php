<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'connect.php'; // defines $conn as PDO

if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}
echo "Starting page...<br>";

$username = $_SESSION['user'];
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
$productFilter = $_GET['product_name'] ?? '';

// Build dynamic query
$query = "SELECT product_name, quantity, total_price, ordered_at, invoice_id 
          FROM orders 
          WHERE username = :username";
$params = [':username' => $username];

if (!empty($productFilter)) {
    $query .= " AND product_name ILIKE :product_name";
    $params[':product_name'] = '%' . $productFilter . '%';
}
if (!empty($dateFrom)) {
    $query .= " AND ordered_at >= :date_from";
    $params[':date_from'] = $dateFrom . " 00:00:00";
}
if (!empty($dateTo)) {
    $query .= " AND ordered_at <= :date_to";
    $params[':date_to'] = $dateTo . " 23:59:59";
}
$query .= " ORDER BY ordered_at DESC";

// Execute order query
$stmt = $conn->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch profile info
$profileStmt = $conn->prepare("SELECT profile_image, address, email, phone FROM users WHERE username = :username");
$profileStmt->execute([':username' => $username]);
$profile = $profileStmt->fetch(PDO::FETCH_ASSOC);

$profileImage = $profile['profile_image'] ?? 'default-avatar.png';
$address = $profile['address'] ?? 'Not Provided';
$phone = $profile['phone'] ?? 'Not Provided';
$email = $profile['email'] ?? 'Not Provided';
?>
