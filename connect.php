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

    // ✅ Test query: get server time
    $stmt = $pdo->query("SELECT NOW() as server_time");
    $row = $stmt->fetch();

    echo "✅ Successfully connected to Render PostgreSQL!<br>";
    echo "⏰ Server time: " . $row['server_time'];

} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
?>
