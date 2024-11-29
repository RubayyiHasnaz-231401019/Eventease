<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'google-config.php';

$config = include('email-config.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['feedbackType'])) {
    $feedbackType = htmlspecialchars($_POST['feedbackType']);
    $feedbackMessage = htmlspecialchars($_POST['feedbackMessage']);
    $recipientEmail = "noreply.eventease@gmail.com"; // Feedback email recipient

    if (empty($feedbackType) || empty($feedbackMessage)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $config['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['SMTP_USERNAME'];
        $mail->Password = $config['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config['SMTP_PORT'];

        $mail->setFrom($config['SMTP_USERNAME'], 'Eventease Feedback');
        $mail->addAddress($recipientEmail);
        $mail->Subject = "New Feedback: $feedbackType";
        $mail->isHTML(true);
        $mail->Body = "
            <h2>New Feedback Received</h2>
            <p><strong>Type of Feedback:</strong> $feedbackType</p>
            <p><strong>Message:</strong></p>
            <p>$feedbackMessage</p>
        ";

        $mail->send();
        echo json_encode(['status' => 'success', 'message' => 'Feedback sent successfully!']);
    } catch (Exception $e) {
        error_log($mail->ErrorInfo);
        echo json_encode(['status' => 'error', 'message' => $mail->ErrorInfo]);
    }
    exit;
  } 

    $fullName = htmlspecialchars($_POST['fullName']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);
    $recipientEmail = "noreply.eventease@gmail.com";

    if (empty($fullName) || empty($email) || empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }
    
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $config['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['SMTP_USERNAME'];
        $mail->Password = $config['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config['SMTP_PORT'];

        $mail->setFrom($config['SMTP_USERNAME'], 'Eventease CONTACT US');
        // $mail->setFrom($email, $fullName);           // Email pengirim
        $mail->addAddress($recipientEmail, 'Eventease'); // Email penerima
        $mail->Subject = "Eventease CONTACT US";
        $mail->isHTML(true);
        $mail->Body = "
            <h2>Contact Us Message</h2>
            <p><strong>Full Name:</strong> $fullName</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Message:</strong></p>
            <p>$message</p>
        ";
        
        $mail->send();
        echo "<script>alert('Thank you for contacting us! We will get back to you soon.')</script>";
    } catch (Exception $e) {
        error_log($mail->ErrorInfo);
        echo json_encode(['status' => 'error', 'message' => $mail->ErrorInfo]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Eventease - Contact Us</title>
  <!-- Fonts -->
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Abhaya+Libre:wght@400;500;600;700;800&family=Shrikhand&display=swap"
    rel="stylesheet" />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Abhaya+Libre:wght@400;500;600;700;800&family=Shrikhand&display=swap"
    rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/emailjs-com@3.2.0/dist/email.min.js"></script>
  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  <link rel="stylesheet" href="css/contact.css" />
</head>

<body>
  <!-- Pre loader -->
  <div id="preloader-wrapper">
    <div class="loading-container">
      <div class="loading-text">
        <span>.</span>
        <span>E</span>
        <span>V</span>
        <span>E</span>
        <span>N</span>
        <span>T</span>
        <span>E</span>
        <span>A</span>
        <span>S</span>
        <span>E</span>
      </div>
    </div>
  </div>

  <div class="navbar">
    <div class="logo">.Eventease</div>
    <button class="mobile-menu-btn">
      <i class="fas fa-bars"></i>
    </button>
    <nav>
      <ul>
        <li><a href="home.html">Home</a></li>
        <li><a href="events.html">Events</a></li>
        <li><a href="about.html">About</a></li>
        <li><a href="contact.html" class="active">Contact & Feedback</a></li>
      </ul>

      <div class="icons">
        <a href="createevent.html">Create Event</a>
        <a href="tickets.html"><i class="fa-solid fa-ticket"></i> Tickets</a>
        <a href="draft.html"><i class="fa-solid fa-folder"></i> Draft</a>
        <a href="profilepage.html"><i class="fa-solid fa-user"></i> Profile</a>
      </div>
    </nav>
  </div>
  
  <div class="container">
    <h1>Contact Us</h1>

    <div class="contact-grid">
      <form method="POST" class="contact-form" id="contactForm">
        <div class="form-group">
          <label for="fullName">Full Name</label>
          <input type="text" name="fullName" id="fullName" required />
        </div>

        <div class="form-group">
          <label for="phone">Email</label>
          <input type="email" name="email" id="email" required />
        </div>

        <div class="form-group">
          <label for="message">Message</label>
          <textarea id="message" name="message" required></textarea>
        </div>

        <button type="submit">Contact Us</button>
      </form>

      <div class="contact-info">
        <div class="info-item">
          <h2>Contact</h2>
          <p>eventeasee@gmail.com</p>
        </div>

        <div class="info-item">
          <h2>Based in</h2>
          <p>Medan, Indonesia</p>
        </div>

        <div class="social-links">
          <a href="https://www.instagram.com/binarybite._/profilecard/?igsh=MTh4NzVwbXQ1b2kyOA==" class="instagram">
            <i class="fa-brands fa-instagram"></i></a>
          <a href="https://wa.me/6282167256663?text=Halo%20saya%20ingin%20bertanya%20" class="whatsapp"><i
              class="fa-brands fa-whatsapp"></i>
          </a>
          <a href="#" class="x"><i class="fa-brands fa-x-twitter"></i></a>
        </div>
      </div>
    </div>
  </div>

  <div class="feedback-button" id="feedbackBtn">💭</div>

  <div class="modal-overlay" id="modalOverlay"></div>
  <div class="feedback-modal" id="feedbackModal">
    <button class="close-modal" id="closeModal">✕</button>
    <h2>Send Feedback</h2>
    <form action="" method="POST" class="contact-form" id="feedbackForm">
      <div class="form-group">
        <label for="feedbackType">Type of Feedback</label>
        <select name="feedbackType" class="feedBackForm" id="feedbackType" required>
          <option value="suggestion">Suggestion</option>
          <option value="bug">Bug Report</option>
          <option value="compliment">Compliment</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="form-group">
        <label for="feedbackMessage">Your Feedback</label>
        <textarea name="feedbackMessage" id="feedbackmessage" required></textarea>
      </div>
      <button type="submit">Submit Feedback</button>
    </form>
  </div>

  <!-- Script Preloader-->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      setTimeout(() => {
        const animationDuration = 700;
        const lastLetterDelay = 700;
        const totalDuration = animationDuration + lastLetterDelay;

        setTimeout(() => {
          const preloader = document.getElementById("preloader-wrapper");
          preloader.classList.add("hide-preloader");

          const mainContent = document.getElementById("main-content");
          mainContent.classList.add("show-content");

          setTimeout(() => {
            preloader.style.display = "none";
          }, 700);
        }, totalDuration);
      }, 700);
    });
  </script>

  <script>
    const feedbackBtn = document.getElementById("feedbackBtn");
    const feedbackModal = document.getElementById("feedbackModal");
    const modalOverlay = document.getElementById("modalOverlay");
    const closeModal = document.getElementById("closeModal");

    feedbackBtn.addEventListener("click", () => {
      feedbackModal.style.display = "block";
      modalOverlay.style.display = "block";
    });

    function closeModalFunction() {
      feedbackModal.style.display = "none";
      modalOverlay.style.display = "none";
    }

    closeModal.addEventListener("click", closeModalFunction);
    modalOverlay.addEventListener("click", closeModalFunction);

    document.getElementById('feedbackForm').addEventListener('submit', async function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      try {
        const response = await fetch('', { method: 'POST', body: formData });
        const result = await response.json();
        alert(result.message);
      } catch (error) {
        console.error(error);
        alert('Feedback submission failed.');
      }
      this.reset();
      closeModalFunction();
    });
  </script>
</body>

</html>