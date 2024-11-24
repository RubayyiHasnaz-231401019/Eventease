<?php
include 'db.php';
session_start();

$message = '';

if (isset($_SESSION['reset_email'])) {
    $email = $_SESSION['reset_email'];
} else {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $query = "UPDATE users SET password = $1 WHERE email = $2";
        $result = pg_query_params($conn, $query, array($hashed_password, $email));

        if ($result) {
            echo "<script>alert('Password successfully updated!'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Error updating password.');</script>";
        }

        // Hapus session setelah selesai untuk keamanan
        session_unset();
        session_destroy();
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
            <form action="reset.php" method="POST" style="padding-left: 15px;">
                <div class="input" style="margin-left: 22px;">
                    <div class="input-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                        <span class="toggle-password" data-target="new_password">
                            <i id="eye-icon" class="fas fa-eye" style="margin-right: 400px; margin-bottom: 280px;"></i>
                        </span>
                    </div>
                    <div class="input-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Enter confirm password" required>
                        <span class="toggle-password" data-target="confirm_password">
                            <i id="eye-icon" class="fas fa-eye" style="margin-right: 400px; margin-bottom: 120px;"></i>
                        </span>
                    </div>                    
                </div>
                <div class="buttons">
                    <button type="submit" class="create-button" style="margin-right: 17px; width: 418px;">Reset</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        window.onload = function() {
            <?php if ($message): ?>
                alert("<?= $message ?>");
                window.location.href = "login.php"; // Redirect ke halaman login setelah 2 detik
            <?php endif; ?>
        };

        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.toggle-password').forEach(function(toggle) {
                toggle.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const passwordInput = document.getElementById(targetId);
                    const eyeIcon = this.querySelector('i');

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
        });
    </script>
</body>
</html>