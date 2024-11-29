<?php
session_start();
include 'db.php';

$user_id = $_SESSION['id'];
$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : null;

// Jika tidak ada event_id di URL, gunakan sesi
if (!$event_id && isset($_SESSION['event_id'])) {
    $event_id = $_SESSION['event_id'];
}

// // Hanya validasi `event_id` jika bukan mode create
// if (!$event_id && $_SERVER['REQUEST_METHOD'] !== 'POST') {
//     echo "Parameter event_id tidak valid.";
//     exit();
// }

// Ambil data lama jika dalam mode update
if (!empty($event_id)) {
    $sql = "SELECT * FROM tabel_event WHERE event_id = $1 AND id = $2";
    $params = array($event_id, $user_id);
    $result = pg_query_params($conn, $sql, $params);

    if ($result && pg_num_rows($result) > 0) {
        $data = pg_fetch_assoc($result);

        // Simpan data lama ke variabel
        $nama_event = $data['nama_event'];
        $event_category = $data['event_category'];
        $hostname = $data['hostname'];
        $host_telp = $data['host_telp'];
        $event_type = $data['event_type'];
        $start_date = $data['start_date'];
        $start_time_first = $data['start_time_first'];
        $end_time_first = $data['end_time_first'];
        $end_date = $data['end_date'];
        $location_type = $data['location_type'];
        $address = $data['address'];
        $gmaps_link = $data['gmaps_link'];
        $online_link = $data['online_link'];
        $description = $data['description'];
        $start_time_sec = $data['start_time_sec'];
        $end_time_sec = $data['end_time_sec'];
        $hostprofile = $data['hostprofile'];
    } else {
        echo "Data tidak ditemukan.";
        exit();
    }
} else {
    // Inisialisasi nilai kosong untuk form saat insert
    $nama_event = $event_category = $hostname = $host_telp = $event_type = '';
    $start_date = $start_time_first = $end_time_first = $end_date = '';
    $location_type = $address = $gmaps_link = $online_link = $description = '';
    $start_time_sec = $end_time_sec = $hostprofile = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_event = $_POST['nama_event'] ?? null;
    $event_category = $_POST['event_category'] ?? null;
    $hostname = $_POST['hostname'] ?? null;
    $host_telp = $_POST['host_telp'] ?? null;
    $event_type = $_POST['event_type'] ?? null;
    $start_date = $_POST['start_date'] ?? null;
    $start_time_first = $_POST['start_time_first'] ?? null;
    $end_time_first = $_POST['end_time_first'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $location_type = $_POST['location_type'] ?? null;
    $address = $_POST['address'] ?? null;
    $gmaps_link = $_POST['gmaps_link'] ?? null;
    $online_link = $_POST['online_link'] ?? null;
    $description = $_POST['description'] ?? null;
    $start_time_sec = $_POST['start_time_sec'] ?? null;
    $end_time_sec = $_POST['end_time_sec'] ?? null;
    $hostprofile = $_POST['hostprofile'] ?? null;

    if ($event_type == "single") {
        $end_date = $start_date;
    }

    if ($location_type == "offline") {
        $online_link = null;
    } elseif ($location_type == "online") {
        $address = null;
        $gmaps_link = null;
    }

    // Jika tidak ada file baru yang diunggah
    if (!isset($_FILES['hostprofile']) || $_FILES['hostprofile']['size'] == 0) {
        header("Location: banner.php");
        exit();
    } else {
        $file_name = $_FILES['hostprofile']["name"];
        $ekstensi = strtolower(end(explode(".", $file_name)));
        $file_name = uniqid() . "." . $ekstensi;
        $target_file = "uploads/" . $file_name;

        // Memindahkan file ke folder 'uploads/'
        if (move_uploaded_file($_FILES["hostprofile"]["tmp_name"], $target_file)) {
            $hostprofile = $file_name;
        } else {
            echo "File gagal diupload.";
        }
    }

    if (!empty($event_id)) {
        // Update data berdasarkan event_id yang sudah ada
        $sql = "UPDATE tabel_event SET
                    nama_event = $1, event_category = $2, hostname = $3, host_telp = $4, event_type = $5,
                    start_date = $6, start_time_first = $7, end_time_first = $8, end_date = $9, location_type = $10,
                    address = $11, gmaps_link = $12, online_link = $13, description = $14, start_time_sec = $15,
                    end_time_sec = $16, hostprofile = $17
                WHERE event_id = $18 AND id = $19";
        $params = array(
            $nama_event, $event_category, $hostname, $host_telp, $event_type, $start_date, $start_time_first,
            $end_time_first, $end_date, $location_type, $address, $gmaps_link, $online_link, $description,
            $start_time_sec, $end_time_sec, $hostprofile, $event_id, $user_id
        );
    } else {
        // Insert data baru
        $sql = "INSERT INTO tabel_event (id, nama_event, event_category, hostname, host_telp, event_type, 
                                          start_date, start_time_first, end_time_first, end_date, location_type, 
                                          address, gmaps_link, online_link, description, start_time_sec, end_time_sec, hostprofile)
                VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16, $17, $18)
                RETURNING event_id";
        $params = array(
            $user_id, $nama_event, $event_category, $hostname, $host_telp, $event_type, $start_date, $start_time_first,
            $end_time_first, $end_date, $location_type, $address, $gmaps_link, $online_link, $description,
            $start_time_sec, $end_time_sec, $hostprofile
        );
    }

    $result = pg_query_params($conn, $sql, $params);

    if ($result) {
        if (empty($event_id)) {
            $inserted_event = pg_fetch_assoc($result);
            $_SESSION['event_id'] = $inserted_event['event_id']; // Simpan event_id ke sesi
        }
        header("Location: banner.php");
        exit();
    } else {
        echo "Failed to save data: " . pg_last_error($conn);
    }
}

