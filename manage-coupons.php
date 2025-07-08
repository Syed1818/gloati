<?php
session_start();
include 'connect.php'; // $conn is a PDO instance

// Handle coupon addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_coupon'])) {
    $code = trim($_POST['code']);
    $discount = (float) $_POST['discount'];
    $expires_at = $_POST['expires_at'];

    $stmt = $conn->prepare("INSERT INTO coupons (code, discount, expires_at) VALUES (:code, :discount, :expires_at)");
    $stmt->execute([
        ':code' => $code,
        ':discount' => $discount,
        ':expires_at' => $expires_at
    ]);
    header("Location: manage-coupons.php");
    exit();
}

// Handle coupon deletion
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM coupons WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header("Location: manage-coupons.php");
    exit();
}

// Fetch all coupons
$stmt = $conn->query("SELECT * FROM coupons ORDER BY id DESC");
$coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Coupons - Gloati</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f3f3;
            margin: 0;
            padding: 0;
        }
        header {
            background: #6ca59e;
            color: white;
            padding: 1rem 2rem;
        }
        .container {
            max-width: 900px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
        }
        h2 {
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 0.7rem;
            text-align: left;
        }
        th {
            background: #eaeaea;
        }
        form input, form button {
            padding: 0.5rem;
            margin-right: 0.5rem;
        }
        .delete-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .delete-btn:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
    <header>
        <h1>Admin Panel - Manage Coupons</h1>
    </header>

    <div class="container">
        <h2>Coupon List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Discount (%)</th>
                <th>Expires At</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($coupons as $row): ?>
            <tr>
                <td>#<?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['code']) ?></td>
                <td><?= $row['discount'] ?>%</td>
                <td><?= $row['expires_at'] ?></td>
                <td>
                    <a class="delete-btn" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this coupon?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h2>Add Coupon</h2>
        <form method="post">
            <input type="text" name="code" placeholder="Coupon Code" required />
            <input type="number" name="discount" placeholder="Discount %" step="0.01" required />
            <input type="date" name="expires_at" required />
            <button type="submit" name="add_coupon">Add</button>
        </form>
    </div>
</body>
</html>
