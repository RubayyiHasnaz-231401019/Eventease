<?php
include 'db.php';
session_start();

// Ambil ID dari parameter GET
$event_id = $_GET['event_id'] ?? null;
if (!$event_id) {
    die("Event ID or Ticket ID is missing.");
}

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
      header("Location: home.php");
      exit;
  } else {
      die("Gagal memperbarui status event: " . pg_last_error($conn));
  }
}
if(isset($_POST['edit'])){
  $_SESSION['event_id'] = $event_id;
  header("Location: create.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Draft-Review - .Eventease</title>

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

    <div class="title" style="padding-top: 20px; font-size: 40px;">DRAFT EVENT</div>

  <div class="container2">
    <header class="header">
      <h1>
      <?= htmlspecialchars($event['nama_event']) ?>
      </h1>
    </header>
    <section class="image-placeholder">
      <img alt="Event image placeholder"
        src="./uploads/<?=htmlspecialchars($event['foto_banner'])?>"
      />
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
          <?= htmlspecialchars("{$SD_day} - {$ED_day}, {$start_date}  en  {$end_date}") ?>
        </p>
        <p>
          <i class="fas fa-clock icon">
          </i>
          <?= htmlspecialchars("{$event['start_time_sec']} - {$event['end_time_sec']}  en  {$event['start_time_sec']} - {$event['end_time_sec']} ")?>
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
          <img alt="Host image"
          src="<?= './uploads/'.htmlspecialchars($event['hostprofile'])?>"
          />
        </div>
        <div class="host-details">
            <p>
            <?=htmlspecialchars($event['hostname'])?>
            </p>
            <button class="contactBtn" >   
                <a href= "<?='https://wa.me/' . htmlspecialchars($event['host_telp']) ?>/">Contact</a>
            </button>
        </div>
      </div>

      <div class="section-description">
        <h3>Event Description</h3>
        <p>
        <?= htmlspecialchars($event['description']) ?>
        </p>
      </div>
    </section>
  </div>

  <form method="POST" enctype="multipart/form-data" >
  <div class="buttons" style="margin-bottom: 20px;">
    <a type="submit" name="edit"> EDIT </a>
    <button type="submit" name="kirim" > Save & Publish </button>
  </div>
  </form>
  </div>
  <?php include'footer.php'?>
  <script src="review.js"></script>
</body>

</html>