<?php
session_start();
include 'connect.php'; // $conn is a PDO instance

// CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=users.csv');
    $output = fopen("php://output", "w");
    fputcsv($output, ['ID', 'Username', 'Email', 'Role', 'Created At']);

    $stmt = $conn->query("SELECT id, username, email, role, created_at FROM users");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}

// Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $password, $role]);
    header("Location: manage-users.php");
    exit();
}

// Filter
$filter = "";
$params = [];

if (!empty($_GET['q'])) {
    $filter = "WHERE username LIKE :q OR email LIKE :q";
    $params[':q'] = '%' . $_GET['q'] . '%';
}

$sql = "SELECT * FROM users $filter ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users - Admin Panel</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 20px;
        }
        h2, h3 { color: #333; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background: #fff;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 0.75rem;
            text-align: left;
        }
        th {
            background: #eee;
        }
        form.inline {
            display: inline-block;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .search-bar {
            margin: 1rem 0;
        }
        input, select {
            padding: 0.5rem;
            margin: 0.3rem;
        }
        button {
            padding: 0.5rem 1rem;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h2>Admin Panel - Manage Users</h2>

<div class="search-bar">
    <form method="GET" class="inline">
        <input type="text" name="q" placeholder="Search by username/email" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        <button type="submit">Search</button>
    </form>
    <a href="?export=csv"><button>Export CSV</button></a>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Created</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
            <td>#<?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= $u['role'] ?></td>
            <td><?= $u['created_at'] ?></td>
            <td class="actions">
                <?php if ($u['id'] != ($_SESSION['id'] ?? 0)): ?>
                    <form method="POST" action="delete-user.php" class="inline" onsubmit="return confirm('Are you sure?')">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <button type="submit">Delete</button>
                    </form>
                <?php else: ?>
                    <em>(You)</em>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h3>Add User</h3>
<form method="POST">
    <input type="hidden" name="add_user" value="1">
    <input type="text" name="username" required placeholder="Username">
    <input type="email" name="email" required placeholder="Email">
    <input type="password" name="password" required placeholder="Password">
    <select name="role" required>
        <option value="user">User</option>
        <option value="admin">Admin</option>
    </select>
    <button type="submit">Add</button>
</form>

</body>
</html>
