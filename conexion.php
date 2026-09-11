<?php
$host = getenv('MYSQL_ADDON_HOST') ?: 'localhost';
$dbname = getenv('MYSQL_ADDON_DB') ?: 'perlas_shop';
$username = getenv('MYSQL_ADDON_USER') ?: 'root';
$password = getenv('MYSQL_ADDON_PASSWORD') ?: '';
$port = getenv('MYSQL_ADDON_PORT') ?: '3306';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>