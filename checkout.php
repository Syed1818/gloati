<?php
session_start();
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    exit("Not logged in");
}

require 'fpdf/fpdf.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'connect.php'; // $conn is PDO

$username = $_SESSION['user'];

// Get user info
$stmt = $conn->prepare("SELECT address, phone, id, email FROM users WHERE username = :username");
$stmt->execute([':username' => $username]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) exit("User not found");

$email = $userData['email'];
$user_id = $userData['id'];
$address = $userData['address'] ?? 'N/A';
$phone = $userData['phone'] ?? 'N/A';

// Get cart & coupon
$data = json_decode(file_get_contents("php://input"), true);
$cart = $data['cart'] ?? [];
$coupon = $data['coupon'] ?? null;

$totalAmount = 0;
$discount = 0;
$invoiceId = "INV" . str_pad(mt_rand(1, 999999), 6, "0", STR_PAD_LEFT);

// Start PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, "Gloati Invoice - $username", 0, 1, 'C');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 8, "Invoice ID: $invoiceId", 0, 1);
$pdf->Cell(0, 8, "Email: $email", 0, 1);
$pdf->Cell(0, 8, "Address: $address", 0, 1);
$pdf->Cell(0, 8, "Phone: $phone", 0, 1);
$pdf->Cell(0, 8, "Date: " . date("Y-m-d H:i:s"), 0, 1);
$pdf->Cell(0, 8, "Support: support@gloati.com | +91-88765XXXXX", 0, 1);
$pdf->Ln(5);

// Table header
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(80, 10, 'Product', 1);
$pdf->Cell(30, 10, 'Qty', 1);
$pdf->Cell(40, 10, 'Price (Rs.)', 1);
$pdf->Cell(40, 10, 'Total (Rs.)', 1);
$pdf->Ln();

// Insert orders and populate PDF
$pdf->SetFont('Arial', '', 11);
foreach ($cart as $item) {
    $name = $item['name'] ?? '';
    $qty = (int)($item['qty'] ?? 0);
    $price = (float)($item['price'] ?? 0);
    $itemTotal = $price * $qty;
    $totalAmount += $itemTotal;

    // Save to DB
    $orderStmt = $conn->prepare("INSERT INTO orders (user_id, username, invoice_id, product_name, quantity, total_price) 
                                VALUES (:user_id, :username, :invoice_id, :product_name, :quantity, :total_price)");
    $orderStmt->execute([
        ':user_id' => $user_id,
        ':username' => $username,
        ':invoice_id' => $invoiceId,
        ':product_name' => $name,
        ':quantity' => $qty,
        ':total_price' => $itemTotal
    ]);

    // Add to PDF
    $pdf->Cell(80, 10, $name, 1);
    $pdf->Cell(30, 10, $qty, 1);
    $pdf->Cell(40, 10, number_format($price, 2), 1);
    $pdf->Cell(40, 10, number_format($itemTotal, 2), 1);
    $pdf->Ln();
}

// Handle coupon
if ($coupon) {
    $couponStmt = $conn->prepare("SELECT discount FROM coupons WHERE code = :code");
    $couponStmt->execute([':code' => $coupon]);
    if ($row = $couponStmt->fetch(PDO::FETCH_ASSOC)) {
        $discount = $row['discount'];

        // Save coupon usage (check for duplicates)
        try {
            $usageStmt = $conn->prepare("INSERT INTO used_coupons (username, coupon) VALUES (:username, :coupon)");
            $usageStmt->execute([':username' => $username, ':coupon' => $coupon]);
        } catch (PDOException $e) {
            // Optional: silently ignore if already used
        }
    }
}

$discountAmount = ($totalAmount * $discount) / 100;
$finalTotal = $totalAmount - $discountAmount;

// Summary in PDF
$pdf->Ln(4);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, "Subtotal: Rs. " . number_format($totalAmount, 2), 0, 1);
if ($discount > 0) {
    $pdf->Cell(0, 8, "Coupon ($coupon) Discount: -Rs. " . number_format($discountAmount, 2), 0, 1);
}
$pdf->Cell(0, 8, "Total: Rs. " . number_format($finalTotal, 2), 0, 1);
$pdf->Ln(6);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, "Thank you for shopping with Gloati!", 0, 1, 'C');

// Save invoice
if (!is_dir('invoices')) {
    mkdir('invoices', 0777, true);
}
$pdfPath = __DIR__ . "/invoices/{$invoiceId}.pdf";
$pdf->Output('F', $pdfPath);

// Email the PDF
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'jabeen9945425979@gmail.com';
    $mail->Password = 'hglq frbr uezf aemc'; // App password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('jabeen9945425979@gmail.com', 'Gloati');
    $mail->addAddress($email, $username);
    $mail->Subject = "Your Gloati Invoice";
    $mail->Body = "Hi $username,\n\nThank you for shopping with Gloati. Your invoice is attached.\n\nRegards,\nGloati Team";
    $mail->addAttachment($pdfPath);

    $mail->send();
    echo "Checkout successful! Invoice sent to $email";
} catch (Exception $e) {
    echo "Checkout successful, but email failed: {$mail->ErrorInfo}";
}
?>
