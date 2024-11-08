<?php
include 'db.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Cek apakah email ada di database
    $query = "SELECT * FROM users WHERE email = $1";
    $result = pg_query_params($conn, $query, array($email));

    if (pg_num_rows($result) > 0) {
        // Buat link reset password
        $reset_link = "http://localhost/eventease/reset.php?email=" . urlencode($email);

        session_start();
        $_SESSION['reset_email'] = $email;
        
        // Inisialisasi PHPMailer
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;
            $mail->Username = 'khairnnisasrg08@gmail.com'; 
            $mail->Password = 'rhfarvrokohnvmoh'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            $mail->setFrom('no-reply@eventease.com', 'EventEase.com');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password';
            $mail->Body = "Click this link to reset your password: <a href='$reset_link'>$reset_link</a>";

            $mail->send();
            echo " <script>alert('Reset password link has been sent to your email!');</script>";
        } catch (Exception $e) {
            echo " <script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');</script>";
        }
    } else {
        echo "<script>alert('Email not found!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - EventEase</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Abhaya+Libre:wght@800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="left-side">
            <img src="images/logo.png" alt="EventEase Logo">
            <h1>.Eventease:</h1>
            <h2>Your</h2>
            <h2>Gateway to</h2>
            <h2>the Hottest</h2>
            <h2>Events!</h2>
            <p>Find it, book it, live it. One<br>click, all vibes.</p>
        </div>
        <div class="right-side">
            <h2>Reset Password</h2>    
            <form action="request_reset.php" method="POST">
                <div class="input" style="margin-left: 22px;">
                    <div class="input-group" style="width: 100%; margin-left: 18px">
                        <label for="email">E-mail Address</label>
                        <input type="email" id="email" name="email" placeholder="Enter your e-mail" style="width: 88%;" required>
                    </div>                   
                </div>
                <div class="buttons" style="gap: 200px; margin-top: 25px;">
                    <button type="button" class="back-button" onclick="window.location.href='login.php';">Back</button>
                    <button type="submit" class="login-button" style="width: 115px;">Send Reset</button>
                </div>
                <p class="login-account">Don't have an account? <a href="register.php">Create Account</a></p>
            </form>
        </div>
    </div>
    <script>
        window.onload = function() {
            <?php if ($message): ?>
                alert("<?= $message ?>");
                window.location.href = "login.php"; 
            <?php endif; ?>
        };

        document.addEventListener("DOMContentLoaded", function() {
            document.querySelector('.toggle-password').addEventListener('click', function() {
                const passwordInput = document.querySelector('.input-password');
                const eyeIcon = document.getElementById('eye-icon');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                }
            });
        });
    </script>
</body>
</html>
