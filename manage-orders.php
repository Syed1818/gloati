<?php
session_start();
include 'connect.php'; // Assumes $conn is a PDO connection

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filtering
$filter = "";
$params = [];

if (!empty($_GET['username'])) {
    $filter = "WHERE users.username ILIKE :username";
    $params[':username'] = '%' . $_GET['username'] . '%';
}

$sql = "
    SELECT orders.*, users.username, users.email, users.phone
    FROM orders
    LEFT JOIN users ON orders.user_id = users.id
    $filter
    ORDER BY ordered_at DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $conn->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total orders
$countSql = "
    SELECT COUNT(*) as total 
    FROM orders
    LEFT JOIN users ON orders.user_id = users.id
    $filter
";
$countStmt = $conn->prepare($countSql);
foreach ($params as $key => $value) {
    $countStmt->bindValue($key, $value);
}
$countStmt->execute();
$totalOrders = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalOrders / $limit);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Manage Orders - Gloati</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      padding: 0;
    }
    header {
      background-color: #6ca59e;
      color: white;
      padding: 1rem 2rem;
      font-size: 1.5rem;
    }
    .container {
      max-width: 1000px;
      margin: 2rem auto;
      background: #fff;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
    }
    th, td {
      padding: 0.75rem;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }
    th {
      background-color: #f0f0f0;
    }
    .pagination {
      margin-top: 20px;
      text-align: center;
    }
    .pagination a {
      padding: 6px 12px;
      margin: 0 3px;
      background: #6ca59e;
      color: white;
      border-radius: 4px;
      text-decoration: none;
    }
    .pagination a.active {
      background: #55877f;
    }
    .filter-box {
      margin-bottom: 1rem;
    }
  </style>
</head>
<body>
  <header>Admin Panel - Manage Orders</header>
  <div class="container">
    <h2>Order List</h2>

    <form method="GET" class="filter-box">
      <input type="text" name="username" placeholder="Filter by username" value="<?= htmlspecialchars($_GET['username'] ?? '') ?>">
      <button type="submit">Filter</button>
    </form>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Customer</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Date</th>
          <th>Total</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($orders) > 0): ?>
          <?php foreach ($orders as $order): ?>
            <tr>
              <td>#<?= htmlspecialchars($order['id']) ?></td>
              <td><?= htmlspecialchars($order['username'] ?? 'Guest') ?></td>
              <td><?= htmlspecialchars($order['email'] ?? '-') ?></td>
              <td><?= htmlspecialchars($order['phone'] ?? '-') ?></td>
              <td><?= htmlspecialchars($order['ordered_at']) ?></td>
              <td>₹<?= number_format($order['total_price'], 2) ?></td>
              <td><a href="order-items.php?id=<?= $order['id'] ?>">View Items</a></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7">No orders found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <div class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>&username=<?= urlencode($_GET['username'] ?? '') ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </div>
</body>
</html>
