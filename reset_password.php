<?php include 'config.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>
    
    <?php
    // Dapatkan token dari GET atau POST
    $token = $_GET['token'] ?? $_POST['token'] ?? '';
    
    if (!$token) {
        echo "<p style='color:red'>Token tidak ditemukan!</p>";
        echo "<p><a href='forgot_password.php'>Request reset password baru</a></p>";
        exit();
    }
    
    echo "<p>Token: $token</p>";
    
    // Cek token di database
    $stmt = $pdo->prepare("SELECT id, reset_token_expires FROM users WHERE reset_token = ?");
    $stmt->execute([$token]);
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch();
        $current_time = date('Y-m-d H:i:s');
        $expires_time = $user['reset_token_expires'];
        
        echo "<p>Token expires: $expires_time</p>";
        echo "<p>Current time: $current_time</p>";
        
        // Cek apakah token masih valid
        if (strtotime($expires_time) > time()) {
            // Token masih valid
            if ($_POST && isset($_POST['password'])) {
                $new_password = $_POST['password'];
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update password dan hapus token
                $update = $pdo->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
                if ($update->execute([$password_hash, $user['id']])) {
                    echo "<p style='color:green'>✓ Password berhasil direset! <a href='login.php'>Login disini</a></p>";
                } else {
                    echo "<p style='color:red'>✗ Gagal reset password!</p>";
                }
            } else {
                ?>
                <form method="POST">
                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                    <p>Password Baru: <input type="password" name="password" required></p>
                    <p><button type="submit">Reset Password</button></p>
                </form>
                <?php
            }
        } else {
            echo "<p style='color:red'>✗ Token sudah kadaluarsa!</p>";
            echo "<p><a href='forgot_password.php'>Request reset password baru</a></p>";
        }
    } else {
        echo "<p style='color:red'>✗ Token tidak valid!</p>";
        echo "<p><a href='forgot_password.php'>Request reset password baru</a></p>";
    }
    ?>
</body>
</html>