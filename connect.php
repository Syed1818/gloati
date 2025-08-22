<?php
$host = "dpg-d2jvkk3e5dus738k3tvg-a.oregon-postgres.render.com";
$port = "5432";
$dbname = "gloati_users_123t";
$user = "gloati_users_123t_user";
$password = "IioFoNnK6Kx9RyBYdlfdJiZvR1KW7HE6";

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    return $pdo; // ✅ return the connection instead of echo
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    throw $e; // ✅ throw error so calling file can handle it
}
?>
