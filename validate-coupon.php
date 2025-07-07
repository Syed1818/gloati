<?php
session_start(); // Make sure session is started
include 'connect.php';

$code = strtoupper(trim($_GET['code'] ?? ''));
$response = ['success' => false, 'message' => 'Invalid coupon'];

if ($code) {
    $stmt = $conn->prepare("SELECT discount FROM coupons WHERE code = ? AND (expires_at IS NULL OR expires_at > NOW())");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $stmt->bind_result($discount);

    if ($stmt->fetch()) {
        $stmt->close();

        // Save coupon usage if user is logged in
        if (isset($_SESSION['user'])) {
            $username = $_SESSION['user'];
            $insertStmt = $conn->prepare("INSERT INTO used_coupons (username, coupon) VALUES (?, ?)");
            $insertStmt->bind_param("ss", $username, $code);
            $insertStmt->execute();
            $insertStmt->close();
        }

        $response = ['success' => true, 'discount' => $discount];
    } else {
        $response['message'] = 'Coupon expired or not found';
        $stmt->close();
    }
}

echo json_encode($response);
?>
