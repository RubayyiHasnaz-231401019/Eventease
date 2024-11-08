document.querySelector('.save-button button').addEventListener('click', function() {
    // Mengambil nilai dari input
    const newEmail = document.getElementById('first-name').value.trim();
    const confirmEmail = document.getElementById('last-name').value.trim();

    // Memeriksa apakah semua input terisi
    if (newEmail === '' || confirmEmail === '') {
        alert('Please fill in all fields!');
    } else if (newEmail !== confirmEmail) {
        alert('Email addresses do not match!');
    } else {
        alert('Changes saved successfully!');
    }
});

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
