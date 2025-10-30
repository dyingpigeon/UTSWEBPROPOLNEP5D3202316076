<?php include 'config.php'; 
require 'send_email.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Admin Gudang</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: #333;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .logo p {
            color: #666;
            font-size: 14px;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border: 1px solid transparent;
        }

        .alert-success {
            background-color: #e8f5e8;
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background-color: #fee;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .links {
            margin-top: 25px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e1e5e9;
        }

        .links a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
            display: block;
            margin: 8px 0;
        }

        .links a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .input-icon {
            position: relative;
        }

        .input-icon .form-control {
            padding-left: 40px;
        }

        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 16px;
        }

        @media (max-width: 480px) {
            .register-container {
                padding: 30px 20px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <h1><i class="fas fa-warehouse"></i> Daftar Akun</h1>
            <p>Buat akun admin gudang baru</p>
        </div>

        <?php
        if ($_POST) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $full_name = $_POST['full_name'];
            
            // Cek email sudah terdaftar
            $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $check->execute([$email]);
            
            if ($check->rowCount() > 0) {
                echo '<div class="alert alert-error">';
                echo '<i class="fas fa-exclamation-circle"></i> Email sudah terdaftar!';
                echo '</div>';
            } else {
                // Generate activation token
                $activation_token = bin2hex(random_bytes(32));
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                // Simpan user
                $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, full_name, activation_token) VALUES (?, ?, ?, ?)");
                $stmt->execute([$email, $password_hash, $full_name, $activation_token]);
                
                $activation_link = $base_url . "/activate.php?token=" . $activation_token;
                
                // KIRIM EMAIL AKTIVASI
                $email_result = sendActivationEmail($email, $full_name, $activation_link);
                
                if ($email_result['success']) {
                    echo '<div class="alert alert-success">';
                    echo '<i class="fas fa-check-circle"></i> ';
                    echo '<div>';
                    echo '<strong>Registrasi berhasil!</strong><br>';
                    echo 'Link aktivasi telah dikirim ke email Anda.';
                    echo '</div>';
                    echo '</div>';
                } else {
                    echo '<div class="alert alert-warning">';
                    echo '<i class="fas fa-exclamation-triangle"></i> ';
                    echo '<div>';
                    echo '<strong>Registrasi berhasil!</strong><br>';
                    echo 'Tapi gagal mengirim email. Gunakan link manual:';
                    echo '</div>';
                    echo '</div>';
                    echo '<div style="background: #f8f9fa; padding: 10px; border-radius: 6px; margin-top: 10px; font-size: 12px; word-break: break-all;">';
                    echo '<a href="' . $activation_link . '">' . $activation_link . '</a>';
                    echo '</div>';
                }
            }
        }
        ?>

        <form method="POST">
            <div class="form-group">
                <label for="full_name">Nama Lengkap</label>
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" 
                           id="full_name" 
                           name="full_name" 
                           class="form-control" 
                           placeholder="Masukkan nama lengkap"
                           required
                           value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           placeholder="Masukkan email"
                           required
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           placeholder="Buat password"
                           required
                           minlength="6">
                </div>
            </div>

            <button type="submit" class="btn">
                <i class="fas fa-user-plus"></i> Daftar Sekarang
            </button>
        </form>

        <div class="links">
            <a href="login.php">
                <i class="fas fa-sign-in-alt"></i> Sudah punya akun? Login
            </a>
        </div>
    </div>

    <script>
        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => alert.remove(), 500);
                }, 8000);
            });
        });
    </script>
</body>
</html>