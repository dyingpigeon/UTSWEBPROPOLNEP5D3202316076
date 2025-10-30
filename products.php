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
    
    $_SESSION['message'] = 'Produk berhasil ditambahkan!';
    header("Location: products.php");
    exit();
}

// Update produk
if ($_POST && isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $product_code = $_POST['product_code'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    
    $stmt = $pdo->prepare("UPDATE products SET product_code = ?, name = ?, description = ?, category = ?, price = ?, stock = ? WHERE id = ? AND created_by = ?");
    $stmt->execute([$product_code, $name, $description, $category, $price, $stock, $product_id, $user_id]);
    
    $_SESSION['message'] = 'Produk berhasil diupdate!';
    header("Location: products.php");
    exit();
}

// Hapus produk
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND created_by = ?");
    $stmt->execute([$product_id, $user_id]);
    
    $_SESSION['message'] = 'Produk berhasil dihapus!';
    header("Location: products.php");
    exit();
}

// Ambil data produk untuk edit
$edit_product = null;
if (isset($_GET['edit'])) {
    $product_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND created_by = ?");
    $stmt->execute([$product_id, $user_id]);
    $edit_product = $stmt->fetch();
}

// Ambil semua produk user
$products = $pdo->prepare("SELECT * FROM products WHERE created_by = ? ORDER BY id DESC");
$products->execute([$user_id]);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin Gudang</title>
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

        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .header h1 {
            color: #333;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header h1 i {
            color: #667eea;
        }

        .back-link {
            color: #667eea;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 15px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid transparent;
        }

        .alert-success {
            background-color: #e8f5e8;
            border-color: #c3e6cb;
            color: #155724;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 30px;
        }

        .card h3 {
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card h3 i {
            color: #667eea;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
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

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-sm {
            padding: 8px 15px;
            font-size: 12px;
        }

        .table-container {
            overflow-x: auto;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .product-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        .product-table td {
            padding: 15px;
            border-bottom: 1px solid #e1e5e9;
        }

        .product-table tr:hover {
            background-color: #f8f9fa;
        }

        .product-table tr:last-child td {
            border-bottom: none;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .stock-low {
            color: #dc3545;
            font-weight: 600;
        }

        .stock-ok {
            color: #28a745;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .product-table {
                font-size: 14px;
            }
            
            .product-table th,
            .product-table td {
                padding: 10px 8px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="dashboard.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
            <h1><i class="fas fa-boxes"></i> Kelola Produk</h1>
            <p>Tambahkan, edit, dan kelola produk di gudang Anda</p>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <!-- Form Tambah/Edit Produk -->
        <div class="card">
            <h3>
                <i class="fas <?php echo $edit_product ? 'fa-edit' : 'fa-plus'; ?>"></i>
                <?php echo $edit_product ? 'Edit Produk' : 'Tambah Produk Baru'; ?>
            </h3>
            
            <form method="POST">
                <?php if ($edit_product): ?>
                    <input type="hidden" name="update_product" value="1">
                    <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
                <?php else: ?>
                    <input type="hidden" name="add_product" value="1">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="product_code"><i class="fas fa-barcode"></i> Kode Produk</label>
                        <input type="text" 
                               id="product_code" 
                               name="product_code" 
                               class="form-control" 
                               placeholder="PRD001"
                               value="<?php echo $edit_product ? htmlspecialchars($edit_product['product_code']) : ''; ?>"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="name"><i class="fas fa-tag"></i> Nama Produk</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-control" 
                               placeholder="Nama produk"
                               value="<?php echo $edit_product ? htmlspecialchars($edit_product['name']) : ''; ?>"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category"><i class="fas fa-folder"></i> Kategori</label>
                        <input type="text" 
                               id="category" 
                               name="category" 
                               class="form-control" 
                               placeholder="Elektronik, Pakaian, dll."
                               value="<?php echo $edit_product ? htmlspecialchars($edit_product['category']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="price"><i class="fas fa-money-bill-wave"></i> Harga (Rp)</label>
                        <input type="number" 
                               id="price" 
                               name="price" 
                               class="form-control" 
                               step="0.01" 
                               min="0"
                               placeholder="100000"
                               value="<?php echo $edit_product ? $edit_product['price'] : ''; ?>"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock"><i class="fas fa-boxes"></i> Stok</label>
                        <input type="number" 
                               id="stock" 
                               name="stock" 
                               class="form-control" 
                               min="0"
                               placeholder="50"
                               value="<?php echo $edit_product ? $edit_product['stock'] : ''; ?>"
                               required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description"><i class="fas fa-align-left"></i> Deskripsi</label>
                    <textarea id="description" 
                              name="description" 
                              class="form-control" 
                              placeholder="Deskripsi produk..."><?php echo $edit_product ? htmlspecialchars($edit_product['description']) : ''; ?></textarea>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas <?php echo $edit_product ? 'fa-save' : 'fa-plus'; ?>"></i>
                        <?php echo $edit_product ? 'Update Produk' : 'Tambah Produk'; ?>
                    </button>
                    
                    <?php if ($edit_product): ?>
                        <a href="products.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Daftar Produk -->
        <div class="card">
            <h3><i class="fas fa-list"></i> Daftar Produk</h3>
            
            <div class="table-container">
                <?php if ($products->rowCount() > 0): ?>
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($product = $products->fetch()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($product['product_code']); ?></strong></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category']); ?></td>
                                <td>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                                <td>
                                    <span class="<?php echo $product['stock'] <= 5 ? 'stock-low' : 'stock-ok'; ?>">
                                        <?php echo $product['stock']; ?>
                                        <?php if ($product['stock'] <= 5): ?>
                                            <i class="fas fa-exclamation-triangle"></i>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="products.php?edit=<?php echo $product['id']; ?>" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="products.php?delete=<?php echo $product['id']; ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Yakin hapus produk <?php echo htmlspecialchars($product['name']); ?>?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <h3>Belum ada produk</h3>
                        <p>Tambahkan produk pertama Anda menggunakan form di atas</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Auto-hide alert
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.querySelector('.alert');
            if (alert) {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            }
        });
    </script>
</body>
</html>