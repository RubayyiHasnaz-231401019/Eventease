<?php
include 'db.php';
include 'notifikasi.php';
session_start();
$isLogin = isset($_SESSION['id']);
function formatRupiah($angka) {
  return "Rp " . number_format($angka, 0, ",", ".");
}

$eventsearch = $_POST['eventsearch'] ?? '';
$place_filter = $_POST['place_filter'] ?? '';
$timesearch = $_POST['timesearch'] ?? 'all';
$category = $_POST['category'] ?? 'all';
$conditions = [];
$params = [];
$paramIndex = 1;

if ($category !== 'all') {
  $conditions[] = "event_category = $" . $paramIndex++;
  $params[] = $category;
}
if (!empty($eventsearch)) {
    $conditions[] = "nama_event ILIKE $" . $paramIndex++;
    $params[] = "%$eventsearch%";
}
if (!empty($place_filter)) {
    $conditions[] = "address ILIKE $" . $paramIndex++;
    $params[] = "%$place_filter%";
}
if ($timesearch === 'today') {
  $conditions[] = "DATE(start_date) = CURRENT_DATE";
} elseif ($timesearch === 'tomorrow') {
  $conditions[] = "DATE(start_date) = CURRENT_DATE + INTERVAL '1 day'";
} elseif ($timesearch === 'week') {
  $conditions[] = "DATE(start_date) >= CURRENT_DATE AND DATE(start_date) <= CURRENT_DATE + INTERVAL '7 days'";
} elseif ($timesearch === 'month') {
  $conditions[] = "DATE(start_date) >= CURRENT_DATE AND DATE(start_date) <= CURRENT_DATE + INTERVAL '1 month'";
} elseif ($timesearch === 'offline') {
    $conditions[] = "location_type = 'offline'";
} elseif ($timesearch === 'free') {
    $conditions[] = "jenis_tiket = 'free'";
}
$whereClause = "";
if (!empty($conditions)) {
    $whereClause = " AND " . implode(' AND ', $conditions);
}
$query = "SELECT * FROM tabel_event WHERE status='published'" . $whereClause;
$result = pg_query_params($conn, $query, $params);

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
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>.Eventease</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Abhaya+Libre:wght@400;500;600;700;800&family=Shrikhand&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link rel="stylesheet" href="main.css" />
</head>

