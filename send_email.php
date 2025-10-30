<?php
require 'vendor/autoload.php';
require 'env.php'; // Tambahkan ini

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendResetPasswordEmail($to_email, $to_name, $reset_link) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings - GUNAKAN ENVIRONMENT VARIABLES
        $mail->isSMTP();
        $mail->Host       = Env::get('SMTP_HOST', 'smtp.gmail.com');
        $mail->SMTPAuth   = true;
        $mail->Username   = Env::get('SMTP_USERNAME');
        $mail->Password   = Env::get('SMTP_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = Env::get('SMTP_PORT', 587);
        
        // Recipients - GUNAKAN ENVIRONMENT VARIABLES
        $mail->setFrom(
            Env::get('SMTP_FROM_EMAIL'), 
            Env::get('SMTP_FROM_NAME', 'Admin Gudang')
        );
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
        // Server settings - GUNAKAN ENVIRONMENT VARIABLES
        $mail->isSMTP();
        $mail->Host       = Env::get('SMTP_HOST', 'smtp.gmail.com');
        $mail->SMTPAuth   = true;
        $mail->Username   = Env::get('SMTP_USERNAME');
        $mail->Password   = Env::get('SMTP_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = Env::get('SMTP_PORT', 587);
        
        // Recipients - GUNAKAN ENVIRONMENT VARIABLES
        $mail->setFrom(
            Env::get('SMTP_FROM_EMAIL'), 
            Env::get('SMTP_FROM_NAME', 'Admin Gudang')
        );
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