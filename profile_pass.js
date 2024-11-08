function saveChanges() {
    // Mendapatkan nilai input
    const currentPassword = document.getElementById('first-name').value;
    const newPassword = document.getElementById('first-name').value;
    const confirmPassword = document.getElementById('last-name').value;
  
    // Memeriksa apakah semua input sudah terisi
    if (currentPassword.trim() === '' || newPassword.trim() === '' || confirmPassword.trim() === '') {
      // Tampilkan pesan error jika ada input yang kosong
      alert('Please fill in all the required fields.');
    } else if (newPassword !== confirmPassword) {
      // Tampilkan pesan error jika password baru dan konfirmasi tidak cocok
      alert('New password and confirm password do not match!');
    } else {
      // Tampilkan alert bahwa perubahan berhasil disimpan
      alert('Changes saved successfully!');
    }
  }


  // <!-- Script Menu Mobile -->

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


// <!-- Script Bootstrap -->
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
  crossorigin="anonymous"