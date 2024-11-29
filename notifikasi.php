<?php
include 'db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Pastikan PHPMailer terinstall via Composer
$config = include('email-config.php');

$query_new_events = " SELECT * FROM tabel_event 
    WHERE status = 'published' AND created_at >= NOW() - INTERVAL '1 DAY'";
$result_new_events = pg_query($conn, $query_new_events);

if (!$result_new_events) {
    die("Query failed: " . pg_last_error());
}

// Cek apakah ada event baru
$new_events = [];
while ($row = pg_fetch_assoc($result_new_events)) {
    $new_events[] = $row;
}

// Ambil email semua pengguna yang terdaftar
$query_registered_users = "SELECT email FROM users";
$result_users = pg_query($conn, $query_registered_users);

$emails = [];
while ($row = pg_fetch_assoc($result_users)) {
    $emails[] = $row['email'];
}

// Siapkan email dengan PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = $config['SMTP_HOST'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['SMTP_USERNAME'];
    $mail->Password = $config['SMTP_PASSWORD'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $config['SMTP_PORT'];

    $mail->setFrom($config['SMTP_USERNAME'], 'Eventease Notifications');

    // Kirimkan email ke semua pengguna
    foreach ($emails as $email) {
        $mail->addAddress($email);
    }

    $mail->Subject = "New Events Published!";
    $mail->isHTML(true);

    // Buat isi email berdasarkan event baru
    $event_list = "";
    foreach ($new_events as $event) {
        $event_list .= "
            <h2>{$event['nama_event']}</h2>
            <h3>{$event['description']}</h3>
            <h5><em>Published at: {$event['created_at']}</em></h5>
            <hr>
        ";
    }

    $mail->Body = "
        <h1>New Events on Eventease!</h1>
        <p>Check out the latest events published in the last 24 hours:</p>
        $event_list
        <h2>Visit our website for more details!</h2>
    ";

    $mail->send();
    // echo "Emails sent successfully to registered users.\n";
} catch (Exception $e) {
    echo "Failed to send email: " . $mail->ErrorInfo . "\n";
}
