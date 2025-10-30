<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Tambah produk
if ($_POST && isset($_POST['add_product'])) {
    $product_code = $_POST['product_code'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    
    $stmt = $pdo->prepare("INSERT INTO products (product_code, name, description, category, price, stock, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$product_code, $name, $description, $category, $price, $stock, $user_id]);
}

// Hapus produk
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND created_by = ?");
    $stmt->execute([$product_id, $user_id]);
}

// Ambil semua produk user
$products = $pdo->prepare("SELECT * FROM products WHERE created_by = ? ORDER BY id DESC");
$products->execute([$user_id]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Produk</title>
</head>
<body>
    <h2>Kelola Produk</h2>
    <p><a href="dashboard.php">← Kembali ke Dashboard</a></p>
    
    <h3>Tambah Produk Baru</h3>
    <form method="POST">
        <input type="hidden" name="add_product" value="1">
        <p>Kode Produk: <input type="text" name="product_code" required></p>
        <p>Nama Produk: <input type="text" name="name" required></p>
        <p>Deskripsi: <textarea name="description"></textarea></p>
        <p>Kategori: <input type="text" name="category"></p>
        <p>Harga: <input type="number" name="price" step="0.01" required></p>
        <p>Stok: <input type="number" name="stock" required></p>
        <p><button type="submit">Tambah Produk</button></p>
    </form>
    
    <h3>Daftar Produk</h3>
    <table border="1" cellpadding="8">
        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
        <?php while ($product = $products->fetch()): ?>
        <tr>
            <td><?php echo $product['product_code']; ?></td>
            <td><?php echo $product['name']; ?></td>
            <td><?php echo $product['category']; ?></td>
            <td>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
            <td><?php echo $product['stock']; ?></td>
            <td>
                <a href="products.php?delete=<?php echo $product['id']; ?>" onclick="return confirm('Hapus produk?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>