<?php include 'config.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Debug Reset Password</title>
</head>
<body>
    <h2>Debug Reset Password</h2>
    
    <?php
    $token = 'c38ffa7a4063b5e8bb878eae93663d4934e5835ff412d50c84211966dcb39cb3';
    echo "<p>Token yang diuji: $token</p>";
    echo "<p>Panjang token: " . strlen($token) . " karakter</p>";
    
    // 1. Cek apakah token ada di database
    $stmt = $pdo->prepare("SELECT id, email, reset_token, reset_token_expires, NOW() as current_time_db FROM users WHERE reset_token = ?");
    $stmt->execute([$token]);
    
    echo "<h3>1. Pencarian Token di Database:</h3>";
    echo "<p>Jumlah hasil: " . $stmt->rowCount() . "</p>";
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch();
        echo "<p>ID User: " . $user['id'] . "</p>";
        echo "<p>Email: " . $user['email'] . "</p>";
        echo "<p>Token di DB: " . $user['reset_token'] . "</p>";
        echo "<p>Panjang token di DB: " . strlen($user['reset_token']) . " karakter</p>";
        echo "<p>Token expires: " . $user['reset_token_expires'] . "</p>";
        echo "<p>Current time: " . $user['current_time_db'] . "</p>";
        
        // 2. Cek apakah token sudah expired
        $stmt2 = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expires > NOW()");
        $stmt2->execute([$token]);
        
        echo "<h3>2. Cek Masa Berlaku Token:</h3>";
        echo "<p>Token masih valid: " . ($stmt2->rowCount() > 0 ? 'YA' : 'TIDAK') . "</p>";
        
        if ($stmt2->rowCount() > 0) {
            echo "<p style='color:green'>✓ Token VALID dan bisa digunakan</p>";
            
            // Tampilkan form reset
            ?>
            <h3>3. Reset Password:</h3>
            <form action="reset_password.php" method="POST">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <p>Password Baru: <input type="password" name="password" required></p>
                <p><button type="submit">Reset Password</button></p>
            </form>
            <?php
        } else {
            echo "<p style='color:red'>✗ Token sudah KADALUARSA</p>";
        }
    } else {
        echo "<p style='color:red'>✗ Token TIDAK DITEMUKAN di database</p>";
        
        // Cek token yang ada di database
        $all_tokens = $pdo->query("SELECT reset_token, LENGTH(reset_token) as token_length FROM users WHERE reset_token IS NOT NULL")->fetchAll();
        echo "<h3>Token yang ada di database:</h3>";
        foreach ($all_tokens as $row) {
            echo "<p>" . $row['reset_token'] . " (panjang: " . $row['token_length'] . " karakter)</p>";
        }
    }
    ?>
    
    <hr>
    <p><a href="forgot_password.php">Request reset password baru</a></p>
    <p><a href="login.php">Kembali ke login</a></p>
</body>
</html>