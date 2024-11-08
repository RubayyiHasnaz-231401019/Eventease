<?php 
session_start();
include 'db.php';

date_default_timezone_set('Asia/Jakarta'); // Sesuaikan dengan timezone lokal

$otpMessage = '';
$emailMessage = '';

// Ambil email dari session
if (!isset($_SESSION['email'])) {
    die("Email not set in session. Please go back and enter your email.");
}
$email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['otp_code']) && is_array($_POST['otp_code'])) {
    // Gabungkan kode OTP yang diinput
    $otp_code = implode('', $_POST['otp_code']);

    $query = "SELECT * FROM users WHERE email = $1 AND otp_code = $2 AND is_verified = FALSE AND otp_expiration >= NOW()";
    $result = pg_query_params($conn, $query, array($email, $otp_code));

    if (pg_num_rows($result) > 0) {
        // Jika OTP valid, update status verifikasi
        $update_query = "UPDATE users SET is_verified = TRUE, otp_code = NULL, otp_expiration = NULL WHERE email = $1";
        $update_result = pg_query_params($conn, $update_query, array($email));
        echo "<script>alert('Email verified successfully!'); window.location.href='login.php';</script>";
    } else {
        // Jika OTP tidak valid atau sudah expired
        $otpMessage = "Invalid OTP or OTP has expired!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - EventEase</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Modal styling */
        body {
            font-family: 'Poppins', sans-serif;
            background: radial-gradient(circle at 80% 40%, #591234, #2a1a3e, #000000);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: #ffffff;
            width: 300px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .modal-header {
            font-size: 1.5rem;
            font-weight: bold;
            color: #BF266F;
            margin-bottom: 10px;
        }
        .otp-container {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }
        .otp-input {
            width: 40px;
            height: 40px;
            font-size: 1.5rem;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .verify-btn {
            background: linear-gradient(to right, #BF266F, #591234);
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
            font-weight: 600;
        }
        .resend-otp {
            text-align: center;
            letter-spacing: 0.3px;
            font-family: 'Poppins', sans-serif;
            margin-top: 15px;
            font-size: 0.9em;
            color: #636363;
        }
        .resend-otp a {
            letter-spacing: 0.3px;
            font-weight: 500;
            font-family: 'Poppins', sans-serif;
            color: rgba(191, 38, 111, 1);
            text-decoration: none;
        }
        .resend-otp a :hover {
            color: #BF266F;
            text-decoration: underline;
        }

    </style>
</head>
<body>
    <!-- Modal Overlay -->
    <div class="modal-overlay" id="otpModal">
        <div class="modal-content">
            <div class="modal-header">Verify Your Email</div>
            <p style="color: #636363;">Enter the code sent to your email.</p>
            <form action="verify.php" method="POST">
                <div class="otp-container">
                    <input type="text" name="otp_code[]" maxlength="1" class="otp-input" autofocus>
                    <input type="text" name="otp_code[]" maxlength="1" class="otp-input">
                    <input type="text" name="otp_code[]" maxlength="1" class="otp-input">
                    <input type="text" name="otp_code[]" maxlength="1" class="otp-input">
                    <input type="text" name="otp_code[]" maxlength="1" class="otp-input">
                    <input type="text" name="otp_code[]" maxlength="1" class="otp-input">
                </div>
                <button type="submit" class="verify-btn">Verify</button>
                <div class="message" style="color: #721c24;"><?= $otpMessage ?></div>
            </form>
            <p class="resend-otp">
                Haven't received the OTP? 
                <form action="resend.php" method="POST" style="display:inline;">
                    <input type="hidden" name="email" value="<?= $email ?>">
                    <button type="submit" name="resend_otp" style="background:none; border:none; color:rgba(191, 38, 111, 1); cursor:pointer;">Resend OTP</button>
                </form>
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('otpModal').style.display = 'flex';
        });

        const otpInputs = document.querySelectorAll('.otp-input');
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (input.value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });
        });
    </script>

</body>
</html>