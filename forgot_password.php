<?php include 'config.php'; 
require 'send_email.php'; // Tambahkan ini
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lupa Password</title>
</head>
<body>
    <h2>Lupa Password</h2>
    
    <?php
    if ($_POST) {
        $email = $_POST['email'];
        
        $stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            $reset_token = bin2hex(random_bytes(32));
            $reset_expires = date('Y-m-d H:i:s', time() + 86400); // 24 jam
            
            // Simpan token reset
            $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?");
            $update->execute([$reset_token, $reset_expires, $user['id']]);
            
            $reset_link = $base_url . "/reset_password.php?token=" . $reset_token;
            
            // KIRIM EMAIL ASLI
            if (sendResetPasswordEmail($email, $user['full_name'], $reset_link)) {
                echo "<p style='color:green'>Link reset password telah dikirim ke email Anda!</p>";
                echo "<p>Silakan cek inbox atau spam folder.</p>";
            } else {
                echo "<p style='color:red'>Gagal mengirim email. Link reset: <a href='$reset_link'>$reset_link</a></p>";
            }
            
        } else {
            echo "<p style='color:red'>Email tidak ditemukan!</p>";
        }
    }
    ?>
    
    <form method="POST">
        <p>Email: <input type="email" name="email" required></p>
        <p><button type="submit">Kirim Link Reset</button></p>
    </form>
    
    <p><a href="login.php">Kembali ke Login</a></p>
</body>
</html>