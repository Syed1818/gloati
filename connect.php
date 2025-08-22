<?php
$host = "dpg-d2jd8cbuibrs73dgeqcg-a.oregon-postgres.render.com";
$port = "5432";
$dbname = "gloati_users";
$user = "myadmin";
$password = "XwXE4IolsAZeSmqU0qhQ9DAgEKNPjXLG";

try {
    $conn = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
