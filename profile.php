<?php
include 'db.php';
session_start();

$user_id = $_SESSION['id'];

$query = "SELECT * FROM users WHERE id = $1";
$result = pg_query_params($conn, $query, array($user_id));
$user = pg_fetch_assoc($result);

$success = '';
$error = '';

$has_password = !empty($user['password']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Bagian Update Account Information
    if (isset($_POST['update_account_info'])) {
        $first_name = $_POST['first_name'] ?? '';
        $last_name = $_POST['last_name'] ?? '';
        $phone_number = $_POST['phone_number'] ?? '';
        $address = $_POST['address'] ?? '';
        $city = $_POST['city'] ?? '';

        // Proses upload gambar (jika ada)
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $target_dir = "uploads/";
            $image_name = basename($_FILES['profile_picture']['name']);
            $target_file = $target_dir . $image_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $file_size = $_FILES['profile_picture']['size'];

            $valid_extensions = array("jpg", "jpeg", "png");
            $max_file_size = 2 * 1024 * 1024; // Maksimal 2MB

            // Validasi ukuran file
            if ($file_size > $max_file_size) {
                $error = "The profile picture is too large. Maximum size allowed is 2MB.";
            }
            // Validasi format file
            elseif (!in_array($imageFileType, $valid_extensions)) {
                $error = "Invalid photo format. Please use JPG, JPEG, or PNG.";
            }
            // Jika validasi berhasil, lakukan upload
            else {
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                    $query = "UPDATE users SET first_name = $1, last_name = $2, phone_number = $3, address = $4, city = $5, profile_picture = $6 WHERE id = $7";
                    $result = pg_query_params($conn, $query, array($first_name, $last_name, $phone_number, $address, $city, $target_file, $user_id));
                    $success = $result ? "Account information updated successfully!" : pg_last_error($conn);
                } else {
                    $error = "Error uploading file. Please try again.";
                }
            }
        } else {
            $query = "UPDATE users SET first_name = $1, last_name = $2, phone_number = $3, address = $4, city = $5 WHERE id = $6";
            $result = pg_query_params($conn, $query, array($first_name, $last_name, $phone_number, $address, $city, $user_id));
            $success = $result ? "Account information updated successfully!" : pg_last_error($conn);
        }

        // Refresh data user
        $query = "SELECT * FROM users WHERE id = $1";
        $result = pg_query_params($conn, $query, array($user_id));
        $user = pg_fetch_assoc($result);
    }

    // Bagian Change Email
    if (isset($_POST['change_email'])) {
        $new_email = $_POST['new_email'] ?? '';
        $confirm_email = $_POST['confirm_email'] ?? '';
    
        // Validasi format email
        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        }
    
        // Cek apakah email baru dan konfirmasi email cocok
        if (empty($error) && $new_email === $confirm_email) {
            // Cek apakah email sudah terdaftar
            $query = "SELECT id FROM users WHERE email = $1 AND id != $2"; 
            $result = pg_query_params($conn, $query, array($new_email, $user_id));
            
            if (pg_num_rows($result) > 0) {
                $error = "This email is already in use.";
            } else {
                // Jika email valid dan belum digunakan, update email
                $query = "UPDATE users SET email = $1 WHERE id = $2";
                $result = pg_query_params($conn, $query, array($new_email, $user_id));
                if ($result) {
                    $success = "Email updated successfully!";
                } else {
                    $error = "Error updating email: " . pg_last_error($conn);
                }
            }
        } else {
            $error = "Emails do not match!";
        }
    }
        
    // Bagian Change Password
    if (isset($_POST['change_password'])) {
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
    
        // Jika user sudah memiliki password (tanpa google_id)
        if ($has_password) {
            $current_password = $_POST['current_password'] ?? '';
    
            // Verifikasi password lama jika ada
            if (!password_verify($current_password, $user['password'])) {
                $error = "Current password is incorrect.";
            }
        }
    
        // Cek apakah password baru dan konfirmasi password cocok
        if (empty($error) && $new_password === $confirm_password) {
            if (strlen($new_password) < 8) {
                $error = "Password must be at least 8 characters long.";
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
                // Update password
                $query = "UPDATE users SET password = $1, has_password = TRUE WHERE id = $2";
                $result = pg_query_params($conn, $query, array($hashed_password, $user_id));
                if ($result) {
                    $success = "Password updated successfully!";
                } else {
                    $error = "Error updating password: " . pg_last_error($conn);
                }
            }
        } else {
            $error = empty($error) ? "Passwords do not match!" : $error;
        }
    }    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profile Settings - .Eventease</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Abhaya+Libre:wght@400;500;600;700;800&family=Shrikhand&display=swap" rel="stylesheet"/>
    <link  rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="profile.css" />
