<?php include 'config.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrasi Admin Gudang</title>
</head>
<body>
    <h2>Registrasi Admin Gudang</h2>
    
    <?php
    if ($_POST) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $full_name = $_POST['full_name'];
        
        // Cek email sudah terdaftar
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        
        if ($check->rowCount() > 0) {
            echo "<p style='color:red'>Email sudah terdaftar!</p>";
        } else {
            // Generate activation token
            $activation_token = bin2hex(random_bytes(32));
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Simpan user
            $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, full_name, activation_token) VALUES (?, ?, ?, ?)");
            $stmt->execute([$email, $password_hash, $full_name, $activation_token]);
            
            // PERBAIKAN: Gunakan base_url untuk link aktivasi
            $activation_link = $base_url . "/activate.php?token=" . $activation_token;
            echo "<p>Registrasi berhasil! Link aktivasi: <a href='$activation_link'>$activation_link</a></p>";
        }
    }
    ?>
    
    <form method="POST">
        <p>Nama Lengkap: <input type="text" name="full_name" required></p>
        <p>Email: <input type="email" name="email" required></p>
        <p>Password: <input type="password" name="password" required></p>
        <p><button type="submit">Daftar</button></p>
    </form>
    
    <p><a href="login.php">Sudah punya akun? Login</a></p>
</body>
</html>