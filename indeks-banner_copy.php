<?php
include 'db.php';

session_start();

$event_id = $_SESSION['event_id'] ?? null;
if (!$event_id) {
    die("Event ID tidak ditemukan.");
}

if (isset($_FILES['foto_banner']) && $_FILES['foto_banner']['size'] !== 0) {
    $file_name = $_FILES['foto_banner']["name"];
    $ekstensi = strtolower(end(explode(".", $file_name)));
    $file_name = uniqid() . "." . $ekstensi;
    $target_file = "./uploads/" . $file_name;

    if (move_uploaded_file($_FILES["foto_banner"]["tmp_name"], $target_file)) {
        $foto_banner = $file_name;
    }
}

if (isset($_POST['kirim'])) {
    // $sql = "UPDATE tabel_event 
    //         SET foto_banner = '$foto_banner'";

    $sql = "UPDATE tabel_event 
    SET foto_banner = '$foto_banner'
    WHERE event_id = '$event_id'";

    $result = pg_query($conn, $sql);
    if ($result) {
        header("Location: indeks-ticketin_copy.php");
    } else {
        echo "Gagal memperbarui data.";
    }
  echo "$sql";
}

pg_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>.Eventease</title>

    <!-- CSS styles -->
    <link rel="stylesheet" href="indeks-banner.css">

    <!-- Fonts -->
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Abhaya+Libre:wght@400;500;600;700;800&family=Shrikhand&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Abhaya+Libre:wght@400;500;600;700;800&family=Shrikhand&display=swap"
      rel="stylesheet"
    />
    <!-- Bootstrap CSS -->
    <link
      rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.2/css/bootstrap.min.css"
    />
    <!-- Icons -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    />
</head>
<body>
   
    <div class="navbar">
        <div class="logo">.Eventease</div>
        <button class="mobile-menu-btn">
          <i class="fas fa-bars"></i>
        </button>
        <nav>
          <ul>
            <li><a href="home2.html">Home</a></li>
            <li><a href="events.html">Events</a></li>
            <li><a href="about.html">About</a></li>
            <li><a href="contact.html">Contact & Feedback</a></li>
          </ul>
  
          <div class="icons">
            <a href="indeks-create.html">Create Event</a>
            <a href="tickets.html"><i class="fa-solid fa-ticket"></i> Tickets</a>
            <a href="draft.html"><i class="fa-solid fa-folder"></i> Draft</a>
            <a href="profilepage.html"
              ><i class="fa-solid fa-user"></i> Profile</a
            >
          </div>
        </nav>
      </div>
  
      <!-- Script Menu Mobile -->
      <script>
        const mobileMenuBtn = document.querySelector(".mobile-menu-btn");
        const nav = document.querySelector("nav");
  
        mobileMenuBtn.addEventListener("click", () => {
          nav.classList.toggle("active");
        });
  
        document.addEventListener("click", (e) => {
          if (!nav.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
            nav.classList.remove("active");
          }
        });
      </script>
    <section class="container">
        <div class="title">Create a New Event</div>
        <div class="progress-bar">
            <div class="step">
                <div class="step-circle active"></div>
                <div class="step-label">Create</div>
            </div>
            <div class="connector"></div> <!-- Garis penghubung -->
            <div class="step">
                <div class="step-circle active"></div>
                <div class="step-label">Banner</div>
            </div>
            <div class="connector"></div> <!-- Garis penghubung -->
            <div class="step">
                <div class="step-circle inactive"></div>
                <div class="step-label inactive">Ticketing</div>
            </div>
            <div class="connector"></div> <!-- Garis penghubung -->
            <div class="step">
                <div class="step-circle inactive"></div>
                <div class="step-label inactive">Review</div>
            </div>
        </div>
    </section>

    <section class="upload-container">
      <form action="indeks-banner_copy.php" method="POST" enctype="multipart/form-data">
        <div class="upload-container">
            <h2 class="text-2xl font-medium">Upload Image<span style="color: red">*</span></h2>
            <div class="upload-box" id="upload-box">
                <img id="preview" src="https://placehold.co/100x100" alt="Placeholder image icon representing image upload">
            </div>
            <div class="file-input">
                <label for="file-upload">Choose File</label>
                <input id="file-upload" type="file" accept="image/*" onchange="previewImage(event)" name="foto_banner" required/>
                <span id="file-name">No file chosen</span>
            </div>
            <div class="file-formats">
                Valid file formats: JPG, GIF, PNG.
            </div>
        </div>

        <div class="buttons"style="margin-bottom: 20px;">
          <a href="createevent_copy.php"> Go back </a>
          <button name="kirim" type="submit"> Save & Continue </button>
        </div>
      </form>
    </section>
    <footer class="footer">
        <div class="footer-container">
          <div class="help-banner">
            <h3>Do you need help?</h3>
            <a href="contact.html" class="consultation-btn">
              Get consultation
              <span>→</span>
            </a>
          </div>
  
          <div class="footer-content">
            <div class="info-section">
              <h4>INFO</h4>
              <ul class="footer-links">
                <li><a href="home2.html">Home</a></li>
                <li><a href="events.html">Events</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="contact.html">Contact</a></li>
              </ul>
  
              <div class="social-links">
                <a
                  href="https://www.instagram.com/binarybite._/profilecard/?igsh=MTh4NzVwbXQ1b2kyOA=="
                  class="instagram"
                  >Instagram <i class="fa-brands fa-instagram"></i
                ></a>
                <a
                  href="https://wa.me/6282167256663?text=Halo%20saya%20ingin%20bertanya%20"
                  class="whatsapp"
                >
                  Whatsapp <i class="fa-brands fa-whatsapp"></i>
                </a>
                <a href="#" class="x">X<i class="fa-brands fa-x-twitter"></i></a>
              </div>
            </div>
  
            <div class="contact-info">
              <div class="logo">.Eventease</div>
              <div>+62 821 6725 6663</div>
              <div>eventease@gmail.com</div>
            </div>
          </div>
  
          <div class="copyright">© 2024 — Copyright</div>
        </div>
      </footer>
    <script src="indeks-banner.js"></script>
</body>
</html>

