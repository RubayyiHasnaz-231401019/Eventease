<?php
session_start();
include 'db.php';

$event_id = $_SESSION['event_id'];

$foto_banner = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Jika tidak ada file yang diunggah, tetap lanjutkan tanpa perubahan
    if (!isset($_FILES['foto_banner']) || $_FILES['foto_banner']['size'] == 0) {
        // Tidak ada file yang diunggah, lanjutkan tanpa update
        header("Location: ticketin.php");
        exit();
    }

    // Jika ada file yang diunggah, proses upload file
    if (isset($_FILES['foto_banner']) && $_FILES['foto_banner']['size'] > 0) {
        $file_name = $_FILES['foto_banner']['name'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $file_name = uniqid() . "." . $file_extension;
        $target_file = "./uploads/" . $file_name;
        // Validasi ekstensi file
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        if (!in_array($file_extension, $allowed_extensions)) {
            echo "Invalid file format. Only JPG, PNG, and GIF are allowed.";
            exit();
        }
        // Pindahkan file ke folder tujuan
        if (move_uploaded_file($_FILES["foto_banner"]["tmp_name"], $target_file)) {
            $foto_banner = $file_name;
        } else {
            echo "Failed to upload file.";
            exit();
        }
        // Update foto_banner ke database
        $sql = "UPDATE tabel_event SET foto_banner = $1 WHERE event_id = $2";
        $params = array($foto_banner, $event_id);
        $result = pg_query_params($conn, $sql, $params);

        if (!$result) {
            echo "Failed to update data: " . pg_last_error($conn);
            exit();
        }
        // Jika update berhasil, simpan foto_banner ke session dan redirect
        $_SESSION['foto_banner'] = $foto_banner;
    }
    // Redirect ke halaman ticketin setelah upload selesai
    header("Location: ticketin.php");
    exit();
}
// Ambil data foto_banner dari database untuk ditampilkan
$sql = "SELECT foto_banner FROM tabel_event WHERE event_id = $1";
$result = pg_query_params($conn, $sql, array($event_id));
if ($result && pg_num_rows($result) > 0) {
    $row = pg_fetch_assoc($result);
    $foto_banner = $row['foto_banner'];
}
// Default banner jika belum ada
$banner_path = $foto_banner ? "./uploads/" . htmlspecialchars($foto_banner) : "https://placehold.co/100x100";
// Tutup koneksi database
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
    <link rel="stylesheet" href="banner.css">
</head>
<body>

    <?php 
        include 'navbar.php';
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

    <section class="upload-container">
        <form action="banner.php" method="POST" enctype="multipart/form-data">
            <div class="upload-container">
                <h2 class="text-2xl font-medium">Upload Image<span style="color: red">*</span></h2>
                <div class="upload-box" id="upload-box">
                    <img id="preview" src="<?php echo isset($foto_banner) ? './uploads/' . htmlspecialchars($foto_banner) : 'https://placehold.co/100x100'; ?>" alt="Banner Image">
                </div>
                <div class="file-input">
                    <label for="file-upload">Choose File</label>
                    <input id="file-upload" type="file" accept="image/*" onchange="previewImage(event)" name="foto_banner"/>
                    <span id="file-name">No file chosen</span>
                </div>
                <div class="file-formats">
                    Valid file formats: JPG, GIF, PNG.
                </div>
            </div>
            <div class="buttons"style="margin-bottom: 20px;">
                <a href="create.php?event_id=<?php echo $_SESSION['event_id']; ?>"> Go back </a>
                <button name="kirim" type="submit"> Save & Continue </button>
            </div>
        </form>
    </section>

    <?php 
        include 'footer.php';
    ?>

    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('preview');
            const fileName = document.getElementById('file-name');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result; 
                    preview.style.display = 'block';
                }

                reader.readAsDataURL(input.files[0]);
                fileName.textContent = input.files[0].name;
            } else {
                preview.src = ''; 
                preview.style.display = 'none';
                fileName.textContent = 'No file chosen';
            }
        }

        document.getElementById('file-upload').addEventListener('change', function(event) {
            if (!event.target.files.length) {
                alert('Please choose a file.');
            } else {
                previewImage(event);
            }
        });
    </script>
</body>
</html>

