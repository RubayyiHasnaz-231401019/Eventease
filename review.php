<?php
include 'db.php';
session_start();

// Ambil ID dari parameter GET
$event_id = $_SESSION['event_id'] ?? null;

// Ambil data event
$query = "SELECT * FROM tabel_event WHERE event_id = $1";
$result = pg_query_params($conn, $query, [$event_id]);
$event = pg_fetch_assoc($result);

if ($event && isset($event['start_date'], $event['end_date'])) {
    $start_date = $event['start_date'];
    $end_date = $event['end_date'];
    $gmaps_link = $event['gmaps_link'];
    function getDateParts($date) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw new InvalidArgumentException("Format tidak valid: {$date}");
        }
        $timestamp = strtotime($date);
        return [
            'day_name' => date('l', $timestamp),
            'month_name' => strtoupper(date('M', $timestamp)),
            'day_number' => date('d', $timestamp)
        ];
    }

    try {
        $start_date_parts = getDateParts($start_date);
        $end_date_parts = getDateParts($end_date);

        $SD_day = $start_date_parts['day_name'];
        $SD_month = $start_date_parts['month_name'];
        $SD_dayNumber = $start_date_parts['day_number'];
        $ED_day = $end_date_parts['day_name'];
        $ED_month = $end_date_parts['month_name'];
        $ED_dayNumber = $end_date_parts['day_number'];
    } catch (InvalidArgumentException $e) {
        error_log("Error parsing dates: " . $e->getMessage());
        $SD_day = $ED_day = 'Unknown';
    }
} else {
    $SD_day = $ED_day = 'Unknown';
}
if(isset($_POST['kirim'])){
  $_SESSION['event_id'] = $event_id;
  $sql = "UPDATE tabel_event SET status = 'published' WHERE event_id = '$event_id'";
  $result = pg_query($conn, $sql);

  if ($result) {
      unset($_SESSION['event_id']);
      header("Location: home.php");

      exit;
  } else {
      die("Failed to update event status: " . pg_last_error($conn));
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create Event - .Eventease</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Abhaya+Libre:wght@400;500;600;700;800&family=Shrikhand&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.2/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  <link rel="stylesheet" href="review.css" />
</head>
<body>

  <?php 
    include 'navbar.php'
  ?>

  <section class="container">
    <div class="title">Create a New Event</div>
    <div class="progress-bar">
      <div class="step">
        <div class="step-circle active"></div>
        <div class="step-label">Create</div>
      </div>
      <div class="connector"></div>
      <div class="step">
        <div class="step-circle active"></div>
        <div class="step-label">Banner</div>
      </div>
      <div class="connector"></div>
      <div class="step">
        <div class="step-circle active"></div>
        <div class="step-label active">Ticketing</div>
      </div>
      <div class="connector"></div>
      <div class="step">
        <div class="step-circle active"></div>
        <div class="step-label active">Review</div>
      </div>
    </div>
  </section>

  <div class="container2">
    <header class="header">
      <h1>
        <?= htmlspecialchars($event['nama_event']) ?>
      </h1>
    </header>
    <section class="image-placeholder">
      <img alt="Event image placeholder" src="./uploads/<?=htmlspecialchars($event['foto_banner'])?>" />
    </section>
    <section class="section">
      <h2>
        Date and Time
      </h2>
      <?php if ($event['event_type'] == "single"): ?>
      <p>
        <i class="fas fa-calendar-alt icon">
        </i>
        <?= htmlspecialchars("{$SD_day}, {$start_date}") ?>
      </p>
      <p>
        <i class="fas fa-clock icon">
        </i>
        <?= htmlspecialchars("{$event['start_time_first']} - {$event['end_time_first']} ")?>
      </p>
      <p style="color: #800020;">
        + Add to Calendar
      </p>
      <?php else: ?>
      <p>
        <i class="fas fa-calendar-alt icon">
        </i>
        <?= htmlspecialchars("{$SD_day} - {$ED_day}, {$start_date}  end  {$end_date}") ?>
      </p>
      <p>
        <i class="fas fa-clock icon">
        </i>
        <?= htmlspecialchars("{$event['start_time_sec']} - {$event['end_time_sec']}  end  {$event['start_time_sec']} - {$event['end_time_sec']} ")?>
      </p>
      <p style="color: #800020;">
        + Add to Calendar
      </p>
      <?php endif; ?>
    </section>
    <section class="section">
      <h2>
        Location
      </h2>
      <?php if ($event['location_type'] == 'online'): ?>
      <p>
        <i class="fa-solid fa-globe"></i>
        <?= htmlspecialchars($event['online_link']) ?>
      </p>
      <?php else: ?>
      <p>
        <i class="fas fa-map-marker-alt icon">
        </i>
        <?= htmlspecialchars($event['address']) ?>
      </p>
      <div class="map-placeholder">
        <!-- <?= htmlspecialchars($event['gmaps_link'])?> -->
        <!-- <iframe src="<?htmlspecialchars($event['gmaps_link'])?>" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe> -->
        <?= $gmaps_link ?>
      </div>
      <?php endif; ?>
    </section>
    <section class="section">
      <h3>
        Ticket Information
      </h3>
      <p style="width : 20px" class="d-flex">
      <p class="ticket-price">
        <?= htmlspecialchars(" Ticket Price : {$event['harga']}")?>
      </p>
      </img>
    </section>
    <section class="section">
      <div class="host-info">
        <div class="hostprofileContainer">
          <img alt="Host image" src="<?= './uploads/'.htmlspecialchars($event['hostprofile'])?>" />
        </div>
        <div class="host-details">
          <p>
            <?=htmlspecialchars($event['hostname'])?>
          </p>
          <button class="contactBtn">
            <a href="<?='https://wa.me/' . htmlspecialchars($event['host_telp']) ?>/">Contact</a>
          </button>
        </div>
      </div>

      <div class="section-description">
        <h3>Event Description</h3>
        <p>
          <?= nl2br(htmlspecialchars($event['description'], ENT_QUOTES, 'UTF-8')) ?>
        </p>
      </div>
    </section>
  </div>

  <form method="POST" enctype="multipart/form-data">
    <div class="buttons" style="margin-bottom: 20px;">
      <a
        href="ticketin.php?event_id=<?php echo htmlspecialchars($_SESSION['event_id']); ?>&jenis_tiket=<?php echo htmlspecialchars($_SESSION['jenis_tiket']); ?>">
        Go Back </a>
      <a href="draft.php"> Save for Later </a>
      <button type="submit" name="kirim"> Save & Publish </button>
    </div>
  </form>
  </div>

  <script>
    fetch('review.php')
      .then(response => response.json())
      .then(data => {
        // Ambil semua elemen dengan class ticket-price
        const ticketPriceElements = document.querySelectorAll('.ticket-price');
        
        // Masukkan data ke setiap elemen
        ticketPriceElements.forEach((el, index) => {
          const price = data[index]?.harga_tiket; // Ambil harga berdasarkan indeks
          if (price !== undefined) {
            el.textContent = `Rp${parseInt(price).toLocaleString('id-ID')}`;
          } else {
            el.textContent = "Data tidak tersedia";
          }
        });
      })
      .catch(error => {
        console.error('Error fetching data:', error);
      });
  </script>
</body>
=======
<?php
  include 'db.php';
  session_start();
  $event_id = $_SESSION['event_id'];
  $jenis_tiket = $_SESSION['jenis_tiket'];
  $nama_event = $_SESSION['nama_event'];
  $event_category = $_SESSION['event_category'] ?? null;
  $hostname = $_SESSION['hostname'] ?? null;
  $host_telp = $_SESSION['host_telp'] ?? null;
  $hostprofile = $_SESSION['hostprofile'] ?? null;
  $event_type = $_SESSION['event_type'] ?? null;
  $start_date = $_SESSION['start_date'] ?? null;
  $start_time_first = $_SESSION['start_time_first'] ?? null;
  $end_time_first = $_SESSION['end_time_first'] ?? null;
  $end_date = $_SESSION['end_date'] ?? null;
  $location_type = $_SESSION['location-type'] ?? null;
  $address = $_SESSION['address'] ?? null;
  $gmaps_link = $_SESSION['gmaps_link'] ?? null;
  $online_link = $_SESSION['online_link'] ?? null;
  $description = $_SESSION['decription'] ?? null;
  $start_time_sec = $_SESSION['start_time_sec'] ?? null;
  $end_time_sec = $_SESSION['end_time_sec'] ?? null;
  $foto_banner = $_SESSION['foto_banner'] ?? null;

  $jenis_tiket = $_SESSION['jenis_tiket'] ?? null;
  $nama_tiket = $_SESSION['nama_tiket'] ?? null;
  $harga = $_SESSION['harga'] ?? null;
  $stok = $_SESSION['stok'] ?? null;

  if (isset($_POST['kirim'])) {
    $sql = "UPDATE tabel_event 
    SET foto_banner = '$foto_banner'
    WHERE event_id = '$event_id'";

    $result = pg_query($conn, $sql);
    if ($result) {
        header("Location: home2.php");
    } else {
        echo "Gagal memperbarui data.";
    }

    $_SESSION['nama_event'] = $nama_event;
    $_SESSION['event_category'] = $event_category;
    $_SESSION['hostname'] = $hostname;
    $_SESSION['host_telp'] = $host_telp;
    $_SESSION['hostprofile'] = $hostprofile;
    $_SESSION['event_type'] = $event_type;
    $_SESSION['start_date'] = $start_date;
    $_SESSION['start_time_first'] = $start_time_first;
    $_SESSION['end_time_first'] = $end_time_first;
    $_SESSION['end_date'] = $end_date;
    $_SESSION['location-type'] = $location_type;
    $_SESSION['address'] = $address;
    $_SESSION['gmaps_link'] = $gmaps_link;
    $_SESSION['online_link'] = $online_link;
    $_SESSION['decription'] = $description;
    $_SESSION['start_time_sec'] = $start_time_sec;
    $_SESSION['end_time_sec'] = $end_time_sec;  
    $_SESSION['foto_banner'] = $foto_banner;
    $_SESSION['jenis_tiket'] = $jenis_tiket;
    $_SESSION['jenis_tiket'] = $jenis_tiket;
    $_SESSION['nama_tiket'] = $nama_tiket;
    $_SESSION['harga'] = $harga;
    $_SESSION['stok'] = $stok;
  }

function getDateParts($date) {
  list($year, $month, $day) = explode('-', $date); 
  $year = (int) $year;
  $month = (int) $month;
  $day = (int) $day;
  $timestamp = mktime(0, 0, 0, $month, $day, $year); 
  return [
      'day_name' => date('l', $timestamp),       
      'month_name' => strtoupper(date('M', $timestamp)),
      'day_number' => date('d', $timestamp) 
  ];
}
$start_date = getDateParts($start_date);
$end_date = getDateParts($start_date);

$SD_day = $start_date('day_name');
$SD_month = $start_date('month_name');
$SD_dayNumber = $start_date('dayNumber');
$ED_day= $end_date('day_name');
$ED_month = $end_date('month_name');
$ED_dayNumber = $end_date('dayNumber');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>.Eventease</title>

  <!-- CSS styles -->
  <link rel="stylesheet" href="review.css" />

  <!-- Fonts -->
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Abhaya+Libre:wght@400;500;600;700;800&family=Shrikhand&display=swap"
    rel="stylesheet" />

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.2/css/bootstrap.min.css" />
  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
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
        <a href="createevent.html">Create Event</a>
        <a href="tickets.html"><i class="fa-solid fa-ticket"></i> Tickets</a>
        <a href="draft.php"><i class="fa-solid fa-folder"></i> Draft</a>
        <a href="profilepage.html"><i class="fa-solid fa-user"></i> Profile</a>
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
        <div class="step-circle active"></div>
        <div class="step-label active">Review</div>
      </div>
    </div>
  </section>


  <div class="container2">
    <header class="header">
      <h1>
      <?= htmlspecialchars($nama_event) ?>
      </h1>
    </header>
    <section class="image-placeholder">
      <img alt="Event image placeholder"
        src="./uploads/<?=htmlspecialchars($foto_banner)?>"
      />
    </section>
    <section class="section">
      <h2>
        Date and Time
      </h2>
      <?php if ($event_type == "single"): ?>
        <p>
          <i class="fas fa-calendar-alt icon">
          </i>
          <?= htmlspecialchars("{$SD_day}, {$start_date}") ?>
        </p>
        <p>
          <i class="fas fa-clock icon">
          </i>
          <?= htmlspecialchars("{$start_time_first} - {$end_time_first} ")?>
        </p>
        <p style="color: #800020;">
          + Add to Calendar
        </p>
      <?php else: ?>
        <p>
          <i class="fas fa-calendar-alt icon">
          </i>
          <?= htmlspecialchars("{$SD_day} - {$ED_day}, {$start_date} en {$end_date}") ?>
        </p>
        <p>
          <i class="fas fa-clock icon">
          </i>
          <?= htmlspecialchars("{$start_time_sec} - {$end_time_sec} ")?>
        </p>
        <p style="color: #800020;">
          + Add to Calendar
        </p>
      <?php endif; ?>
    </section>
    <section class="section">
      <h2>
        Location
      </h2>
      <?php if ($event_type == 'single'): ?>
        <p>
          <i class="fa-solid fa-globe"></i>
          <?= htmlspecialchars($online_link) ?>
        </p>
      <?php else: ?>
        <p>
          <i class="fas fa-map-marker-alt icon">
          </i>
          <?= htmlspecialchars($address) ?>
        </p>
        <div class="map-placeholder">
          <?= htmlspecialchars($gmaps_link)?>
        </div>
      <?php endif; ?>
    </section>
    <section class="section">
      <h2>
        Ticket Information
      </h2>
      <p class="ticket-price">
        <i class="fas fa-ticket-alt icon">
        </i>      
        Ticket Price: <?= htmlspecialchars($harga)?>
      </p>
    </section>
    <section class="section">
      <div class="host-info">
        <div class="hostprofileContainer">
          <img alt="Host image"
          src="<?= './uploads/'.htmlspecialchars($hostprofile)?>"
          />
        </div>
        <div class="host-details">
          <p>
          <?=htmlspecialchars($hostname)?>
          </p>
          <div class="buttons">
            <a class="follow"href= "<?='https://wa.me/' . htmlspecialchars($host_telp) ?>">
              Contact
            </a>
          </div>
        </div>
      </div>

      <div class="section-description">
        <h3>Event Description</h3>

        <p>
        <?= htmlspecialchars($description) ?>
        </p>
      </div>
    </section>
  </div>

  <div class="buttons" style="margin-bottom: 20px;">
    <a href="indeks-ticketin.html"> Go Back </a>
    <a href="draft.php"> Save for Later </a>
    <button type="submit" name="kirim"> Save & Continue </button>
  </div>
  </div>
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
            <a href="https://www.instagram.com/binarybite._/profilecard/?igsh=MTh4NzVwbXQ1b2kyOA=="
              class="instagram">Instagram <i class="fa-brands fa-instagram"></i></a>
            <a href="https://wa.me/6282167256663?text=Halo%20saya%20ingin%20bertanya%20" class="whatsapp">
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
  <script src="review.js"></script>
</body>
</html>