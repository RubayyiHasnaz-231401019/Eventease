const followButton = document.querySelector('.follow');

// Cek status dari localStorage saat halaman dimuat
if (localStorage.getItem('followStatus') === 'unfollow') {
    followButton.classList.add('unfollow');
    followButton.classList.remove('follow');
    followButton.textContent = 'Unfollow';
}

// Tambahkan event listener untuk mengubah status
followButton.addEventListener('click', function () {
    if (followButton.classList.contains('follow')) {
        followButton.classList.remove('follow');
        followButton.classList.add('unfollow');
        followButton.textContent = 'Unfollow';
        localStorage.setItem('followStatus', 'unfollow'); // Simpan status di localStorage
    } else {
        followButton.classList.remove('unfollow');
        followButton.classList.add('follow');
        followButton.textContent = '+ Follow';
        localStorage.setItem('followStatus', 'follow'); // Simpan status di localStorage
    }
});

let isAvailable = false; // Status awal tiket

    function toggleAvailability() {
        isAvailable = !isAvailable; // Toggle status tiket

        const availabilityDiv = document.getElementById('ticketAvailability');
        
        if (isAvailable) {
            availabilityDiv.textContent = "Tickets Available";
            availabilityDiv.classList.remove('unavailable');
            availabilityDiv.classList.add('available');
        } else {
            availabilityDiv.textContent = "Tickets Unavailable";
            availabilityDiv.classList.remove('available');
            availabilityDiv.classList.add('unavailable');
        }
    }

    //  Script Menu Mobile

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

  
    //  Script Bootstrap 

        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"

