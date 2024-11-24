<?php
session_start();

if (isset($_POST['kirim'])) {
    $_SESSION['form_data'] = [
        'nama_event' => $_POST['nama_event'] ?? '',
        'event_category' => $_POST['event_category'] ?? '',
        'hostName' => $_POST['hostName'] ?? '',
        'hostWhatsapp' => $_POST['hostWhatsapp'] ?? '',
        'event_type' => $_POST['event_type'] ?? '',
        'start_date' => $_POST['start_date'] ?? '',
        'start_time_first' => $_POST['start_time_first'] ?? '',
        'end_time_first' => $_POST['end_time_first'] ?? '',
        'end_date' => ($_POST['event_type'] === 'single') ? $_POST['start_date'] : ($_POST['end_date'] ?? ''),
        'location_type' => $_POST['location_type'] ?? '',
        'address' => $_POST['address'] ?? '',
        'gmaps_link' => $_POST['gmaps_link'] ?? '',
        'online_link' => $_POST['online_link'] ?? '',
        'description' => $_POST['description'] ?? '',
    ];

    // Jika ada file yang diunggah, simpan di sesi
    if (isset($_FILES['hostprofile']) && $_FILES['hostprofile']['size'] !== 0) {
        $file_name = $_FILES['hostprofile']["name"];
        $ekstensi = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $file_name = uniqid() . "." . $ekstensi;
        $target_file = "./uploads/" . $file_name;

        if (move_uploaded_file($_FILES["hostprofile"]["tmp_name"], $target_file)) {
            $_SESSION['form_data']['hostprofile'] = $file_name;
        } else {
            $_SESSION['form_data']['hostprofile'] = null;
        }
    }

    // Redirect ke halaman berikutnya
    header("Location: indeks-banner_copy.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>.Eventease</title>

    <!-- CSS styles -->
    <link rel="stylesheet" href="indeks-create.css" />
  
    <!-- Fonts -->
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Abhaya+Libre:wght@400;500;600;700;800&family=Shrikhand&display=swap"
      rel="stylesheet"
    />
    
    <!-- Bootstrap CSS -->
    <link
      rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.2/css/bootstrap.min.css"
    />
    <!-- icon -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    />
    <!-- Include Flatpickr CSS -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"
    />
  </head>
  <body>
    <!-- Navbar Section -->
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
        <div class="connector"></div>
        <!-- Garis penghubung -->
        <div class="step">
          <div class="step-circle inactive"></div>
          <div class="step-label">Banner</div>
        </div>
        <div class="connector"></div>
        <!-- Garis penghubung -->
        <div class="step">
          <div class="step-circle inactive"></div>
          <div class="step-label inactive">Ticketing</div>
        </div>
        <div class="connector"></div>
        <!-- Garis penghubung -->
        <div class="step">
          <div class="step-circle inactive"></div>
          <div class="step-label inactive">Review</div>
        </div>
      </div>
    </section>

    <div class="content-wrapper">
      <form action="createevent_copy.php" method="POST" enctype="multipart/form-data">
        <section>
          <h2>Event Details</h2>
          <div class="form-group">
            <label for="event-title"
              >Event Title <span style="color: red">*</span></label
            >
            <input type="text" id="event-title" name="nama_event" placeholder="Enter the name of your event" required>
          </div>
          <div class="form-group">
            <label for="event-category"
              >Event Category <span style="color: red">*</span></label
            >
            <select id="event-category" name="event_category" required>
              <option>Please select one</option>
              <option value="music">Music</option>
              <option value="art & culture">Art & Culture</option>
              <option value="food & beverage">Food & Beverage</option>
              <option value="sports">Sports</option>
              <option value="entertainment">Entertainment</option>
              <option value="technology">Technology</option>
              <option value="education & learning">Education & Learning</option>
            </select>
          </div>
        </section>

        <section>
          <div class="container-pp">
            <h2>Host</h2>
            <div class="form-group">
              <label for="hostprofile">
                Host Profile <span class="required">*</span></label
              >
              <img
                alt="Placeholder image for host profile"
                class="image-preview"
                id="imagePreview"
                src="img/askrindo.jpg"
              >
              <input
                id="hostprofile"
                name="hostprofile"
                type="file"
                accept="image/*"
                onchange="previewImage(event)"
              />            
            </div>
            <div class="form-group">
              <label for="hostName"
                >Host Name<span class="required">*</span></label
              >
              <input
                id="hostName"
                name="hostName"
                placeholder="Enter the name of host"
                type="text"
              />
            </div>
            <div class="form-group">
              <label for="hostWhatsapp"
                >Host WhatsApp<span class="required">*</span></label
              >
              <input
                id="hostWhatsapp"
                name="hostWhatsapp"
                placeholder="Enter the host’s WhatsApp number"
                type="tel"
              />
            </div>
          </div>
        </section>

        <section>
          <h2>Date & Time</h2>
          <div class="form-group">
            <label>Event Type <span style="color: red">*</span></label>
            <div class="radio-group" >
              <label>
                <input
                  type="radio"
                  name="event_type"
                  id="single-event"
                  value= "single"
                  checked
                  onclick="toggleDateInput()"
                />
                <span>Single Event</span>
              </label>
              <label>
                <input
                  type="radio"
                  name="event_type"
                  id="recurring-event"
                  value="recurring"
                  onclick="toggleDateInput()"
                />
                <span>Recurring Event</span>
              </label>
            </div>
          </div>

          <div class="date-time-group" id="date-time-inputs">
            <div class="form-group">
              <label for="start-date"
                >Start Date <span style="color: red">*</span></label
              >
              <div style="position: relative">
                <span
                  class="fa fa-calendar"
                  style="position: absolute; left: 10px; top: 10px"
                  onclick="document.getElementById('start-date')._flatpickr.open();"
                ></span>
                <input
                  type="date"
                  id="start-date"
                  style="padding-left: 30px"
                  name = "start_date"
                  required
                />
              </div>
            </div>
            <div class="form-group">
              <label for="start-time"
                >Start Time <span style="color: red">*</span></label
              >
              <div style="position: relative">
                <span
                  class="fa fa-clock"
                  style="position: absolute; left: 10px; top: 10px"
                ></span>
                <input
                  type="time"
                  id="start-time"
                  name = "start_time_first"
                  placeholder="12:00 AM"
                  style="padding-left: 30px"
                  required
                />
              </div>
            </div>
            <div class="form-group">
              <label for="end-time"
                >End Time <span style="color: red">*</span></label
              >
              <div style="position: relative">
                <span
                  class="fa fa-clock"
                  style="position: absolute; left: 10px; top: 10px"
                ></span>
                <input
                  type="time"
                  id="end-time"
                  name = "end_time_first"
                  placeholder="12:00 AM"
                  style="padding-left: 30px"
                  required
                />
              </div>
            </div>
          </div>

          <div
            class="date-time-group"
            id="end-date-section"
            style="display: none"
          >
            <div class="form-group">
              <label for="end-date"
                >End Date <span style="color: red">*</span></label
              >
              <div style="position: relative">
                <span
                  class="fa fa-calendar"
                  style="position: absolute; left: 10px; top: 10px"
                  onclick="document.getElementById('end-date')._flatpickr.open();"
                ></span>
                <input
                  type="date"
                  id="end-date"
                  name = "end_date"
                  style="padding-left: 30px"
                  
                  />
              </div>
            </div>
            <div class="form-group">
              <label for="end-start-time"
                >Start Time <span style="color: red">*</span></label
              >
              <div style="position: relative">
                <span
                  class="fa fa-clock"
                  style="position: absolute; left: 10px; top: 10px"
                ></span>
                <input
                  type="time"
                  id="end-start-time"
                  name="start_time_sec"
                  placeholder="12:00 AM"
                  style="padding-left: 30px"
                  
                />
              </div>
            </div>
            <div class="form-group">
              <label for="end-end-time"
                >End Time <span style="color: red">*</span></label
              >
              <div style="position: relative">
                <span
                  class="fa fa-clock"
                  style="position: absolute; left: 10px; top: 10px"
                ></span>
                <input
                  type="time"
                  id="end-end-time"
                  name = "end_time_sec"
                  placeholder="12:00 AM"
                  style="padding-left: 30px"
                  
                />
              </div>
            </div>
          </div>
        </section>

        <section>
          <h2>Location</h2>
          <div class="form-group">
            <label>Select Location <span style="color: red">*</span></label>
            <select id="event-location" onchange="toggleInputFields()" name="location_type">
              <option value="">Please select one</option>
              <option value="offline">Offline</option>
              <option value="online">Online</option>
            </select>
          </div>

          <div id="offline-input" style="display: none">
            <div class="form-group">
              <label for="address"
                >Address: <span style="color: red">*</span></label
              >
              <input
                type="text"
                id="address"
                name = "address"
                placeholder="Example: Jl. Example No. 123"
              />
            </div>
            <div class="form-group">
              <label for="google-maps-link"
                >GMaps Link: <span style="color: red">*</span></label
              >
              <input
                type="text"
                name = "gmaps_link"
                id="google-maps-link"
                placeholder="Example: https://goo.gl/maps/example"
              />
            </div>
          </div>

          <div id="online-input" style="display: none">
            <div class="form-group">
              <label for="online-platform"
                >Online Platform Link: <span style="color: red">*</span></label
              >
              <input
                type="url"
                id="online-platform"
                name = "online_link"
                placeholder="Example: https://platform.example.com"
              />
            </div>
          </div>
        </section>
        <h2>Additional Information</h2>
        <div class="form-group">
          <label for="event-description"
            >Event Description <span style="color: red">*</span></label
          >
          <textarea
            id="event-description"
            name= "description"
            rows="5"
            placeholder="Describe what's special about your event & other important details."
          ></textarea>
        </div>

        <div class="buttons" style="margin-bottom: 20px">
          <button name="kirim" type="submit">
            Save & Continue
          </button>
        </div>
      </form>
    </div>

    <script src="indeks-create.js"></script>
    <!-- Include Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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
  </body>
</html>
