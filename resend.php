<?php
include 'db.php';
require 'vendor/autoload.php';

date_default_timezone_set('Asia/Jakarta'); 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$config = include('email-config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    $query  = "SELECT * FROM users WHERE email = $1 AND is_verified = FALSE";
    $result = pg_query_params($conn, $query, array($email));

    if (pg_num_rows($result) > 0) {
        $otp_code = rand(100000, 999999);
        $otp_expiration = date("Y-m-d H:i:s", strtotime("+5 minutes")); // Kedaluwarsa 5 menit

        $update_query = "UPDATE users SET otp_code = $1, otp_expiration = $2 WHERE email = $3";
        pg_query_params($conn, $update_query, array($otp_code, $otp_expiration, $email));

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $config['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['SMTP_USERNAME'];
            $mail->Password = $config['SMTP_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $config['SMTP_PORT'];
            // Set pengirim dan penerima
            $mail->setFrom('no-reply@eventease.com', 'EventEase');
            $mail->addAddress($email);
            // Isi email
            $mail->isHTML(true);
            $mail->Subject = 'Resend OTP - Email Verification Code';
            $mail->Body = "Your new verification code is: <strong>$otp_code</strong>";
            // Kirim email
            $mail->send();
            echo "<script>alert('New OTP sent to your email.'); window.location.href='verify.php';</script>";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Email not found or already verified!";
    }
}
?>
