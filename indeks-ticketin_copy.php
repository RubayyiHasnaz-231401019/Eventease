<?php
  include 'db.php';

  $jenis_tiket = $_POST['jenis_tiket'] ?? '';
  $nama_tiket = $_POST['nama_tiket'] ?? '';
  $harga = $_POST['harga'] ?? '';
  $stok = $_POST['stok'] ?? '';

 
  if (isset($_POST['kirim'])){
    $event_id = $_POST['event_id'];
    $sql = "INSERT INTO tickets(event_id, jenis_tiket, nama_tiket, harga, stok) VALUES ('$event_id','$jenis_tiket', '$nama_tiket', $harga, $stok)";
    $result = pg_query($conn, $sql);
    header("Location: indeks-review.html");
  }
  // echo "$sql";
  pg_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>.Eventease</title>

    <!-- Fonts -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
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
    <!-- CSS styles -->
    <link rel="stylesheet" href="indeks-ticketin.css" />

</head>
<body>
    <div class="navbar">
        <div class="logo">.Eventease</div>
        <button class="mobile-menu-btn">
          <i class="fas fa-bars"></i>
        </button>
        <nav>
          <ul>
            <li><a href="home2.html" >Home</a></li>
            <li><a href="events.html">Events</a></li>
            <li><a href="about.html">About</a></li>
            <li><a href="contact.html">Contact & Feedback</a></li>
          </ul>
  
          <div class="icons">
            <a href="createevent.html">Create Event</a>
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
                <div class="step-circle active"></div>
                <div class="step-label active">Ticketing</div>
            </div>
            <div class="connector"></div> <!-- Garis penghubung -->
            <div class="step">
                <div class="step-circle inactive"></div>
                <div class="step-label inactive">Review</div>
            </div>
        </div>
    </section>

    <section class="container2">
      <form action="indeks-ticketin_copy.php" method="POST" enctype="multipart/form-data">
        <?php
          include 'db.php';
            $ambil_id = "SELECT * FROM tabel_event order by event_id DESC LIMIT 1";
            $result = pg_query($conn, $ambil_id);
          
          while ($row = pg_fetch_row($result)) {
            echo"<input type='hidden' name='event_id' value='$row[0]'> ";
          }
        ?>
      
        <h2>What type of event are you running?</h2>
        <div class="event-type">
            <div type="radio" class="event-option selected" name="jenis_tiket" value="Ticketed" id="ticketed-event" checked>
                <i class="fas fa-ticket-alt"></i>
                <p>Ticketed Event</p>
                <p>My event requires tickets for entry</p>
                <input type="hidden">
            </div>
            <div type="radio" class="event-option" name="jenis_tiket" value="Free" id="free-event">
                <i class="fas fa-tag"></i>
                <p>Free Event</p>
                <p>I'm running a free event</p>
            </div>
        </div>
        
        <!-- Form yang akan ditampilkan/dihilangkan -->
        <div class="form-group" id="ticket-name-group">
            <label for="ticket-name">Ticket Name</label>
            <input type="text" name="nama_tiket" id="ticket-name" placeholder="Ticket Name e.g. General Admission" required>
        </div>
        <div class="form-row" id="ticket-details">
            <div class="form-group">
                <label for="total-tickets">Total Tickets for Sale</label>
                <div class="ticket-quantity" >
                    <button type="button" id="decrease">-</button>
                    <input type="number" name="stok" id="ticket-quantity" value="1" min="1" required >
                    <button type="button" id="increase">+</button>
                </div>
                <div class="form-group" id="ticket-price-group">
                    <label for="ticket-price">Ticket Price (per unit)</label>
                    <div class="price-container">
                        <span>IDR</span>
                        <input type="number" name="harga" id="ticket-price" placeholder="000">
                    </div>
            </div>                
        </div>
        </div>

        <div class="buttons"style="margin-bottom: 20px;">
          <a href="indeks-banner.html"> Go back </a>
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

    <script src="indeks-ticketin.js"></script>
</body>
</html>