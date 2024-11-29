<?php
session_start();
include 'db.php'; 

$event_id = $_SESSION['event_id'];

$jenis_tiket = $_SESSION['jenis_tiket'] ?? '';
$nama_tiket = $_SESSION['nama_tiket'] ?? '';
$harga = $_SESSION['harga'] ?? 0;
$stok = $_SESSION['stok'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $jenis_tiket = $_POST['jenis_tiket'] ?? 'ticketed';
    $_SESSION['jenis_tiket'] = $jenis_tiket;
    $nama_tiket = $_POST['nama_tiket'] ?? '';
    
    if ($jenis_tiket === 'free') {
        $harga = 0; // Tiket gratis, harga diatur ke 0
        $stok = $_POST['stok'] ?? 0; // Stok tetap dapat diatur
    } else {
        $harga = is_numeric($_POST['harga']) ? (float)$_POST['harga'] : 0; // Validasi harga hanya angka
        $stok = $_POST['stok'] ?? 0;
    }

    // Update data tiket di database
    $sql = "UPDATE tabel_event 
            SET jenis_tiket = $1, nama_tiket = $2, harga = $3, stok = $4
            WHERE event_id = $5";
    $params = array($jenis_tiket, $nama_tiket, $harga, $stok, $event_id);
    $result = pg_query_params($conn, $sql, $params);

    if ($result) {
        $_SESSION['jenis_tiket'] = $jenis_tiket;
        $_SESSION['nama_tiket'] = $nama_tiket;
        $_SESSION['harga'] = $harga;
        $_SESSION['stok'] = $stok;
        // Redirect ke halaman review
        header("Location: review.php");
        exit();
    } else {
        error_log("Query Error: " . pg_last_error($conn));
        echo "Failed to update data.";
    }
}
// Tutup koneksi database
pg_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create Event - .Eventease</title>
  <link rel="stylesheet" href="ticketin.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Abhaya+Libre:wght@400;500;600;700;800&family=Shrikhand&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.2/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
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
        <div class="step-circle inactive"></div>
        <div class="step-label inactive">Review</div>
      </div>
    </div>
  </section>

  <section class="container2">
    <h2>What type of event are you running?</h2>
    <form action="ticketin.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="jenis_tiket" id="jenis_tiket" value="<?php echo htmlspecialchars($_SESSION['jenis_tiket'] ?? 'ticketed'); ?>"/>
      <div class="event-type">
        <div class="event-option <?php echo $jenis_tiket === 'ticketed' ? 'selected' : ''; ?>" onclick="selectEventType('ticketed')">
          <img src="images/ticket.png" alt="Ticketed Event">
          <p>Ticketed Event</p>
          <p>My event requires tickets for entry</p>
        </div>
        <div class="event-option <?php echo $jenis_tiket === 'free' ? 'selected' : ''; ?>" onclick="selectEventType('free')">
          <img src="images/free.png" alt="Free Event">
          <p>Free Event</p>
          <p>I'm running a free event</p>
        </div>
      </div>
      <div class="form-group" id="ticket-name-group">
        <label for="ticket-name">Ticket Name</label>
        <input type="text" name="nama_tiket" id="ticket-name" placeholder="Ticket Name e.g. General Admission" value="<?php echo htmlspecialchars($_SESSION['nama_tiket'] ?? ''); ?>" required>
      </div>
      <div class="form-row" id="ticket-details">
        <div class="form-group">
          <label for="total-tickets">Total Tickets for Sale</label>
          <div class="ticket-quantity">
            <button type="button" id="decrease">-</button>
            <input name="stok" type="number" id="tickets-quantity" value="<?php echo htmlspecialchars($_SESSION['stok'] ?? 1); ?>" min="1" required>
            <button type="button" id="increase">+</button>
          </div>
          <div class="form-group" id="ticket-price-group">
            <label for="ticket-price">Ticket Price (per unit)</label>
            <div class="price-container">
              <span>Rp</span>
              <input name="harga" type="number" id="ticket-price" placeholder="Rp 10000"  value="<?php echo htmlspecialchars($_SESSION['harga'] ?? ''); ?>" required>
            </div>
          </div>
        </div>
      </div>
      <div class="buttons" style="margin-bottom: 20px;">
        <a href="banner.php?event_id=<?php echo htmlspecialchars($_SESSION['event_id']); ?>"> Go back </a>
        <button name="kirim" type="submit"> Save & Continue </button>
      </div>
    </form>
  </section>

  <?php 
    include 'footer.php';
  ?>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
        const jenisTiket = document.getElementById('jenis_tiket').value;
        selectEventType(jenisTiket); 
    });

    function selectEventType(type) {
        // Atur value jenis_tiket
        document.getElementById('jenis_tiket').value = type;

        // Atur visual feedback
        const options = document.querySelectorAll('.event-option');
        options.forEach(option => option.classList.remove('selected'));

        if (type === 'ticketed') {
            options[0].classList.add('selected');
            document.getElementById('ticket-price-group').style.display = 'block';
        } else if (type === 'free') {
            options[1].classList.add('selected');
            document.getElementById('ticket-price-group').style.display = 'none';
        }
    }

    // Increment/Decrement ticket quantity
    document.addEventListener('DOMContentLoaded', function () {
        const increaseBtn = document.getElementById('increase');
        const decreaseBtn = document.getElementById('decrease');
        const ticketQuantity = document.getElementById('tickets-quantity');

        increaseBtn.addEventListener('click', () => {
            ticketQuantity.value = parseInt(ticketQuantity.value) + 1;
        });

        decreaseBtn.addEventListener('click', () => {
            if (parseInt(ticketQuantity.value) > 1) {
                ticketQuantity.value = parseInt(ticketQuantity.value) - 1;
            }
        });

        ticketQuantity.addEventListener('input', () => {
            if (parseInt(ticketQuantity.value) < 1 || isNaN(parseInt(ticketQuantity.value))) {
                ticketQuantity.value = 1;
            }
        });
    });
  </script>
</body>
</html>