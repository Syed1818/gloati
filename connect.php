<?php
$host = "dpg-d1mc9eili9vc739c06o0-a.oregon-postgres.render.com";
$db = "gloati_users";
$user = "myadmin";
$pass = "WNm50QgZaSDyHlrwJmktc8IaOl7jBJtI";

try {
    $conn = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
