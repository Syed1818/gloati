<?php
session_start();
include 'connect.php';

// Delete product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM products WHERE id = $id");
    header("Location: manage-products.php");
    exit();
}

// Add new product
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $img = $_POST['image'];

    $stmt = $conn->prepare("INSERT INTO products (name, price, image) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $name, $price, $img);
    $stmt->execute();
}

$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Products - Admin</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f7f9fb;
      padding: 2rem;
      margin: 0;
    }

    h2, h3 {
      color: #333;
    }

    .product-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1.5rem;
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .product-table th, .product-table td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    .product-table th {
      background-color: #6ca59e;
      color: white;
      font-weight: normal;
    }

    .product-table tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .product-table img {
      width: 70px;
      height: auto;
      border-radius: 6px;
    }

    .btn {
      padding: 0.5rem 1rem;
      background: #f44336;
      color: white;
      border: none;
      border-radius: 6px;
      text-decoration: none;
      font-size: 0.9rem;
      cursor: pointer;
    }

    .btn:hover {
      background: #d32f2f;
    }

    .form-container {
      margin-top: 3rem;
      max-width: 500px;
      background: #fff;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.05);
    }

    input[type="text"], input[type="number"] {
      width: 100%;
      padding: 0.75rem;
      margin-bottom: 1rem;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
    }

    input[type="submit"] {
      width: 100%;
      padding: 0.75rem;
      background: #6ca59e;
      border: none;
      color: white;
      font-size: 1rem;
      border-radius: 8px;
      cursor: pointer;
    }

    input[type="submit"]:hover {
      background: #55877f;
    }

    @media (max-width: 768px) {
      .product-table th, .product-table td {
        font-size: 0.9rem;
        padding: 0.7rem;
      }

      .form-container {
        padding: 1rem;
      }
    }
  </style>
</head>
<body>

<h2>🛠️ Manage Products</h2>

<table class="product-table">
  <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Price (₹)</th>
    <th>Image</th>
    <th>Actions</th>
  </tr>
  <?php while ($row = $products->fetch_assoc()): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td>₹<?= number_format($row['price'], 2) ?></td>
      <td><img src="<?= htmlspecialchars($row['image']) ?>" alt="Product Image" /></td>
      <td>
        <a class="btn" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure to delete this product?')">Delete</a>
      </td>
    </tr>
  <?php endwhile; ?>
</table>

<div class="form-container">
  <h3>➕ Add New Product</h3>
  <form method="POST">
    <input type="text" name="name" placeholder="Product Name" required>
    <input type="number" name="price" step="0.01" placeholder="Price (e.g. 149.99)" required>
    <input type="text" name="image" placeholder="Image URL (e.g. uploads/product.jpg)" required>
    <input type="submit" value="Add Product">
  </form>
</div>

</body>
</html>
