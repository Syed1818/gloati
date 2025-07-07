<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

$host = '127.0.0.1';
$db = 'Gloati_users';
$user = 'myadmin';
$pass = 'syedshahid@123';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Database connection failed.");
}

$username = $_SESSION['user'];
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
$productFilter = $_GET['product_name'] ?? '';

// Base query
$query = "SELECT product_name, quantity, total_price, ordered_at, invoice_id FROM orders WHERE username = ?";
$params = [$username];
$types = "s";

// Apply filters
if (!empty($productFilter)) {
    $query .= " AND product_name LIKE ?";
    $params[] = "%$productFilter%";
    $types .= "s";
}
if (!empty($dateFrom)) {
    $query .= " AND ordered_at >= ?";
    $params[] = $dateFrom . " 00:00:00";
    $types .= "s";
}
if (!empty($dateTo)) {
    $query .= " AND ordered_at <= ?";
    $params[] = $dateTo . " 23:59:59";
    $types .= "s";
}
$query .= " ORDER BY ordered_at DESC";

// Prepare and execute query
$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Fetch profile image
$profileQuery = $conn->prepare("SELECT profile_image, address,email, phone FROM users WHERE username = ?");
$profileQuery->bind_param("s", $username,);
$profileQuery->execute();
$profileResult = $profileQuery->get_result();
$profile = $profileResult->fetch_assoc();
$profileImage = $profile && $profile['profile_image'] ? $profile['profile_image'] : 'default-avatar.png';
$address = $profile['address'] ?? 'Not Provided';
$phone = $profile['phone'] ?? 'Not Provided';
$email = $profile['email'];
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
      <img src="uploads/<?php echo htmlspecialchars($profileImage); ?>" alt="Profile Picture" />
      <div>
        <h3><?php echo htmlspecialchars($username); ?></h3>
        <p><strong>📍 Address:</strong> <?php echo htmlspecialchars($address); ?></p>
        <p><strong>📞 Phone:</strong> <?php echo htmlspecialchars($phone); ?></p>
        <p><strong>📧 Email:</strong> <?php echo htmlspecialchars($email); ?></p>
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
        <input type="text" name="product_name" value="<?php echo htmlspecialchars($productFilter); ?>" />
      </label>
      <label>
        From:
        <input type="date" name="date_from" value="<?php echo htmlspecialchars($dateFrom); ?>" />
      </label>
      <label>
        To:
        <input type="date" name="date_to" value="<?php echo htmlspecialchars($dateTo); ?>" />
      </label>
      <button type="submit">Filter</button>
      <a href="my account.php" style="margin-left: 10px;">Reset</a>
    </form>

    <?php if ($result->num_rows > 0): ?>
      <table>
        <tr>
          <th>Product</th>
          <th>Quantity</th>
          <th>Total Price (₹)</th>
          <th>Date</th>
          <th>Invoice ID</th>
          <th>Download</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
            <td><?php echo $row['quantity']; ?></td>
            <td><?php echo $row['total_price']; ?></td>
            <td><?php echo $row['ordered_at']; ?></td>
            <td><?php echo htmlspecialchars($row['invoice_id']); ?></td>
            <td>
              <a href="invoice.php?invoice_id=<?php echo urlencode($row['invoice_id']); ?>" target="_blank">🧾 Download</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <p>No orders found.</p>
    <?php endif; ?>
  </div>
</body>
</html>
