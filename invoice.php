<?php
require('fpdf/fpdf.php');
session_start();

if (!isset($_SESSION['user'])) exit("Not logged in");

$username = $_SESSION['user'];

// Connect to DB
$conn = new mysqli("127.0.0.1", "myadmin", "syedshahid@123", "Gloati_users");
if ($conn->connect_error) exit("DB connection failed");

$invoiceId = $_GET['invoice_id'] ?? '';
if (empty($invoiceId)) exit("Invoice ID missing");

// Get email
$emailStmt = $conn->prepare("SELECT email FROM users WHERE username = ?");
$emailStmt->bind_param("s", $username);
$emailStmt->execute();
$emailRes = $emailStmt->get_result();
$email = $emailRes->fetch_assoc()['email'] ?? '';

// Get orders for this invoice
$orderStmt = $conn->prepare("SELECT product_name, quantity, total_price, ordered_at FROM orders WHERE username = ? AND invoice_id = ?");
$orderStmt->bind_param("ss", $username, $invoiceId);
$orderStmt->execute();
$orderRes = $orderStmt->get_result();

$orders = [];
$total = 0;
$orderTime = '';
while ($row = $orderRes->fetch_assoc()) {
    $orders[] = $row;
    $total += $row['total_price'];
    $orderTime = $row['ordered_at']; // Get timestamp for coupon check
}
if (empty($orders)) exit("No orders found for this invoice.");

// Get most recent coupon used before/at order time
$couponStmt = $conn->prepare("
    SELECT c.code, c.discount 
    FROM used_coupons u 
    JOIN coupons c ON u.coupon = c.code 
    WHERE u.username = ? AND u.used_at <= ?
    ORDER BY u.used_at DESC LIMIT 1
");
$couponStmt->bind_param("ss", $username, $orderTime);
$couponStmt->execute();
$couponRes = $couponStmt->get_result();

$coupon = null;
$discountPercent = 0;
if ($couponRes->num_rows > 0) {
    $couponData = $couponRes->fetch_assoc();
    $coupon = $couponData['code'];
    $discountPercent = $couponData['discount'];
}

$discountAmount = ($total * $discountPercent / 100);
$finalTotal = $total - $discountAmount;

// Generate PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, "Gloati Invoice - $username", 0, 1, 'C');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 8, "Invoice ID: $invoiceId", 0, 1);
$pdf->Cell(0, 8, "Email: $email", 0, 1);
$pdf->Cell(0, 8, 'Date: ' . date("Y-m-d H:i:s", strtotime($orderTime)), 0, 1);
$pdf->Cell(0, 8, 'Support: support@gloati.com | +91-88765XXXXX', 0, 1);
$pdf->Ln(5);

// Table Header
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(80, 10, 'Product', 1);
$pdf->Cell(30, 10, 'Qty', 1);
$pdf->Cell(40, 10, 'Price (Rs.)', 1);
$pdf->Cell(40, 10, 'Total (Rs.)', 1);
$pdf->Ln();

// Table Rows
$pdf->SetFont('Arial', '', 11);
foreach ($orders as $order) {
    $price = $order['quantity'] > 0 ? $order['total_price'] / $order['quantity'] : 0;
    $pdf->Cell(80, 10, $order['product_name'], 1);
    $pdf->Cell(30, 10, $order['quantity'], 1);
    $pdf->Cell(40, 10, number_format($price, 2), 1);
    $pdf->Cell(40, 10, number_format($order['total_price'], 2), 1);
    $pdf->Ln();
}

// Totals
$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, "Subtotal: Rs. " . number_format($total, 2), 0, 1);
if ($discountPercent > 0) {
    $pdf->Cell(0, 8, "Coupon ($coupon) Discount: -Rs. " . number_format($discountAmount, 2), 0, 1);
}
$pdf->Cell(0, 8, "Total: Rs. " . number_format($finalTotal, 2), 0, 1);
$pdf->Ln(6);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, "Thank you for shopping with Gloati!", 0, 1, 'C');

// Output to browser
$pdf->Output("D", "Gloati_Invoice_{$invoiceId}.pdf");
?>

