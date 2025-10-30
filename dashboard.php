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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin Gudang</title>
</head>
<body>
    <h2>Dashboard Admin Gudang</h2>
    <p>Selamat datang, <?php echo $user['full_name']; ?>!</p>
    
    <nav>
        <a href="products.php">Kelola Produk</a> |
        <a href="profile.php">Profil Saya</a> |
        <a href="logout.php">Logout</a>
    </nav>
    
    <h3>Statistik</h3>
    <?php
    // Hitung total produk
    $product_count = $pdo->query("SELECT COUNT(*) FROM products WHERE created_by = $user_id")->fetchColumn();
    echo "<p>Total Produk: $product_count</p>";
    
    // Hitung produk stok rendah
    $low_stock = $pdo->query("SELECT COUNT(*) FROM products WHERE created_by = $user_id AND stock <= min_stock")->fetchColumn();
    echo "<p>Produk Stok Rendah: $low_stock</p>";
    ?>
</body>
</html>