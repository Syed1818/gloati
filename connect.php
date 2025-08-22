<?php
$host = "dpg-d2jd8cbuibrs73dgeqcg-a.oregon-postgres.render.com";
$port = "5432";
$dbname = "gloati_users";
$user = "myadmin";
$password = "XwXE4IolsAZeSmqU0qhQ9DAgEKNPjXLG";

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "Successfully connected to the database!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
