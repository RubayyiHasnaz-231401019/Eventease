<?php
session_start();
include 'db.php'; 

require_once 'google-config.php';

$email = "";
$password = "";
$email_error = "";
$password_error = "";

// Proses login form biasa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($email)) {
        $email_error = "Email is required!";
    }

    if (empty($password)) {
        $password_error = "Password is required!";
    }

    if (empty($email_error) && empty($password_error)) {
        $query = "SELECT * FROM users WHERE email = $1";
        $result = pg_query_params($conn, $query, array($email));

        if ($result && pg_num_rows($result) > 0) {
            $row = pg_fetch_assoc($result);
            if (password_verify($password, $row['password'])) {
                $_SESSION['id'] = $row['id'];
                header("Location: index-2.php");
                exit();
            } else {
                $password_error = "Incorrect password!";
            }
        } else {
            $email_error = "User not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - EventEase</title>
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
            <h2>Sign In</h2>
            <div class="button-container">
                <a href="<?php echo $client->createAuthUrl(); ?>" class="google-button">
                    <img src="images/google.png" alt="Google Logo">
                    Sign in with Google
                </a>
            </div>
            <div class="line-or">
                <hr class="left-line">
                <span>OR</span>
                <hr class="right-line">
            </div>
            <form method="POST">
                <div class="input" style="margin-left: 40px;">
                    <div class="input-group">
                        <label for="email">E-mail Address</label>
                        <input type="email" id="email" name="email" placeholder="Enter your e-mail" style="width: 91.5%;" value="<?= htmlspecialchars($email) ?>">
                        <?php if (!empty($email_error)): ?>
                            <div class="message" style="color: #721c24;"><?= htmlspecialchars($email_error) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="input-group">
                        <div class="label-container">
                            <label for="password">Password</label>
                            <a href="request.php" class="forgot-password" style="margin-right: 42px;">Forgot Password?</a>
                        </div>
                        <div class="password-container">
                            <input type="password" id="password" class="input-password" placeholder="Enter password" name="password" style="width: 91.5%;">
                            <span class="toggle-password">
                                <i id="eye-icon" class="fas fa-eye" style="margin-right: 39px"></i>
                            </span>
                        </div>
                        <?php if (!empty($password_error)): ?>
                            <div class="message" style="color: #721c24;"><?= htmlspecialchars($password_error) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="buttons" style="gap: 180px; margin-top: 25px; margin-left:2px;">
                    <button type="button" class="back-button">Back</button>
                    <button type="submit" class="login-button" style="width: 115px;">Sign In</button>
                </div>
                <p class="login-account">Don't have an account? <a href="register.php">Create Account</a></p>
            </form>
        </div>
    </div>

    <script>
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