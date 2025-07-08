<?php
session_start();
include 'connect.php'; // $conn is PDO

header('Content-Type: application/json');

$code = strtoupper(trim($_GET['code'] ?? ''));
$response = ['success' => false, 'message' => 'Invalid coupon'];

if ($code) {
    try {
        $stmt = $conn->prepare("SELECT discount FROM coupons WHERE code = :code AND (expires_at IS NULL OR expires_at > NOW())");
        $stmt->execute([':code' => $code]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($coupon) {
            // If user is logged in, track coupon usage
            if (isset($_SESSION['user'])) {
                $insert = $conn->prepare("INSERT INTO used_coupons (username, coupon) VALUES (:username, :coupon)");
                $insert->execute([
                    ':username' => $_SESSION['user'],
                    ':coupon' => $code
                ]);
            }

            $response = [
                'success' => true,
                'discount' => $coupon['discount']
            ];
        } else {
            $response['message'] = 'Coupon expired or not found';
        }
    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>