</head>
<body>
    <?php
        include 'navbar.php';
    ?>
    <div class="container mt-5">
        <div class="sidebar">
            <h2>Profile</h2>
            <ul class="nav nav-tabs" id="profileTabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#account-info" onclick="showTab('account-info')"><i class="fa fa-user"></i>  Account Information</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#change-email" onclick="showTab('change-email')"><i class="fa fa-envelope"></i>  Change Email</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#change-password" onclick="showTab('change-password')"><i class="fa fa-lock"></i>  Change Password</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#history" onclick="showTab('history')"><i class="fa fa-history"></i>  History</a>
                </li>
            </ul>
        </div>
        <div class="content">
            <div id="account-info" class="tab-content active">
                <form method="post" enctype="multipart/form-data">
                    <div class="header">
                        <h2>Account Information</h2>
                    </div>
                    <div class="profile-container">
                        <div class="profile-pic" id="profilePic">
                            <img id="profileImage" src="<?php echo $user['profile_picture'] ?? 'images/userphoto.png'; ?>" alt="Profile Picture"/>
                        </div>
                        <label class="camera-icon" for="fileInput"><img alt="Camera icon" src="images/cameraicon.png"/></label>
                        <input id="fileInput" type="file" name="profile_picture" accept="image/*" onchange="loadFile(event)"/>
                    </div>
                    <div class="form-group">
                        <h3>Profile Information</h3>
                    </div>
                    <div class="form-group">
                        <label for="first-name">First Name:</label>
                        <input id="first-name" placeholder="Enter first name" type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>"/>
                    </div>
                    <div class="form-group">
                        <label for="last-name">Last Name:</label>
                        <input id="last-name" placeholder="Enter last name" type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>"/>
                    </div>
                    <div class="form-group">
                        <h3>Contact Details</h3>
                    </div>
                    <div class="form-group">
                        <label for="phone-number">Phone Number:</label>
                        <input id="phone-number" placeholder="Enter phone number" type="text" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>"/>
                    </div>
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <input id="address" placeholder="Enter address" type="text" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>"/>
                    </div>
                    <div class="form-group">
                        <label for="city">City/Town:</label>
                        <input id="city" placeholder="Enter city/town" type="text" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>"/>
                    </div>
                    <div class="save-button" style="margin-bottom: 20px;">
                        <button onclick="saveChanges()"  name="update_account_info">Save Changes</button>
                    </div>
                </form>
            </div>
            <div id="change-email" class="tab-content" style="display:none;">
                <form method="post" enctype="multipart/form-data">
                    <div class="header">
                        <h2>Change Email</h2>
                    </div>
                    <div class="form-group" style="margin-top: 37px;">
                        <label class="block text-gray-700 mb-2">Current Email:</label>
                            <p><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="form-group">
                        <label for="first-name">New Email:</label>
                        <input id="first-name" placeholder="Enter new email" type="text" name="new_email" required/>
                    </div>
                    <div class="form-group">
                        <label for="last-name">Confirm Email:</label>
                        <input id="last-name" placeholder="Enter email again" type="text" name="confirm_email" required/>
                    </div>
                    <div class="save-button">
                        <button name="change_email">Save Changes</button>
                    </div>
                </form>
            </div>
            <div id="change-password" class="tab-content" style="display:none;">
                <form method="post" enctype="multipart/form-data">
                    <div class="header">
                        <h2 style="margin-bottom: 20px;">Change Password</h2>
                    </div>
                    <?php if ($has_password): ?>
                        <div class="form-group">
                            <label for="first-name">Current Password:</label>
                            <input id="first-name" placeholder="Enter current password" type="text" name="current_password" required/>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="first-name">New Password:</label>
                        <input id="first-name" placeholder="Enter new password" type="text" name="new_password" required/>
                    </div>
                    <div class="form-group">
                        <label for="last-name">Confirm Password:</label>
                        <input id="last-name" placeholder="Enter password again" type="text" name="confirm_password" required/>
                    </div>
                    <div class="save-button">
                        <button onclick="saveChanges()" name="change_password">Save Changes</button>
                    </div>
                </form>
            </div>
            <div id="history" class="tab-content" style="display:none;">
                <div class="header">
                    <h2>Order & Sales History</h2>
                </div>
                <div class="order-item" style="margin-top: 37px;">
                    <img alt="Placeholder image for Buy Ticket" height="60" src="https://storage.googleapis.com/a1aa/image/ojfzYpTO0axoW6NofSawTrwZJ91cQ3kNY1ciEs3gevfEqvuOB.jpg" width="60"/>
                    <div class="order-details">
                        <h3>Buy Ticket Name</h3>
                        <p>Day and Date</p>
                    </div>
                    <div class="order-price">
                        <h3>Ticket Price</h3>
                        <p>Virtual Account</p>
                    </div>
                </div>
                <div class="order-item">
                    <img alt="Placeholder image for Buy Ticket" height="60" src="https://storage.googleapis.com/a1aa/image/ojfzYpTO0axoW6NofSawTrwZJ91cQ3kNY1ciEs3gevfEqvuOB.jpg" width="60"/>
                    <div class="order-details">
                        <h3>Sales Ticket Name</h3>
                        <p>Day and Date</p>
                    </div>
                    <div class="order-price">
                        <h3>Ticket Price</h3>
                        <p>Virtual Account</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
        include 'footer.php';
    ?>  

    <?php if ($error): ?>
    <script type="text/javascript">
        alert("Error: <?php echo addslashes($error); ?>");
    </script>
    <?php endif; ?>

    <?php if ($success): ?>
    <script type="text/javascript">
        alert("Success: <?php echo addslashes($success); ?>");
    </script>
    <?php endif; ?>

    <script>

        function loadFile(event) {
        var image = document.getElementById('profileImage');
        image.src = URL.createObjectURL(event.target.files[0]);
        }
        function showTab(tabId) {
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.style.display = 'none');
            
            const links = document.querySelectorAll('.nav-link');
            links.forEach(link => link.classList.remove('active'));

            document.getElementById(tabId).style.display = 'block';
            document.querySelector(`[href="#${tabId}"]`).classList.add('active');
        }

        // Show first tab by default
        document.addEventListener("DOMContentLoaded", function() {
            showTab('account-info');
        });
    </script>
</body>
</html>