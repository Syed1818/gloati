<?php
session_start();

include 'connect.php';

// CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=users.csv');
    $output = fopen("php://output", "w");
    fputcsv($output, ['ID', 'Username', 'Email', 'Created At']);
    $res = mysqli_query($conn, "SELECT id, username, email, created_at FROM users");
    while ($row = mysqli_fetch_assoc($res)) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}

// Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    mysqli_query($conn, "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')");
    header("Location: manage-users.php");
    exit();
}

// Filter
$filter = "";
if (isset($_GET['q']) && $_GET['q'] !== '') {
    $q = mysqli_real_escape_string($conn, $_GET['q']);
    $filter = "WHERE username LIKE '%$q%' OR email LIKE '%$q%'";
}

$users = mysqli_query($conn, "SELECT * FROM users $filter ORDER BY created_at DESC");
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
        h2 { color: #333; }
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
        th { background: #eee; }
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
    </style>
</head>
<body>

<h2>Admin Panel - Manage Users</h2>

<div class="search-bar">
    <form method="GET" class="inline">
        <input type="text" name="q" placeholder="Search by username/email" value="<?= $_GET['q'] ?? '' ?>">
        <button type="submit">Search</button>
    </form>
    <a href="manage-users.php?export=csv"><button>Export CSV</button></a>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Created</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($u = mysqli_fetch_assoc($users)): ?>
        <tr>
            <td>#<?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= $u['role'] ?></td>
            <td><?= $u['created_at'] ?></td>
            <td class="actions">
                <?php if ($u['id'] != $_SESSION['id']): ?>
                    <form method="POST" action="delete-user.php" class="inline" onsubmit="return confirm('Are you sure?')">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <button type="submit">Delete</button>
                    </form>
                <?php else: ?>
                    <em>(You)</em>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
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
