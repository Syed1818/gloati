<?php
session_start();
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    exit("Not logged in");
}

require('fpdf/fpdf.php');
include 'connect.php'; // Ensure this provides $conn (PDO)

$username = $_SESSION['user'];
$invoiceId = $_GET['invoice_id'] ?? null;

if (!$invoiceId) {
    exit("Invoice ID is required.");
}

// Fetch user email
$userStmt = $conn->prepare("SELECT email FROM users WHERE username = :username");
$userStmt->execute([':username' => $username]);
$email = $userStmt->fetchColumn();

if (!$email) {
    exit("User not found.");
}

// Fetch orders by invoice ID and username
$orderStmt = $conn->prepare("
    SELECT product_name, quantity, total_price 
    FROM orders 
    WHERE username = :username AND invoice_id = :invoice_id
");
$orderStmt->execute([':username' => $username, ':invoice_id' => $invoiceId]);
$orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);

if (!$orders) {
    exit("No matching invoice found.");
}

$total = array_sum(array_column($orders, 'total_price'));

// Fetch coupon discount (if used)
$couponStmt = $conn->prepare("
    SELECT c.code, c.discount
    FROM used_coupons u
    JOIN coupons c ON u.coupon = c.code
    WHERE u.username = :username
    ORDER BY u.used_at DESC LIMIT 1
");
$couponStmt->execute([':username' => $username]);
$couponData = $couponStmt->fetch(PDO::FETCH_ASSOC);

$coupon = $couponData['code'] ?? null;
$discountPercent = $couponData['discount'] ?? 0;
$discountAmount = ($total * $discountPercent) / 100;
$finalTotal = $total - $discountAmount;

// Generate PDF
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

// Order Rows
$pdf->SetFont('Arial', '', 11);
foreach ($orders as $order) {
    $qty = $order['quantity'];
    $price = $qty > 0 ? $order['total_price'] / $qty : 0;
    $pdf->Cell(80, 10, $order['product_name'], 1);
    $pdf->Cell(30, 10, $qty, 1);
    $pdf->Cell(40, 10, number_format($price, 2), 1);
    $pdf->Cell(40, 10, number_format($order['total_price'], 2), 1);
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

// Output
$pdf->Output("I", "Gloati_Invoice_$invoiceId.pdf");
