<?php include 'config.php'; 
require 'send_email.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Admin Gudang</title>
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

        .forgot-container {
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
            line-height: 1.5;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            gap: 10px;
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn:active {
            transform: translateY(0);
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
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

        .reset-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            border: 1px dashed #667eea;
        }

        .reset-info a {
            word-break: break-all;
            font-size: 12px;
            color: #667eea;
            text-decoration: none;
        }

        .reset-info a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .forgot-container {
                padding: 30px 20px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="forgot-container">
        <div class="logo">
            <h1><i class="fas fa-key"></i> Lupa Password</h1>
            <p>Masukkan email Anda untuk mendapatkan link reset password</p>
        </div>

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
                $email_result = sendResetPasswordEmail($email, $user['full_name'], $reset_link);
                
                if ($email_result['success']) {
                    echo '<div class="alert alert-success">';
                    echo '<i class="fas fa-check-circle"></i>';
                    echo '<div>';
                    echo '<strong>Link reset password telah dikirim!</strong><br>';
                    echo 'Silakan cek inbox atau spam folder di email Anda.';
                    echo '</div>';
                    echo '</div>';
                } else {
                    echo '<div class="alert alert-warning">';
                    echo '<i class="fas fa-exclamation-triangle"></i>';
                    echo '<div>';
                    echo '<strong>Gagal mengirim email</strong><br>';
                    echo 'Tapi link reset berhasil dibuat. Gunakan link manual:';
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="reset-info">';
                    echo '<a href="' . $reset_link . '">' . $reset_link . '</a>';
                    echo '</div>';
                }
                
            } else {
                echo '<div class="alert alert-error">';
                echo '<i class="fas fa-exclamation-circle"></i> Email tidak ditemukan atau akun belum aktif!';
                echo '</div>';
            }
        }
        ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           placeholder="Masukkan email yang terdaftar"
                           required
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
            </div>

            <button type="submit" class="btn">
                <i class="fas fa-paper-plane"></i> Kirim Link Reset
            </button>
        </form>

        <div class="links">
            <a href="login.php">
                <i class="fas fa-arrow-left"></i> Kembali ke Login
            </a>
            <a href="register.php">
                <i class="fas fa-user-plus"></i> Buat Akun Baru
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