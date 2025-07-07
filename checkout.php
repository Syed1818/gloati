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

// DB connection
$conn = new mysqli('127.0.0.1', 'myadmin', 'syedshahid@123', 'Gloati_users');
if ($conn->connect_error) {
    die("Database connection failed.");
}

$username = $_SESSION['user'];

// Get user email
$userResult = $conn->query("SELECT address,phone,id,email FROM users WHERE username = '$username'");
if ($userResult->num_rows === 0) exit("User not found");
$userData = $userResult->fetch_assoc();
$email = $userData['email'];
$user_id = $userData['id'];
$address = $userData['address'];
$phone = $userData['phone'];

// Get cart + coupon
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

// Table body
$pdf->SetFont('Arial', '', 11);
foreach ($cart as $item) {
    $name = $item['name'] ?? '';
    $qty = $item['qty'] ?? 0;
    $price = $item['price'] ?? 0;
    $itemTotal = $price * $qty;
    $totalAmount += $itemTotal;

    // Store order in DB
    $stmt = $conn->prepare("INSERT INTO orders (user_id, username, invoice_id, product_name, quantity, total_price) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssii", $user_id, $username, $invoiceId, $name, $qty, $itemTotal);
    $stmt->execute();
    $stmt->close();

    // Add to PDF
    $pdf->Cell(80, 10, $name, 1);
    $pdf->Cell(30, 10, $qty, 1);
    $pdf->Cell(40, 10, number_format($price, 2), 1);
    $pdf->Cell(40, 10, number_format($itemTotal, 2), 1);
    $pdf->Ln();
}

// Coupon handling
if ($coupon) {
    $couponStmt = $conn->prepare("SELECT discount FROM coupons WHERE code = ?");
    $couponStmt->bind_param("s", $coupon);
    $couponStmt->execute();
    $couponResult = $couponStmt->get_result();
    if ($couponResult->num_rows > 0) {
        $discount = $couponResult->fetch_assoc()['discount'];
        // Save usage history (optional)
        $conn->query("INSERT INTO used_coupons (username, coupon) VALUES ('$username', '$coupon')");
    }
    $couponStmt->close();
}

$discountAmount = ($totalAmount * $discount) / 100;
$finalTotal = $totalAmount - $discountAmount;

// Summary section
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

// Save PDF
if (!file_exists(__DIR__ . '/invoices')) {
    mkdir(__DIR__ . '/invoices', 0777, true);
}

// File path using invoice ID
$pdfPath = __DIR__ . "/invoices/{$invoiceId}.pdf";
$pdf->Output('F', $pdfPath);

// Email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'jabeen9945425979@gmail.com';
    $mail->Password = 'hglq frbr uezf aemc';
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
