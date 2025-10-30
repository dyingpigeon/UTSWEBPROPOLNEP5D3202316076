<?php
include 'config.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data user
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Hitung statistik
$product_count = $pdo->query("SELECT COUNT(*) FROM products WHERE created_by = $user_id")->fetchColumn();
$low_stock = $pdo->query("SELECT COUNT(*) FROM products WHERE created_by = $user_id AND stock <= min_stock")->fetchColumn();
$total_value = $pdo->query("SELECT SUM(price * stock) FROM products WHERE created_by = $user_id")->fetchColumn();
$total_value = $total_value ? $total_value : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Gudang</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header Styles */
        .header {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .welcome-section h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .welcome-section h1 i {
            color: #667eea;
            font-size: 32px;
        }

        .welcome-section p {
            color: #666;
            font-size: 16px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 15px 25px;
            border-radius: 15px;
            color: white;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            backdrop-filter: blur(10px);
        }

        .user-details {
            text-align: right;
        }

        .user-details .name {
            font-weight: 600;
            font-size: 16px;
        }

        .user-details .role {
            font-size: 12px;
            opacity: 0.9;
        }

        /* Navigation Styles */
        .nav-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .nav-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-card:hover {
            transform: translateY(-5px);
            border-color: #667eea;
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.2);
        }

        .nav-icon {
            width: 70px;
            height: 70px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
        }

        .nav-card.products .nav-icon {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .nav-card.profile .nav-icon {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .nav-card.logout .nav-icon {
            background: linear-gradient(135deg, #dc3545, #e83e8c);
        }

        .nav-content h3 {
            font-size: 18px;
            margin-bottom: 8px;
            color: #333;
        }

        .nav-content p {
            font-size: 14px;
            color: #666;
            line-height: 1.4;
        }

        /* Statistics Styles */
        .stats-section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 24px;
            color: #333;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i {
            color: #667eea;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border-left: 5px solid #667eea;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .stat-card.warning {
            border-left-color: #ffc107;
        }

        .stat-card.success {
            border-left-color: #28a745;
        }

        .stat-card.danger {
            border-left-color: #dc3545;
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: between;
            margin-bottom: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }

        .stat-card .stat-icon {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .stat-card.warning .stat-icon {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
        }

        .stat-card.success .stat-icon {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .stat-card.danger .stat-icon {
            background: linear-gradient(135deg, #dc3545, #e83e8c);
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .stat-trend {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 12px;
            background: #e8f5e8;
            color: #28a745;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Quick Actions */
        .quick-actions {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .action-btn {
            background: #f8f9fa;
            border: 2px solid #e1e5e9;
            padding: 15px 20px;
            border-radius: 12px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            text-align: left;
        }

        .action-btn:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
            transform: translateY(-2px);
        }

        .action-btn i {
            font-size: 18px;
            width: 20px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
            }

            .user-info {
                align-self: center;
            }

            .nav-cards {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .welcome-section h1 {
                font-size: 24px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }

            .header {
                padding: 20px;
            }

            .nav-card {
                padding: 20px;
            }

            .stat-card {
                padding: 20px;
            }
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .nav-card, .stat-card {
            animation: fadeIn 0.6s ease-out;
        }

        .nav-card:nth-child(2) { animation-delay: 0.1s; }
        .nav-card:nth-child(3) { animation-delay: 0.2s; }
        .stat-card:nth-child(2) { animation-delay: 0.3s; }
        .stat-card:nth-child(3) { animation-delay: 0.4s; }
        .stat-card:nth-child(4) { animation-delay: 0.5s; }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="welcome-section">
                <h1><i class="fas fa-warehouse"></i> Dashboard Admin Gudang</h1>
                <p>Selamat datang kembali, <?php echo htmlspecialchars($user['full_name']); ?>! 👋</p>
                <p style="color: #667eea; font-size: 14px; margin-top: 5px;">
                    <i class="fas fa-clock"></i> Terakhir login: <?php echo date('d F Y H:i'); ?>
                </p>
            </div>
            
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                </div>
                <div class="user-details">
                    <div class="name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                    <div class="role">Admin Gudang</div>
                </div>
            </div>
        </div>

        <!-- Navigation Cards -->
        <div class="nav-cards">
            <a href="products.php" class="nav-card products">
                <div class="nav-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="nav-content">
                    <h3>Kelola Produk</h3>
                    <p>Tambah, edit, dan kelola produk di gudang Anda</p>
                </div>
                <i class="fas fa-chevron-right" style="margin-left: auto; color: #667eea;"></i>
            </a>

            <a href="profile.php" class="nav-card profile">
                <div class="nav-icon">
                    <i class="fas fa-user-cog"></i>
                </div>
                <div class="nav-content">
                    <h3>Profil Saya</h3>
                    <p>Kelola informasi akun dan ubah password</p>
                </div>
                <i class="fas fa-chevron-right" style="margin-left: auto; color: #28a745;"></i>
            </a>

            <a href="logout.php" class="nav-card logout" onclick="return confirm('Yakin ingin logout?')">
                <div class="nav-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <div class="nav-content">
                    <h3>Logout</h3>
                    <p>Keluar dari sistem admin gudang</p>
                </div>
                <i class="fas fa-chevron-right" style="margin-left: auto; color: #dc3545;"></i>
            </a>
        </div>

        <!-- Statistics Section -->
        <div class="stats-section">
            <h2 class="section-title"><i class="fas fa-chart-bar"></i> Statistik Gudang</h2>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                    <div class="stat-number"><?php echo $product_count; ?></div>
                    <div class="stat-label">Total Produk</div>
                    <div class="stat-trend">
                        <i class="fas fa-chart-line"></i> Semua produk aktif
                    </div>
                </div>

                <div class="stat-card warning">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                    <div class="stat-number"><?php echo $low_stock; ?></div>
                    <div class="stat-label">Stok Rendah</div>
                    <div class="stat-trend" style="background: #fff3cd; color: #856404;">
                        <i class="fas fa-clock"></i> Perlu restock
                    </div>
                </div>

                <div class="stat-card success">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                    <div class="stat-number">Rp <?php echo number_format($total_value, 0, ',', '.'); ?></div>
                    <div class="stat-label">Total Nilai Inventori</div>
                    <div class="stat-trend">
                        <i class="fas fa-trending-up"></i> Nilai saat ini
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="stat-number"><?php echo $product_count - $low_stock; ?></div>
                    <div class="stat-label">Stok Aman</div>
                    <div class="stat-trend">
                        <i class="fas fa-shield-alt"></i> Stok mencukupi
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h2 class="section-title"><i class="fas fa-bolt"></i> Akses Cepat</h2>
            
            <div class="actions-grid">
                <a href="products.php" class="action-btn">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Produk Baru</span>
                </a>
                
                <a href="products.php" class="action-btn">
                    <i class="fas fa-search"></i>
                    <span>Cari Produk</span>
                </a>
                
                <a href="products.php" class="action-btn">
                    <i class="fas fa-chart-pie"></i>
                    <span>Laporan Stok</span>
                </a>
                
                <a href="profile.php" class="action-btn">
                    <i class="fas fa-key"></i>
                    <span>Ubah Password</span>
                </a>
            </div>
        </div>
    </div>

    <script>
        // Simple animations
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects
            const cards = document.querySelectorAll('.nav-card, .stat-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>