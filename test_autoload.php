<?php
require __DIR__ . '/vendor/autoload.php';

echo "<h2>Testing Autoload After Composer Install</h2>";

if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "<p style='color:green'>✅ PHPMailer class found!</p>";
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        echo "<p style='color:green'>✅ PHPMailer instantiation successful!</p>";
        
        // Test basic configuration
        $mail->isSMTP();
        echo "<p style='color:green'>✅ SMTP configuration works!</p>";
        
    } catch (Exception $e) {
        echo "<p style='color:red'>❌ PHPMailer instantiation failed: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color:red'>❌ PHPMailer class NOT found!</p>";
    echo "<p>Coba jalankan: <code>composer install</code> di terminal</p>";
}
?>