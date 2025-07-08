<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gloati Admin Panel</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background-color: #f9f9f9;
    }

    header {
      background-color: #6ca59e;
      color: white;
      padding: 1rem 2rem;
      text-align: center;
      font-size: 1.5rem;
    }

    nav {
      background: #fff;
      padding: 1rem 2rem;
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 1rem;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    nav a, nav form button {
      text-decoration: none;
      color: #6ca59e;
      font-weight: bold;
      border: 2px solid #6ca59e;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      background: none;
      cursor: pointer;
      transition: all 0.3s ease;
      font-family: inherit;
    }

    nav a:hover, nav form button:hover {
      background-color: #6ca59e;
      color: white;
    }

    main {
      padding: 2rem;
      text-align: center;
    }

    .section {
      background-color: #fff;
      border-radius: 12px;
      padding: 2rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      max-width: 800px;
      margin: 2rem auto;
    }

    .section h3 {
      margin-bottom: 1rem;
      color: #333;
    }

    .section p {
      color: #666;
    }

    @media (max-width: 600px) {
      nav {
        flex-direction: column;
        align-items: center;
      }
    }
  </style>
</head>
<body>
  <header>Admin Dashboard - Gloati</header>

  <nav>
    <a href="manage-products.php">Manage Products</a>
    <a href="manage-orders.php">Manage Orders</a>
    <a href="manage-users.php">Manage Users</a>
    <a href="manage-coupons.php">Manage Coupons</a>
    <form action="adminlogout.php" method="post" style="margin: 0;">
      <button type="submit">🚪 Logout</button>
    </form>
  </nav>

  <main>
    <div class="section">
      <h3>Welcome, Admin <?= htmlspecialchars($_SESSION['admin'], ENT_QUOTES, 'UTF-8') ?>!</h3>
      <p>Use the navigation above to manage your store's products, orders, users, and discount coupons.</p>
    </div>
  </main>
</body>
</html>