pg_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create Event - .Eventease</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Abhaya+Libre:wght@400;500;600;700;800&family=Shrikhand&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.2/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
  <link rel="stylesheet" href="create.css" />
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
        <div class="step-circle inactive"></div>
        <div class="step-label">Banner</div>
      </div>
      <div class="connector"></div>
      <div class="step">
        <div class="step-circle inactive"></div>
        <div class="step-label inactive">Ticketing</div>
      </div>
      <div class="connector"></div>
      <div class="step">
        <div class="step-circle inactive"></div>
        <div class="step-label inactive">Review</div>
      </div>
    </div>
  </section>

  <div class="content-wrapper">
    <form action="create.php?event_id=<?php echo htmlspecialchars($event_id); ?>" method="POST" enctype="multipart/form-data">
      <section>
        <h2>Event Details</h2>
        <div class="form-group">
          <label for="event-title">Event Title <span style="color: red">*</span></label>
          <input type="text" id="event-title" name="nama_event" placeholder="Enter the name of your event" required value="<?php echo isset($nama_event) ? $nama_event : ''; ?>">
        </div>
        <div class="form-group">
          <label for="event-category">Event Category <span style="color: red">*</span></label>
          <select id="event-category" name="event_category" required>
            <option value="">Please select one</option>
            <option value="music" <?php echo (isset($event_category) && $event_category == 'music') ? 'selected' : ''; ?>>Music</option>
            <option value="art & culture" <?php echo (isset($event_category) && $event_category == 'art & culture') ? 'selected' : ''; ?>>Art & Culture</option>
            <option value="food & beverage" <?php echo (isset($event_category) && $event_category == 'food & beverage') ? 'selected' : ''; ?>>Food & Beverage</option>
            <option value="sports" <?php echo (isset($event_category) && $event_category == 'sports') ? 'selected' : ''; ?>>Sports</option>
            <option value="entertainment" <?php echo (isset($event_category) && $event_category == 'entertainment') ? 'selected' : ''; ?>>Entertainment</option>
            <option value="technology" <?php echo (isset($event_category) && $event_category == 'technology') ? 'selected' : ''; ?>>Technology</option>
            <option value="education & learning" <?php echo (isset($event_category) && $event_category == 'education & learning') ? 'selected' : ''; ?>>Education & Learning</option>
          </select>
        </div>
      </section>

      <section>
        <div class="container-pp">
          <h2>Host</h2>
          <div class="form-group">
            <label for="hostprofile">Host Profile <span class="required">*</span></label>
            <img alt="host profile" class="image-preview" id="imagePreview" src="<?php echo isset($hostprofile) && !empty($hostprofile) && file_exists('uploads/' . $hostprofile) ? 'uploads/' . htmlspecialchars($hostprofile) : 'images/eventphoto.png'; ?>">
            <input id="hostprofile" name="hostprofile" type="file" accept="image/*" onchange="previewImage(event)" />
          </div>
          <div class="form-group">
            <label for="hostname">Host Name <span class="required">*</span></label>
            <input id="hostname" name="hostname" placeholder="Enter the name of host" type="text" value="<?php echo isset($hostname) ? $hostname : ''; ?>" />
          </div>
          <div class="form-group">
            <label for="host_telp">Host WhatsApp <span class="required">*</span></label>
            <input id="host_telp" name="host_telp" placeholder="Enter the host’s WhatsApp number" type="number" value="<?php echo isset($host_telp) ? $host_telp : ''; ?>" />
          </div>
        </div>
      </section>

      <section>
        <h2>Date & Time</h2>
        <div class="form-group">
          <label>Event Type <span style="color: red">*</span></label>
          <div class="radio-group">
            <label>
              <input type="radio" name="event_type" id="single-event" value="single" <?php echo (isset($event_type) && $event_type == 'single') ? 'checked' : ''; ?> onclick="toggleDateInput()" />
              <span>Single Event</span>
            </label>
            <label>
              <input type="radio" name="event_type" id="recurring-event" value="recurring" <?php echo (isset($event_type) && $event_type == 'recurring') ? 'checked' : ''; ?> onclick="toggleDateInput()" />
              <span>Recurring Event</span>
            </label>
          </div>
        </div>
        <div class="date-time-group" id="date-time-inputs">
          <div class="form-group">
            <label for="start-date">Start Date <span style="color: red">*</span></label>
            <input type="date" id="start-date" name="start_date" required value="<?php echo isset($start_date) ? $start_date : ''; ?>" />
          </div>
          <div class="form-group">
            <label for="start-time">Start Time <span style="color: red">*</span></label>
            <input type="time" id="start-time" name="start_time_first" required value="<?php echo isset($start_time_first) ? $start_time_first : ''; ?>" />
          </div>
          <div class="form-group">
            <label for="end-time">End Time <span style="color: red">*</span></label>
            <input type="time" id="end-time" name="end_time_first" required value="<?php echo isset($end_time_first) ? $end_time_first : ''; ?>" />
          </div>
        </div>
        <div class="date-time-group" id="end-date-section" style="display: none">
          <div class="form-group">
            <label for="end-date">End Date <span style="color: red">*</span></label>
            <input type="date" id="end-date" name="end_date" value="<?php echo isset($end_date) ? $end_date : ''; ?>" />
          </div>
          <div class="form-group">
            <label for="end-start-time">Start Time <span style="color: red">*</span></label>
            <input type="time" id="end-start-time" name="start_time_sec" value="<?php echo isset($start_time_sec) ? $start_time_sec : ''; ?>" />
          </div>
          <div class="form-group">
            <label for="end-end-time">End Time <span style="color: red">*</span></label>
            <input type="time" id="end-end-time" name="end_time_sec" value="<?php echo isset($end_time_sec) ? $end_time_sec : ''; ?>" />
          </div>
        </div>
      </section>

      <section>
        <h2>Location</h2>
        <div class="form-group">
          <label>Select Location <span style="color: red">*</span></label>
          <select id="event-location" onchange="toggleInputFields()" name="location_type">
            <option value="">Please select one</option>
            <option value="offline" <?php echo (isset($location_type) && $location_type == 'offline') ? 'selected' : ''; ?>>Offline</option>
            <option value="online" <?php echo (isset($location_type) && $location_type == 'online') ? 'selected' : ''; ?>>Online</option>
          </select>
        </div>
        <div id="offline-input" style="display: none">
          <div class="form-group">
            <label for="address">Address: <span style="color: red">*</span></label>
            <input type="text" id="address" name="address" value="<?php echo isset($address) ? htmlspecialchars($address) : ''; ?>" placeholder="Example: Jl. Example No. 123"/>
          </div>
          <div class="form-group">
            <label for="google-maps-link">GMaps Link: <span style="color: red">*</span></label>
            <input type="text" name="gmaps_link" id="google-maps-link" value="<?php echo isset($gmaps_link) ? htmlspecialchars($gmaps_link) : ''; ?>" placeholder="Example: https://google/maps/example"/>
          </div>
        </div>
        <div id="online-input" style="display: none">
          <div class="form-group">
            <label for="online-platform">Online Platform Link: <span style="color: red">*</span></label>
            <input type="url" id="online-platform" name="online_link" value="<?php echo isset($online_link) ? htmlspecialchars($online_link) : ''; ?>" placeholder="Example: https://platform.example.com"/>
          </div>
        </div>
      </section>
      
      <section>
        <h2>Additional Information</h2>
        <div class="form-group">
          <label for="event-description">Event Description <span style="color: red">*</span></label>
          <textarea id="event-description" name="description" rows="5" placeholder="Describe what's special about your event & other important details."><?php echo isset($description) ? $description : ''; ?></textarea>
        </div>
      </section>

      <div class="buttons" style="margin-bottom: 20px">
        <button name="kirim" type="submit">Save & Continue</button>
      </div>
    </form>
  </div>

  <?php 
    include 'footer.php'
  ?>

  <script>
    function toggleDateInput() {
        const singleEvent = document.getElementById('single-event');
        const endDateSection = document.getElementById('end-date-section');
        const endDate = document.getElementById('end-date');
        const endStartTime = document.getElementById('end-start-time');
        const endEndTime = document.getElementById('end-end-time');

        if (singleEvent.checked) {
            endDateSection.style.display = 'none';
            endDate.required=false;
            endStartTime.required=false;
            endEndTime.required=false;
        } else {
            endDateSection.style.display = 'flex';
            endDate.required=true;
            endStartTime.required=true;
            endEndTime.required=true;
            console.log(endDate);
        }
    }

    function toggleInputFields() {
        const locationSelect = document.getElementById('event-location');
        const offlineInput = document.getElementById('offline-input');
        const onlineInput = document.getElementById('online-input');
        const address = document.getElementById('address');
        const gmapsLink = document.getElementById('google-maps-link');
        const onlineLink = document.getElementById('online-platform');

        if (locationSelect.value === 'offline') {
            offlineInput.style.display = 'block';
            onlineInput.style.display = 'none';
            address.required=true;
            gmapsLink.required=true;
            onlineLink.required=false;
            console.log(address)

        } else {
            onlineInput.style.display = 'block';
            offlineInput.style.display = 'none';
            address.required=false;
            gmapsLink.required=false;
            onlineLink.required=false;
        } 
    }

    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('imagePreview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
