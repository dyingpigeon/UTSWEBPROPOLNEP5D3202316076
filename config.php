<?php
$host = 'localhost';
$dbname = 'web_uts_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // SET TIMEZONE - PERBAIKAN WAKTU
    $pdo->exec("SET time_zone = '+07:00'");
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

$base_url = 'http://localhost/UTS';
session_start();

// Set timezone PHP
date_default_timezone_set('Asia/Jakarta');
?>