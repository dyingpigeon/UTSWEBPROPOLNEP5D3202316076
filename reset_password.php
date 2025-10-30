<?php include 'config.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Admin Gudang</title>
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

        .reset-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            transition: transform 0.3s ease;
        }

        .reset-container:hover {
            transform: translateY(-5px);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: #333;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .logo p {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: linear-gradient(135deg, #e8f5e8, #d4edda);
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background: linear-gradient(135deg, #fee, #f8d7da);
            border-color: #f5c6cb;
            color: #721c24;
        }

        .alert-info {
            background: linear-gradient(135deg, #e3f2fd, #d1ecf1);
            border-color: #b8daff;
            color: #0c5460;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .input-icon {
            position: relative;
        }

        .input-icon .form-control {
            padding-left: 50px;
        }

        .input-icon i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 16px;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn:active {
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #6c757d;
            margin-top: 15px;
        }

        .btn-secondary:hover {
            background: #5a6268;
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
        }

        .links {
            margin-top: 30px;
            text-align: center;
        }

        .links a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0 10px;
        }

        .links a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .password-strength {
            margin-top: 10px;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 6px;
            background: #f8f9fa;
        }

        .strength-weak { 
            color: #dc3545; 
            background: #fee;
        }
        .strength-medium { 
            color: #ffc107; 
            background: #fff3cd;
        }
        .strength-strong { 
            color: #28a745; 
            background: #e8f5e8;
        }

        .token-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #666;
            border-left: 4px solid #667eea;
        }

        .token-info strong {
            color: #333;
        }

        /* Progress bar */
        .progress-bar {
            width: 100%;
            height: 4px;
            background: #e1e5e9;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }

        .progress {
            height: 100%;
            border-radius: 2px;
            transition: width 0.3s ease;
        }

        .progress-weak { 
            width: 33%; 
            background: #dc3545; 
        }
        .progress-medium { 
            width: 66%; 
            background: #ffc107; 
        }
        .progress-strong { 
            width: 100%; 
            background: #28a745; 
        }

        @media (max-width: 480px) {
            .reset-container {
                padding: 30px 20px;
            }
            
            body {
                padding: 10px;
            }
            
            .links a {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="reset-container">
        <div class="logo">
            <h1><i class="fas fa-lock"></i> Reset Password</h1>
            <p>Buat password baru untuk akun Anda</p>
        </div>

        <?php
        // Dapatkan token dari GET atau POST
        $token = $_GET['token'] ?? $_POST['token'] ?? '';
        
        if (!$token) {
            echo '<div class="alert alert-error">';
            echo '<i class="fas fa-exclamation-triangle"></i>';
            echo '<div>';
            echo '<strong>Token tidak ditemukan!</strong><br>';
            echo 'Pastikan Anda mengklik link yang benar dari email.';
            echo '</div>';
            echo '</div>';
            echo '<a href="forgot_password.php" class="btn btn-secondary">';
            echo '<i class="fas fa-redo"></i> Request Reset Baru';
            echo '</a>';
            exit();
        }
        
        // Cek token di database
        $stmt = $pdo->prepare("SELECT id, reset_token_expires, email FROM users WHERE reset_token = ?");
        $stmt->execute([$token]);
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            $current_time = date('Y-m-d H:i:s');
            $expires_time = $user['reset_token_expires'];
            
            // Cek apakah token masih valid
            if (strtotime($expires_time) > time()) {
                // Token masih valid
                if ($_POST && isset($_POST['password'])) {
                    $new_password = $_POST['password'];
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    
                    // Update password dan hapus token
                    $update = $pdo->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
                    if ($update->execute([$password_hash, $user['id']])) {
                        echo '<div class="alert alert-success">';
                        echo '<i class="fas fa-check-circle"></i>';
                        echo '<div>';
                        echo '<strong>Password berhasil direset!</strong><br>';
                        echo 'Sekarang Anda bisa login dengan password baru.';
                        echo '</div>';
                        echo '</div>';
                        echo '<a href="login.php" class="btn">';
                        echo '<i class="fas fa-sign-in-alt"></i> Login Sekarang';
                        echo '</a>';
                    } else {
                        echo '<div class="alert alert-error">';
                        echo '<i class="fas fa-times-circle"></i>';
                        echo '<div>';
                        echo '<strong>Gagal reset password!</strong><br>';
                        echo 'Silakan coba lagi atau request reset baru.';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    // Tampilkan form reset password
                    ?>
                    <div class="token-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Token valid</strong> - Reset password untuk akun: 
                        <?php echo substr($user['email'], 0, 3) . '***' . substr($user['email'], strpos($user['email'], '@')); ?>
                    </div>

                    <form method="POST">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                        
                        <div class="form-group">
                            <label for="password">
                                <i class="fas fa-lock"></i> Password Baru
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-key"></i>
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       class="form-control" 
                                       placeholder="Masukkan password baru"
                                       required
                                       minlength="6"
                                       oninput="checkPasswordStrength(this.value)">
                            </div>
                            <div class="progress-bar">
                                <div id="password-progress" class="progress"></div>
                            </div>
                            <div id="password-strength" class="password-strength">
                                <i class="fas fa-info-circle"></i>
                                <span id="strength-text">Masukkan password baru</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">
                                <i class="fas fa-lock"></i> Konfirmasi Password
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-key"></i>
                                <input type="password" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       class="form-control" 
                                       placeholder="Konfirmasi password baru"
                                       required
                                       oninput="checkPasswordMatch()">
                            </div>
                            <div id="password-match" class="password-strength" style="display: none;">
                                <i class="fas fa-check"></i>
                                <span>Password cocok</span>
                            </div>
                        </div>

                        <button type="submit" class="btn" id="submit-btn">
                            <i class="fas fa-save"></i> Reset Password
                        </button>
                    </form>
                    <?php
                }
            } else {
                echo '<div class="alert alert-error">';
                echo '<i class="fas fa-clock"></i>';
                echo '<div>';
                echo '<strong>Token sudah kadaluarsa!</strong><br>';
                echo 'Token reset password hanya berlaku 1 jam.';
                echo '</div>';
                echo '</div>';
                echo '<a href="forgot_password.php" class="btn btn-secondary">';
                echo '<i class="fas fa-redo"></i> Request Reset Baru';
                echo '</a>';
            }
        } else {
            echo '<div class="alert alert-error">';
            echo '<i class="fas fa-ban"></i>';
            echo '<div>';
            echo '<strong>Token tidak valid!</strong><br>';
            echo 'Token tidak ditemukan atau sudah digunakan.';
            echo '</div>';
            echo '</div>';
            echo '<a href="forgot_password.php" class="btn btn-secondary">';
            echo '<i class="fas fa-redo"></i> Request Reset Baru';
            echo '</a>';
        }
        ?>

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
        // Password strength indicator
        function checkPasswordStrength(password) {
            const strengthElement = document.getElementById('password-strength');
            const progressElement = document.getElementById('password-progress');
            const strengthText = document.getElementById('strength-text');
            const submitBtn = document.getElementById('submit-btn');
            
            let strength = 0;
            let feedback = '';
            let className = '';
            let progressClass = '';
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/\d/)) strength++;
            if (password.match(/[^a-zA-Z\d]/)) strength++;
            
            switch(strength) {
                case 0:
                    feedback = 'Masukkan password';
                    className = '';
                    progressClass = '';
                    submitBtn.disabled = true;
                    break;
                case 1:
                    feedback = 'Lemah';
                    className = 'strength-weak';
                    progressClass = 'progress-weak';
                    submitBtn.disabled = false;
                    break;
                case 2:
                case 3:
                    feedback = 'Sedang';
                    className = 'strength-medium';
                    progressClass = 'progress-medium';
                    submitBtn.disabled = false;
                    break;
                case 4:
                    feedback = 'Kuat';
                    className = 'strength-strong';
                    progressClass = 'progress-strong';
                    submitBtn.disabled = false;
                    break;
            }
            
            strengthText.textContent = feedback;
            strengthElement.className = 'password-strength ' + className;
            progressElement.className = 'progress ' + progressClass;
            
            // Check password match jika confirm password sudah diisi
            checkPasswordMatch();
        }

        // Check password confirmation
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchElement = document.getElementById('password-match');
            const submitBtn = document.getElementById('submit-btn');
            
            if (confirmPassword === '') {
                matchElement.style.display = 'none';
                return;
            }
            
            if (password === confirmPassword) {
                matchElement.style.display = 'flex';
                matchElement.innerHTML = '<i class="fas fa-check"></i><span>Password cocok</span>';
                matchElement.style.background = '#e8f5e8';
                matchElement.style.color = '#28a745';
                submitBtn.disabled = false;
            } else {
                matchElement.style.display = 'flex';
                matchElement.innerHTML = '<i class="fas fa-times"></i><span>Password tidak cocok</span>';
                matchElement.style.background = '#fee';
                matchElement.style.color = '#dc3545';
                submitBtn.disabled = true;
            }
        }

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