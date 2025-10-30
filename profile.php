<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Ubah password
if ($_POST && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    
    if (password_verify($current_password, $user['password_hash'])) {
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $update->execute([$new_hash, $user_id]);
        $success_message = 'Password berhasil diubah!';
    } else {
        $error_message = 'Password saat ini salah!';
    }
}

// Request reset password (forgot password dari dalam profil)
if ($_POST && isset($_POST['request_reset'])) {
    $reset_token = bin2hex(random_bytes(32));
    $reset_expires = date('Y-m-d H:i:s', time() + 3600); // 1 jam
    
    // Simpan token reset
    $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?");
    $update->execute([$reset_token, $reset_expires, $user_id]);
    
    $reset_link = $base_url . "/reset_password.php?token=" . $reset_token;
    $reset_success = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Admin Gudang</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }

        .header h1 {
            color: #333;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .header h1 i {
            color: #667eea;
            font-size: 32px;
        }

        .back-link {
            color: #667eea;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            transform: translateX(-5px);
            text-decoration: underline;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 30px;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            color: #333;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 20px;
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 15px;
        }

        .card h3 i {
            color: #667eea;
            width: 24px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
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

        .info-item {
            display: flex;
            justify-content: between;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .info-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .info-label {
            font-weight: 600;
            color: #555;
            min-width: 120px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-value {
            color: #333;
            font-weight: 500;
        }

        .status-active {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
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

        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
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

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            justify-content: center;
            margin-bottom: 15px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #667eea;
            color: #667eea;
        }

        .btn-outline:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .btn-primary:active,
        .btn-secondary:active,
        .btn-outline:active {
            transform: translateY(-1px);
        }

        .password-strength {
            margin-top: 8px;
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

        .user-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: bold;
            margin: 0 auto 20px;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .member-since {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 10px;
        }

        .reset-info {
            background: #e3f2fd;
            border: 1px solid #b8daff;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            font-size: 14px;
        }

        .reset-link {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            word-break: break-all;
            font-family: monospace;
            font-size: 12px;
            border: 1px dashed #667eea;
        }

        .security-options {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #f8f9fa;
        }

        .security-options h4 {
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
        }

        .divider {
            margin: 20px 0;
            text-align: center;
            position: relative;
            color: #666;
            font-size: 12px;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e1e5e9;
        }

        .divider span {
            background: white;
            padding: 0 10px;
            position: relative;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <a href="dashboard.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
            
            <div class="user-avatar">
                <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
            </div>
            
            <h1><i class="fas fa-user-circle"></i> Profil Saya</h1>
            <p>Kelola informasi profil dan keamanan akun Anda</p>
            
            <div class="member-since">
                <i class="fas fa-calendar-alt"></i> 
                Member sejak: <?php echo date('d F Y', strtotime($user['created_at'])); ?>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> 
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> 
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($reset_success) && $reset_success): ?>
            <div class="alert alert-info">
                <i class="fas fa-key"></i> 
                <div>
                    <strong>Link Reset Password Berhasil Dibuat!</strong><br>
                    Link reset password telah dibuat dan berlaku selama 1 jam.
                </div>
            </div>
        <?php endif; ?>

        <div class="profile-grid">
            <!-- Informasi Profil -->
            <div class="card">
                <h3><i class="fas fa-info-circle"></i> Informasi Profil</h3>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-user"></i> Nama Lengkap
                    </div>
                    <div class="info-value"><?php echo htmlspecialchars($user['full_name']); ?></div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-envelope"></i> Email
                    </div>
                    <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-badge-check"></i> Status Akun
                    </div>
                    <div class="info-value">
                        <span class="status-<?php echo $user['status']; ?>">
                            <?php echo strtoupper($user['status']); ?>
                        </span>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-id-card"></i> User ID
                    </div>
                    <div class="info-value">#<?php echo $user['id']; ?></div>
                </div>

                <!-- Opsi Reset Password -->
                <div class="security-options">
                    <h4><i class="fas fa-shield-alt"></i> Opsi Keamanan</h4>
                    
                    <form method="POST">
                        <input type="hidden" name="request_reset" value="1">
                        <button type="submit" class="btn btn-outline">
                            <i class="fas fa-redo"></i> Buat Link Reset Password
                        </button>
                    </form>
                    
                    <p style="font-size: 12px; color: #666; margin-top: 10px; text-align: center;">
                        <i class="fas fa-info-circle"></i>
                        Berguna jika Anda lupa password dan ingin reset via email
                    </p>
                </div>
            </div>

            <!-- Ubah Password -->
            <div class="card">
                <h3><i class="fas fa-lock"></i> Ubah Password</h3>
                
                <form method="POST">
                    <input type="hidden" name="change_password" value="1">
                    
                    <div class="form-group">
                        <label for="current_password">
                            <i class="fas fa-key"></i> Password Saat Ini
                        </label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" 
                                   id="current_password" 
                                   name="current_password" 
                                   class="form-control" 
                                   placeholder="Masukkan password saat ini"
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="new_password">
                            <i class="fas fa-lock"></i> Password Baru
                        </label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" 
                                   id="new_password" 
                                   name="new_password" 
                                   class="form-control" 
                                   placeholder="Masukkan password baru"
                                   required
                                   minlength="6"
                                   oninput="checkPasswordStrength(this.value)">
                        </div>
                        <div id="password-strength" class="password-strength">
                            <i class="fas fa-info-circle"></i>
                            <span id="strength-text">Kekuatan password</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Ubah Password
                    </button>
                </form>

                <?php if (isset($reset_success) && $reset_success): ?>
                    <div class="reset-info">
                        <h4><i class="fas fa-link"></i> Link Reset Password</h4>
                        <p>Salin link berikut untuk reset password (berlaku 1 jam):</p>
                        <div class="reset-link">
                            <?php echo $reset_link; ?>
                        </div>
                        <p style="font-size: 12px; margin-top: 10px; color: #666;">
                            <i class="fas fa-clock"></i> Link kadaluarsa: 
                            <?php echo date('H:i', strtotime($reset_expires)); ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Statistik Tambahan -->
        <div class="card">
            <h3><i class="fas fa-chart-bar"></i> Statistik Akun</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; text-align: center;">
                <?php
                // Hitung total produk
                $product_count = $pdo->query("SELECT COUNT(*) FROM products WHERE created_by = $user_id")->fetchColumn();
                ?>
                <div class="info-item" style="flex-direction: column; text-align: center;">
                    <div style="font-size: 24px; color: #667eea; margin-bottom: 5px;">
                        <i class="fas fa-box"></i>
                    </div>
                    <div style="font-size: 18px; font-weight: bold; color: #333;">
                        <?php echo $product_count; ?>
                    </div>
                    <div style="font-size: 12px; color: #666;">Total Produk</div>
                </div>
                
                <div class="info-item" style="flex-direction: column; text-align: center;">
                    <div style="font-size: 24px; color: #28a745; margin-bottom: 5px;">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div style="font-size: 18px; font-weight: bold; color: #333;">
                        <?php echo $user['status'] === 'active' ? 'Aktif' : 'Pending'; ?>
                    </div>
                    <div style="font-size: 12px; color: #666;">Status</div>
                </div>

                <div class="info-item" style="flex-direction: column; text-align: center;">
                    <div style="font-size: 24px; color: #ffc107; margin-bottom: 5px;">
                        <i class="fas fa-key"></i>
                    </div>
                    <div style="font-size: 18px; font-weight: bold; color: #333;">
                        Terakhir Diubah
                    </div>
                    <div style="font-size: 12px; color: #666;">Password</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password strength indicator
        function checkPasswordStrength(password) {
            const strengthElement = document.getElementById('password-strength');
            const strengthText = document.getElementById('strength-text');
            
            let strength = 0;
            let feedback = '';
            let className = '';
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/\d/)) strength++;
            if (password.match(/[^a-zA-Z\d]/)) strength++;
            
            switch(strength) {
                case 0:
                case 1:
                    feedback = 'Lemah';
                    className = 'strength-weak';
                    break;
                case 2:
                case 3:
                    feedback = 'Sedang';
                    className = 'strength-medium';
                    break;
                case 4:
                    feedback = 'Kuat';
                    className = 'strength-strong';
                    break;
            }
            
            strengthText.textContent = feedback;
            strengthElement.className = 'password-strength ' + className;
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

            // Copy reset link functionality
            const resetLinks = document.querySelectorAll('.reset-link');
            resetLinks.forEach(link => {
                link.style.cursor = 'pointer';
                link.title = 'Klik untuk menyalin link';
                link.addEventListener('click', function() {
                    const textArea = document.createElement('textarea');
                    textArea.value = this.textContent;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    
                    // Show copied feedback
                    const originalText = this.textContent;
                    this.textContent = '✓ Link disalin!';
                    this.style.background = '#e8f5e8';
                    this.style.color = '#28a745';
                    
                    setTimeout(() => {
                        this.textContent = originalText;
                        this.style.background = '#f8f9fa';
                        this.style.color = 'inherit';
                    }, 2000);
                });
            });
        });
    </script>
</body>
</html>