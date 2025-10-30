<?php
// Pastikan tidak ada spasi/karakter baru sebelum <?php
$host = 'localhost';
$dbname = 'web_uts_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

$base_url = 'http://localhost/UTS';

// Pastikan tidak ada output sebelum session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Pastikan tidak ada karakter/spasi setelah ?>
?>
