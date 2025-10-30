<?php
require 'env.php'; // Tambahkan ini

$host = Env::get('DB_HOST', 'localhost');
$dbname = Env::get('DB_NAME', 'web_uts_db');
$username = Env::get('DB_USERNAME', 'root');
$password = Env::get('DB_PASSWORD', '');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

$base_url = Env::get('BASE_URL', 'http://localhost/UTS');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}