<body>

  <?php if ($isLogin) {
    include 'navbar.php';
  } else {
    include 'navbar_sign.php';
  } ?>

  <!-- Hero Section Container -->
  <div class="hero-gallery-container">
    <div class="hero-section">
      <div class="hero-content">
        <h1>
          Welcome to Eventease: Your Gateway to the Hottest Events in USU!
        </h1>
        <p>
          Level up your day with epic events, competitions, and seminars—find
          it, book it, live it. One click, all vibes. Snag your tickets now
          and flex the moments that matter! 👇🎟
        </p>
      </div>
    </div>

    <!-- Gallery Section -->
    <div class="gallery">
        <img src="img/Hero Images (2).png" alt=""/>
    </div>
  </div>

  <!-- Search Event Section -->
  <section class="search">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="search-container">
          <div class="search-bar">
            <form action="home.php" method="POST" enctype="multipart/form-data" class="search-form">
              <div class="search-group mb-3">
                <label for="eventSearch" class="search-label">Search Event</label>
                <input name="eventsearch" id="eventSearch" type="text" class="search-input" placeholder="Event Name"
                  value="<?= htmlspecialchars($_POST['eventsearch'] ?? '') ?>" />
              </div>
              <div class="search-group mb-3">
                <label for="placeInput" class="search-label">Place</label>
                <input id="placeInput" name="place_filter" type="text" class="search-input" placeholder="Enter location"
                  value="<?= htmlspecialchars($_POST['place_filter'] ?? '') ?>" />
              </div>
              <div class="search-group mb-3">
                <label for="timeSelect" class="search-label">Time</label>
                <select name="timesearch" id="timeSelect" class="search-select">
                  <option value="all" <?=($_POST['timesearch'] ?? 'all' )==='all' ? 'selected' : '' ?>>Any date</option>
                  <option value="today" <?=($_POST['timesearch'] ?? '' )==='today' ? 'selected' : '' ?>>Today</option>
                  <option value="tomorrow" <?=($_POST['timesearch'] ?? '' )==='tomorrow' ? 'selected' : '' ?>>Tomorrow
                  </option>
                  <option value="week" <?=($_POST['timesearch'] ?? '' )==='week' ? 'selected' : '' ?>>This week</option>
                  <option value="month" <?=($_POST['timesearch'] ?? '' )==='month' ? 'selected' : '' ?>>This month
                  </option>
                  <option value="offline" <?=($_POST['timesearch'] ?? '' )==='offline' ? 'selected' : '' ?>>Offline
                  </option>
                  <option value="free" <?=($_POST['timesearch'] ?? '' )==='free' ? 'selected' : '' ?>>Free</option>
                </select>
              </div>
              <div class="searchBtnContainer mb-3">
                <h3 type="submit" name="cari"><i class="fa-solid fa-magnifying-glass"></i></h3>
                <button type="submit" name="cari">FIND</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Scroll Prompt -->
  <div class="scroll-container">
    <div>Scroll to explore</div>
    <div class="scroll-icon">
      <div class="scroll-dot"></div>
    </div>
  </div>

  <!-- Popular Events in USU -->
  <div class="popular-container">
    <section class="popular-events-section">
      <h2 class="section-title">Popular Events in USU</h2>
      <form action="home.php" method="POST" enctype="multipart/form-data">
        <div class="filters">
          <button type="submit" name="category" value="all"
            class="filter-btn <?= (!isset($_POST['category']) || $_POST['category'] === 'all') ? 'active' : '' ?>">All</button>
          <button type="submit" name="category" value="music"
            class="filter-btn <?= ($_POST['category'] ?? '') === 'music' ? 'active' : '' ?>">Music</button>
          <button type="submit" name="category" value="art & culture"
            class="filter-btn <?= ($_POST['category'] ?? '') === 'art & culture' ? 'active' : '' ?>">Art &
            Culture</button>
          <button type="submit" name="category" value="food"
            class="filter-btn <?= ($_POST['category'] ?? '') === 'food' ? 'active' : '' ?>">Food & Beverage</button>
          <button type="submit" name="category" value="sports"
            class="filter-btn <?= ($_POST['category'] ?? '') === 'sports' ? 'active' : '' ?>">Sports</button>
          <button type="submit" name="category" value="entertainment"
            class="filter-btn <?= ($_POST['category'] ?? '') === 'entertainment' ? 'active' : '' ?>">Entertainment</button>
          <button type="submit" name="category" value="technology"
            class="filter-btn <?= ($_POST['category'] ?? '') === 'technology' ? 'active' : '' ?>">Techno</button>
          <button type="submit" name="category" value="learning & education"
            class="filter-btn <?= ($_POST['category'] ?? '') === 'learning & education' ? 'active' : '' ?>">Learning &
            Edu</button>
        </div>
      </form>
    </section>
  </div>
  <div class="swiper eventSwiper">
    <?php if (!$result): ?>
    <h1 style="color: white">
      <?= $result ? '$pesan' : "" ?>
    </h1>
    <?php endif; ?>

    <div class="swiper-wrapper">
      <?php 
        $card_count = 0;
        $row_count = 0;
        $slide_count = 0; 

        while ($event = pg_fetch_assoc($result)): 
            try {
                $start_date_parts = getDateParts($event['start_date']);
                $end_date_parts = getDateParts($event['end_date']);
                $SD_day = $start_date_parts['day_name'];
                $SD_month = $start_date_parts['month_name'];
                $SD_dayNumber = $start_date_parts['day_number'];
                $ED_day = $end_date_parts['day_name'];
                $ED_month = $end_date_parts['month_name'];
                $ED_dayNumber = $end_date_parts['day_number'];
            } catch (InvalidArgumentException $e) {
                error_log("Error parsing date: " . $e->getMessage());
                $SD_dayNumber = '??';
                $SD_month = '??';
            }
            $Angkaharga = $event['harga'];
            $harga = formatRupiah($Angkaharga);
            if ($card_count % 8 === 0): 
                if ($card_count > 0): ?>
    </div> <!-- Tutup swiper-slide -->
      <?php endif; ?>
    <div class="swiper-slide">
      <?php 
        $row_count = 0; 
        $slide_count++; 
            endif;
            if ($card_count % 4 === 0): 
              if ($card_count > 0 && $row_count % 2 !== 0): ?>
      </div> <!-- Tutup event row sebelumnya -->
      <?php endif; ?>
      <div class="event-row"> <!-- baris baru -->
      <?php 
        $row_count++;
          endif; ?>
      <article class="event-card">
        <img class="event-card__image" src="./uploads/<?= htmlspecialchars($event['foto_banner']) ?>" alt="" />
        <div class="event-card__content">
          <div class="event-card__container">
            <h2 class="event-card__title">
              <?= htmlspecialchars($event['nama_event']) ?>
            </h2>
            <div class="event-card__details">
              <div class="event-card__info-grid">
                <div class="event-card__date-block">
                  <p class="event-card__date">
                    <?= htmlspecialchars($SD_month) ?>
                    <span><br />
                      <?= $event['event_type'] === 'single' ? htmlspecialchars($SD_dayNumber) : "{$SD_dayNumber} - {$ED_dayNumber} " ?>
                    </span>
                  </p>
                </div>
                <div class="event-card__location-block">
                  <p class="event-card__location">
                    <?= $event['location_type'] === 'online' ? 'ONLINE/ZOOM' : htmlspecialchars($event['address']) ?>
                  </p>
                  <p class="event-card__time">
                    <?= htmlspecialchars("{$event['start_time_first']} - {$event['end_time_first']}") ?>
                  </p>
                </div>
              </div>
              <div class="event-card__price-block">
                <span class="event-card__price"><i class="fa-solid fa-ticket"></i>
                  <?= $event['jenis_tiket'] === 'free' ? 'FREE' : $harga ?>
                </span>
                <p class="event-card_unavail">Tickets Available</p>
              </div>
            </div>
          </div>
          <nav class="event-button-nav"
            onclick="window.location.href='detail-event.php?event_id=<?= $event['event_id'] ?>'">
            <ul>
              <li>
                More
                <span></span><span></span><span></span><span></span>
              </li>
            </ul>
          </nav>
        </div>
      </article>

      <?php 
            $card_count++; 
        endwhile; 
        if ($card_count % 4 !== 0): ?>
    </div> <!-- Tutup baris akhir -->
    <?php endif;
    if ($card_count % 8 !== 0): ?>
  </div> <!-- Tutup slide akhir -->
  <?php endif; ?>
  </div> <!-- swiper-wrapper terakhir -->
  </div> <!-- swiper end -->

  <!-- Event specially currated -->
  <div class="curated">
    <div class="curated__container">
      <h1 class="curated__title">Events specially curated for you!</h1>
      <p class="curated__description">
        Get event suggestions tailored to your interests! Don't let your
        favorite events slip away.
      </p>
      <a href="#" class="curated__button">
        Get Started
        <svg class="curated__button-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
          stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="5" y1="12" x2="19" y2="12"></line>
          <polyline points="12 5 19 12 12 19"></polyline>
        </svg>
      </a>
    </div>
  </div>

  <!-- Partnership -->
  <div class="partnership" id="about">
    <h1 class="text-center mb-4">
      Eventease: Make it Easy by Providing Broad Access
    </h1>
    <p class="text-center mb-5">
      Wanna level up your event? Eventease is the move! Drop those paper tix
      and embrace the digital vibe. Find it, book it, live it—all with one
      click. It's safe, it's swift, it's lit.
    </p>
    <div class="d-flex justify-content-center">
      <img src="img/Destination.png" alt="Partnership Handshake" class="img-fluid mb-4" style="max-width: 500px" />
    </div>
    <p class="text-center mb-5">
      Eventease make it easy by providing broad access for users to buy and
      sell tickets to all kinds of exciting events.
    </p>
  </div>

  <!-- New Event -->
  <div class="curated">
    <div class="curated__container">
      <h1 class="curated__title">Create a new event!</h1>
      <p class="curated__description">
        Sell tickets for your own events and share unforgettable moments with
        others!
      </p>
      <a href="#" class="curated__button" onclick="window.location.href='create.php';">
        Get Started
        <svg class="curated__button-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
          stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="5" y1="12" x2="19" y2="12"></line>
          <polyline points="12 5 19 12 12 19"></polyline>
        </svg>
      </a>
    </div>
  </div>

  <?php 
      include 'footer.php' 
    ?>

  <!-- Script Navbar -->
  <script>
    window.addEventListener("scroll", function () {
      var navbar = document.querySelector(".navbar");
      if (window.scrollY > 50) {
        navbar.classList.add("scrolled");
      } else {
        navbar.classList.remove("scrolled");
      }
    });
  </script>

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
    
    const swiper = new Swiper(".eventSwiper", {
      slidesPerView: 1,
      spaceBetween: 30,
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      
      loop: true,
      breakpoints: {
        768: {
          slidesPerView: 1,
        },
      },
    });
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const swiper = new Swiper(".eventSwiper", {
        slidesPerView: 1,
        spaceBetween: 10,
      });
    });
  </script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
</body>

</html>