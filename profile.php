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
        echo "<p style='color:green'>Password berhasil diubah!</p>";
    } else {
        echo "<p style='color:red'>Password saat ini salah!</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profil Saya</title>
</head>
<body>
    <h2>Profil Saya</h2>
    <p><a href="dashboard.php">← Kembali ke Dashboard</a></p>
    
    <h3>Informasi Profil</h3>
    <p>Nama: <?php echo $user['full_name']; ?></p>
    <p>Email: <?php echo $user['email']; ?></p>
    <p>Status: <?php echo $user['status']; ?></p>
    
    <h3>Ubah Password</h3>
    <form method="POST">
        <input type="hidden" name="change_password" value="1">
        <p>Password Saat Ini: <input type="password" name="current_password" required></p>
        <p>Password Baru: <input type="password" name="new_password" required></p>
        <p><button type="submit">Ubah Password</button></p>
    </form>
</body>
</html>