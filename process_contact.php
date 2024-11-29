<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = $_POST['fullName'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];

    try {
        $stmt = $pdo->prepare("INSERT INTO contacts (fullName, phone, message) VALUES (:fullName, :phone, :message)");
        $stmt->execute([
            ':fullName' => $fullName,
            ':phone' => $phone,
            ':message' => $message
        ]);
        echo "Thank you for contacting us!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
