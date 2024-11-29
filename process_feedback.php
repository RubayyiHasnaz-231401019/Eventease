<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $feedbackType = $_POST['feedbackType'];
    $feedbackMessage = $_POST['feedbackMessage'];

    try {
        $stmt = $pdo->prepare("INSERT INTO feedbacks (feedbackType, feedbackMessage) VALUES (:feedbackType, :feedbackMessage)");
        $stmt->execute([
            ':feedbackType' => $feedbackType,
            ':feedbackMessage' => $feedbackMessage
        ]);
        echo "Thank you for your feedback!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
