<?php include 'config.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Admin Gudang</title>
</head>
<body>
    <h2>Login Admin Gudang</h2>
    
    <?php
    if ($_POST) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['full_name'];
            
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<p style='color:red'>Email atau password salah!</p>";
        }
    }
    ?>
    
    <form method="POST">
        <p>Email: <input type="email" name="email" required></p>
        <p>Password: <input type="password" name="password" required></p>
        <p><button type="submit">Login</button></p>
    </form>
    
    <p><a href="forgot_password.php">Lupa Password?</a></p>
    <p><a href="register.php">Belum punya akun? Daftar</a></p>
</body>
</html>