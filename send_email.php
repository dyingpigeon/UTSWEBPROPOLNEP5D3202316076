<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendResetPasswordEmail($to_email, $to_name, $reset_link) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'daheknigg@gmail.com'; // Email Gmail Anda
        $mail->Password   = 'vxrcsajlnpddtksv';     // App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Recipients - PERBAIKI: setFrom harus sama dengan username
        $mail->setFrom('daheknigg@gmail.com', 'Admin Gudang');
        $mail->addAddress($to_email, $to_name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Reset Password - Sistem Admin Gudang';
        
        $email_body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; }
                .button { background-color: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; }
                .footer { margin-top: 20px; font-size: 12px; color: #666; }
                .code { background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>Reset Password</h2>
                <p>Halo <strong>$to_name</strong>,</p>
                <p>Anda menerima email ini karena meminta reset password untuk akun Admin Gudang.</p>
                <p>Klik tombol di bawah untuk reset password:</p>
                <p style='text-align: center;'>
                    <a href='$reset_link' class='button'>Reset Password Sekarang</a>
                </p>
                <p>Atau copy link berikut di browser:</p>
                <div class='code'>$reset_link</div>
                <p><strong>Link ini berlaku selama 24 jam.</strong></p>
                <div class='footer'>
                    <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->Body = $email_body;
        $mail->AltBody = "Reset Password untuk $to_name. Klik: $reset_link";
        
        $mail->send();
        return ['success' => true, 'message' => 'Email berhasil dikirim'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => "Gagal mengirim email: {$mail->ErrorInfo}"];
    }
}

function sendActivationEmail($to_email, $to_name, $activation_link) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'daheknigg@gmail.com'; // Email Gmail Anda
        $mail->Password   = 'vxrcsajlnpddtksv';     // App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Recipients - PERBAIKI: setFrom harus sama dengan username
        $mail->setFrom('daheknigg@gmail.com', 'Admin Gudang');
        $mail->addAddress($to_email, $to_name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Aktivasi Akun - Sistem Admin Gudang';
        
        $email_body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; }
                .button { background-color: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; }
                .footer { margin-top: 20px; font-size: 12px; color: #666; }
                .code { background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>Selamat Datang!</h2>
                <p>Halo <strong>$to_name</strong>,</p>
                <p>Terima kasih telah mendaftar di Sistem Admin Gudang.</p>
                <p>Akun Anda sudah berhasil dibuat. Klik tombol di bawah untuk mengaktifkan akun:</p>
                <p style='text-align: center;'>
                    <a href='$activation_link' class='button'>Aktivasi Akun Sekarang</a>
                </p>
                <p>Atau copy link berikut di browser:</p>
                <div class='code'>$activation_link</div>
                <div class='footer'>
                    <p>Jika Anda tidak mendaftar, abaikan email ini.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->Body = $email_body;
        $mail->AltBody = "Aktivasi akun untuk $to_name. Klik: $activation_link";
        
        $mail->send();
        return ['success' => true, 'message' => 'Email aktivasi berhasil dikirim'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => "Gagal mengirim email aktivasi: {$mail->ErrorInfo}"];
    }
}
?>