<?php
require('fpdf/fpdf.php');
session_start();
if (!isset($_SESSION['user'])) exit();

$username = $_SESSION['user'];

// Connect DB
$conn = new mysqli("127.0.0.1", "myadmin", "syedshahid@123", "Gloati_users");
if ($conn->connect_error) exit("DB Connection Failed");



// Fetch email
$emailQuery = $conn->prepare("SELECT email FROM users WHERE username = ?");
$emailQuery->bind_param("s", $username);
$emailQuery->execute();
$emailResult = $emailQuery->get_result();
$email = $emailResult->fetch_assoc()['email'] ?? '';

// Fetch last 5 orders
$stmt = $conn->prepare("SELECT product_name, quantity, total_price FROM orders WHERE username = ? ORDER BY ordered_at DESC LIMIT 5");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();

$orders = [];
$total = 0;
while ($row = $res->fetch_assoc()) {
    $orders[] = $row;
    $total += $row['total_price'];
}

// Try to get latest used coupon (fixing subquery limitation)
$couponQuery = $conn->prepare("
    SELECT c.code, c.discount FROM used_coupons u 
    JOIN coupons c ON u.coupon = c.code 
    WHERE u.username = ? 
    ORDER BY u.used_at DESC LIMIT 1
");
$couponQuery->bind_param("s", $username);
$couponQuery->execute();
$couponResult = $couponQuery->get_result();

$coupon = null;
$discountPercent = 0;
if ($couponResult->num_rows > 0) {
    $couponData = $couponResult->fetch_assoc();
    $coupon = $couponData['code'];
    $discountPercent = $couponData['discount'];
}

$discountAmount = ($total * $discountPercent / 100);
$finalTotal = $total - $discountAmount;

$invoiceId = $_GET['invoice_id'] ?? '';
$invoicePath = __DIR__ . "/invoices/{$invoiceId}.pdf";

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

// Table Data
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

// Output PDF
$pdf->Output("D", "Gloati_Invoice.pdf");
?>
