<?php
include 'db.php';
session_start();

// Ambil ID dari parameter GET
$event_id = $_GET['event_id'] ?? null;
$user_id = $_SESSION['id'];

// Ambil data event
$query = "SELECT * FROM tabel_event WHERE event_id = $1";
$result = pg_query_params($conn, $query, [$event_id]);
$event = pg_fetch_assoc($result);

function formatRupiah($angka) {
  return "Rp " . number_format($angka, 0, ",", ".");
}
$Angkaharga = $event['harga'];
$harga = formatRupiah($Angkaharga);
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Detail Event -.Eventease</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Abhaya+Libre:wght@400;500;600;700;800&family=Shrikhand&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  <link rel="stylesheet" href="detail-event.css"/>
</head>
<body>
  <?php 
    include 'navbar.php'; 
  ?>

  <h1 class="header">
    Eventease: Your Gateway to the Hottest Events!
  </h1>
  <div class="content">
    <h2>
      <?= htmlspecialchars($event['nama_event']) ?>
    </h2>
    <div class="section">
      <img src="./uploads/<?=htmlspecialchars($event['foto_banner']) ?>" alt="Career Talk Event" class="event-image">
      <h3>Date and Time</h3>
      <?php if ($event['event_type'] == "single"): ?>
      <p><i class="fas fa-calendar-alt"></i> <?= htmlspecialchars("{$SD_day}, {$start_date}") ?></p>
      <p><i class="fas fa-clock"></i> <?= htmlspecialchars("{$event['start_time_first']} - {$event['end_time_first']} ")?></p>
      <?php else: ?>
      <p><i class="fas fa-calendar-alt"></i> <?= htmlspecialchars("{$SD_day} - {$ED_day}, {$start_date}  en  {$end_date}") ?></p>
      <p><i class="fas fa-clock"></i> <?= htmlspecialchars("{$event['start_time_sec']} - {$event['end_time_sec']}  en  {$event['start_time_sec']} - {$event['end_time_sec']} ")?></p>
      <?php endif; ?>
      <p class="add-to-calendar"><i class="fas fa-calendar-plus"></i>
        <a href="https://www.google.com/calendar/render?action=TEMPLATE&text=Roadshow+IDFest&dates=20241009T020000Z/20241010T100000Z&details=Roadshow+IDFest+di+Kota+Medan.&location=Aula+Fakultas+Ekonomi+dan+BISNIS+-+Universitas+Sumatera+Utara"> Add to Calendar</a>
      </p>
    </div>
    <div class="sectionMap">
      <h3>Location</h3>
      <?php if ($event['event_type'] == 'single'): ?>
      <p><i class="fa-solid fa-globe"></i> Online</p>
      <a href="<?= htmlspecialchars($event['online_link']) ?>"></a>
      <?php else : ?>
      <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['address'])?></p>
      <?= $gmaps_link?>
      <?php endif; ?>
    </div>
    <div class="sectionPrice">
      <h3>Ticket Information</h3>
      <p><i class="fas fa-ticket-alt"></i> <?= $event['jenis_tiket'] === 'free' ? 'FREE' : $harga ?></p>
    </div>
    <div>
      <form method="POST" enctype="multipart/form-data">
        <button type="button" class="btn buy-tickets-btn" data-bs-toggle="modal" data-bs-target="#payment1"><img src="img/tiket.png" alt="">Buy Ticket</button>
        <div class="modal fade" id="payment1" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">SELECT TICKET</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="d-flex justify-content-between pb-2 tiket_quantity">
                  <p><?= $event['jenis_tiket'] === 'free' ? 'FREE' : "PAID" ?></p>
                  <p style="padding: 0px 35px 0px 0px;">Quantity</p>
                </div>
                <div class="row jenis-tic">
                  <div class="col-warna col"></div>
                  <div class="nama-tic-col col">
                    <h3><?= $event['nama_tiket']?></h3>
                    <p id="ticket-price" data-price="<?= $event['jenis_tiket'] === 'free' ? '0' : $event['harga'] ?>"><?= $event['jenis_tiket'] === 'free' ? 'Rp 0' : $harga ?></p>
                  </div>
                  <div class="col-md-4 ms-auto d-flex justify-content-between quantity">
                    <h3 class="bi bi-dash-circle" type="button" id="decrease-btn"></h3>
                    <input type="number" value="1" min="0" id="quantity">
                    <h3 class="bi bi-plus-circle" type="button" id="increase-btn"></h3>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <div class="col d-flex totalSemen">
                  <h3>Qty: <span class="total-quantity"></span></h3>
                  <h3>Total: <span class="total-price"></span></h3>
                </div>
                <button type="button" class="btn btn-proses titleAdaBack" id="proceedButton">
                  <h1>Proceed<i class="bi bi-arrow-right"></i></h1>
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="payment2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <div class="col d-flex titleModalAdaBack">
                  <h3><i class="bi bi-arrow-left" type="button" data-bs-dismiss="modal" id="backButton"></i></h3>
                  <h1 class="modal-title fs-5" id="staticBackdropLabel">ATTENDEE DETAILS</h1>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="d-flex justify-content-between pb-2 subtitleAttendee">
                  <p>
                    <?php 
                  $nama_event = $event['nama_event'];
                  if (strlen($nama_event) >= 20) {
                      $nama_event = substr($nama_event, 0, 21) . '...';
                  } echo htmlspecialchars($nama_event); ?>
                  </p>
                  <div class="d-flex justify-content-between">
                    <p><img src="img/calender.png" alt=""></p>
                    <p style="padding-left: 8px;">
                      <?= htmlspecialchars("{$SD_day}, {$start_date}") ?>
                    </p>
                  </div>
                </div>
                <p class="standard-ticket">Standard Ticket: Ticket #1</p>
                <div class="warna-summary"></div>
                <div class="row attendee-des">
                  <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <input type="text" id="name" class="form-control" placeholder="Enter Attendee's full name">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" class="form-control" placeholder="Enter Anttendee's Email">
                    <label class="form-label" for="telepon">Phone</label>
                    <div class="form-control">
                      <div class="d-flex telp-form">
                        <select id="country-code" name="country_code">
                          <option value="+1">USA (+1)</option>
                          <option value="+60">MYS (+60)</option>
                          <option value="+62">IDN (+62)</option>
                          <option value="+62">SGP (+65)</option>
                          <option value="+62">DEU (+49)</option>
                          <option value="+62">IND (+91)</option>
                          <option value="+62">KOR (+82)</option>
                        </select>
                        <input type="number" id="no_telp" placeholder="Enter Attendee's Phone Number">
                      </div>
                    </div>
                  </div>
                </div>
                <p class="accept-terms">I accept all the <span>Terms of Service</span> and have the read <span>Privacy Police</span></p>
              </div>
              <div class="modal-footer">
                <div class="col d-flex totalSemen">
                  <h3>Qty: <span class="total-quantity"></span></h3>
                  <h3>Total: <span class="total-price"></span></h3>
                </div>
                <button type="button" class="btn btn-proses titleAdaBack" id="toPayment3Button">
                  <h1>Proceed<i class="bi bi-arrow-right"></i></h1>
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="payment3" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
          aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <div class="col d-flex titleModalAdaBack">
                  <h3><i class="bi bi-arrow-left" type="button" data-bs-dismiss="modal" id="backTo2Button"></i></h3>
                  <h1 class="modal-title fs-5" id="staticBackdropLabel">ORDER SUMMARY</h1>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="warna-summary"></div>
                <div class="ticket">
                  <h1><?= $event['nama_tiket']?></h1>
                  <div>
                    <h3 class="attendee-name" id="display-name"></h3>
                    <div class="col d-flex justify-content-between harga-email">
                      <h3 class="attendee-email" id="display-email"></h3>
                      <h3 class="ticket-price fixtotal-price"></h3>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <div class="row footer-payment3">
                  <div class="col d-flex justify-content-between">
                    <h2>Subtotal:</h2>
                    <h3 class="total-price"></h3>
                  </div>
                </div>
                <div class="row footer-payment3-tax">
                  <div class="col d-flex justify-content-between">
                    <h2>Tax:</h2>
                    <h3 class="tax-price"></h3>
                  </div>
                </div>
                <div class="row footer-payment3-total">
                  <div class="col d-flex justify-content-between">
                    <h2>Order Total:</h2>
                    <h3 class="fixtotal-price"></h3>
                  </div>
                </div>
                <button type="button" class="btn btn-proses titleAdaBack" id="toPayment4Button">
                  <h1><img class="lock" src="img/lock.png" alt="">Pay Now</h1>
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="payment4" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
          aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <div class="col d-flex titleModalAdaBack">
                  <h3><i class="bi bi-arrow-left" type="button" data-bs-dismiss="modal" id="backTo3Button"></i></h3>
                  <h1 class="modal-title fs-5" id="staticBackdropLabel">PAYMENT</h1>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body-pay">
                <div class="content-pay">
                  <div class="circle-container">
                    <div class="circle circle-4"></div>
                    <div class="circle circle-3"></div>
                    <div class="circle circle-2"></div>
                    <div class="circle circle-1">
                      <i class="fas fa-check"></i>
                    </div>
                  </div>
                  <div class="message-pay">Payment Successful</div>
                </div>
                <div class="modal-footer">
                  <button type="buttonNext" class="btn btn-proses titleAdaBack"
                    onclick="window.location.href='tickets.html';">
                    <h1>Continue</h1>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="container">
    <section class="hosted-by">
      <div class="hostprofileContainer">
        <img alt="Host image" src="<?= './uploads/'.htmlspecialchars($event['hostprofile'])?>" />
      </div>
      <div class="host-info">
        <h2><?= $event['hostname'] ?></h2>
        <div class="buttons">
          <button class="follow">
            <a style="text-decoration: none" class="follow" href="<?='https://wa.me/' . $event['host_telp']?>/">Contact</a>
          </button>
        </div>
      </div>
    </section>

    <section class="event-description">
      <p><?= htmlspecialchars($event['description'])?></p>
    </section>
  </div>

  <?php 
    include 'footer.php'; 
  ?>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="detail-event.js"></script>
</body>
</html>