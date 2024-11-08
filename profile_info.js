function loadFile(event) {
    var image = document.getElementById('profileImage');
    image.src = URL.createObjectURL(event.target.files[0]);
}
function saveChanges() {
    // Mendapatkan nilai input
    const firstName = document.getElementById('first-name').value;
    const lastName = document.getElementById('last-name').value;
    const phoneNumber = document.getElementById('phone-number').value;
    const address = document.getElementById('address').value;
    const city = document.getElementById('city').value;
  
    // Memeriksa apakah semua input sudah terisi
    if (firstName.trim() !== '' && lastName.trim() !== '' && phoneNumber.trim() !== '' && address.trim() !== '' && city.trim() !== '') {
      // Tampilkan alert
      alert('Changes saved successfully!');
    } else {
      // Tampilkan pesan error jika ada input yang kosong
      alert('Please fill in all the required fields.');
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