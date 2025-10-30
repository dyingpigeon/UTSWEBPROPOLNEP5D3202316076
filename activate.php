<?php
include 'config.php';

$token = $_GET['token'] ?? '';

if ($token) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE activation_token = ? AND status = 'pending'");
    $stmt->execute([$token]);
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch();
        
        // Aktivasi akun
        $update = $pdo->prepare("UPDATE users SET status = 'active', activation_token = NULL WHERE id = ?");
        $update->execute([$user['id']]);
        
        echo "<p>Akun berhasil diaktivasi! <a href='login.php'>Login disini</a></p>";
    } else {
        echo "<p>Token aktivasi tidak valid atau sudah digunakan!</p>";
    }
} else {
    echo "<p>Token tidak ditemukan!</p>";
}
?>