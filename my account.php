<?php
session_start();
include 'connect.php'; // defines $conn as PDO

if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

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

<!DOCTYPE html>
<html>
<head>
  <title>My Account - Gloati</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #fffaf7;
      padding: 2rem;
    }
    h2 {
      color: #6ca59e;
    }
    .info, .orders {
      background: #fff;
      padding: 1.5rem;
      border-radius: 12px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.06);
      margin-bottom: 2rem;
    }
    .profile-header {
      display: flex;
      align-items: center;
      margin-bottom: 1rem;
    }
    .profile-header img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 1rem;
      border: 2px solid #6ca59e44;
    }
    .profile-header h3 {
      margin: 0;
      font-size: 1.5rem;
    }
    .edit-btn {
      margin-left: auto;
      padding: 0.5rem 1rem;
      background: #6ca59e;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      text-decoration: none;
    }
    .edit-btn:hover {
      background: #55877f;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
    }
    table, th, td {
      border: 1px solid #eee;
    }
    th, td {
      padding: 0.75rem;
      text-align: left;
    }
    th {
      background-color: #f1f1f1;
    }
    a {
      color: #6ca59e;
      text-decoration: none;
      margin-right: 1rem;
    }
  </style>
</head>
<body>
  <h2>My Account</h2>
  <div class="info">
    <div class="profile-header">
      <img src="uploads/<?= htmlspecialchars($profileImage) ?>" alt="Profile Picture" />
      <div>
        <h3><?= htmlspecialchars($username) ?></h3>
        <p><strong>📍 Address:</strong> <?= htmlspecialchars($address) ?></p>
        <p><strong>📞 Phone:</strong> <?= htmlspecialchars($phone) ?></p>
        <p><strong>📧 Email:</strong> <?= htmlspecialchars($email) ?></p>
      </div>
      <a href="edit-profile.php" class="edit-btn">Edit Profile</a>
    </div>
    <a href="shop.php">🛍️ Shop</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <div class="orders">
    <h3>Order History</h3>
    <h4>Filter Orders</h4>
    <form method="GET" style="margin-bottom: 1rem;">
      <label>
        Product:
        <input type="text" name="product_name" value="<?= htmlspecialchars($productFilter) ?>" />
      </label>
      <label>
        From:
        <input type="date" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>" />
      </label>
      <label>
        To:
        <input type="date" name="date_to" value="<?= htmlspecialchars($dateTo) ?>" />
      </label>
      <button type="submit">Filter</button>
      <a href="my account.php" style="margin-left: 10px;">Reset</a>
    </form>

    <?php if (count($orders) > 0): ?>
      <table>
        <tr>
          <th>Product</th>
          <th>Quantity</th>
          <th>Total Price (₹)</th>
          <th>Date</th>
          <th>Invoice ID</th>
          <th>Download</th>
        </tr>
        <?php foreach ($orders as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= number_format($row['total_price'], 2) ?></td>
            <td><?= $row['ordered_at'] ?></td>
            <td><?= htmlspecialchars($row['invoice_id']) ?></td>
            <td><a href="invoice.php?invoice_id=<?= urlencode($row['invoice_id']) ?>" target="_blank">🧾 Download</a></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php else: ?>
      <p>No orders found.</p>
    <?php endif; ?>
  </div>
</body>
</html>
