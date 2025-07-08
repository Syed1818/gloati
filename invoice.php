<?php
require('fpdf/fpdf.php');
session_start();

if (!isset($_SESSION['user'])) exit("Not logged in");

require 'connect.php'; // ✅ Use PDO connection

$username = $_SESSION['user'];

// Fetch email
$emailStmt = $conn->prepare("SELECT email FROM users WHERE username = :username");
$emailStmt->execute([':username' => $username]);
$emailData = $emailStmt->fetch();
$email = $emailData['email'] ?? '';

// Fetch last 5 orders
$orderStmt = $conn->prepare("
    SELECT product_name, quantity, total_price 
    FROM orders 
    WHERE username = :username AND invoice_id = :invoice_id
");
$orderStmt->execute([
    ':username' => $username,
    ':invoice_id' => $invoiceId
]);
$orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($orders as $order) {
    $total += $order['total_price'];
}

// Latest used coupon and discount
$couponStmt = $conn->prepare("
    SELECT c.code, c.discount 
    FROM used_coupons u 
    JOIN coupons c ON u.coupon = c.code 
    WHERE u.username = :username 
    ORDER BY u.used_at DESC 
    LIMIT 1
");
$couponStmt->execute([':username' => $username]);
$couponData = $couponStmt->fetch();

$coupon = $couponData['code'] ?? null;
$discountPercent = $couponData['discount'] ?? 0;
$discountAmount = ($total * $discountPercent) / 100;
$finalTotal = $total - $discountAmount;

// Use invoice_id from URL
$invoiceId = $_GET['invoice_id'] ?? 'N/A';

// Start PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, "Gloati Invoice - $username", 0, 1, 'C');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 8, "Invoice ID: $invoiceId", 0, 1);
$pdf->Cell(0, 8, "Email: $email", 0, 1);
$pdf->Cell(0, 8, 'Date: ' . date("Y-m-d H:i:s"), 0, 1);
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
    $product = $order['product_name'];
    $qty = $order['quantity'];
    $price = $qty > 0 ? $order['total_price'] / $qty : 0;
    $lineTotal = $order['total_price'];

    $pdf->Cell(80, 10, $product, 1);
    $pdf->Cell(30, 10, $qty, 1);
    $pdf->Cell(40, 10, number_format($price, 2), 1);
    $pdf->Cell(40, 10, number_format($lineTotal, 2), 1);
    $pdf->Ln();
}

// Summary
$pdf->Ln(4);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, "Subtotal: Rs. " . number_format($total, 2), 0, 1);
if ($discountPercent > 0) {
    $pdf->Cell(0, 8, "Coupon ($coupon) Discount: -Rs. " . number_format($discountAmount, 2), 0, 1);
}
$pdf->Cell(0, 8, "Total: Rs. " . number_format($finalTotal, 2), 0, 1);
$pdf->Ln(6);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, "Thank you for shopping with Gloati!", 0, 1, 'C');

// Output PDF
$pdf->Output("D", "Gloati_Invoice.pdf");
?>
