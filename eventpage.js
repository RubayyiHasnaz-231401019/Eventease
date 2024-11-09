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


// PUNYA POP UP
// =====================

var payment1ModalElement = document.getElementById('payment1');
var payment2ModalElement = document.getElementById('payment2');
var payment3ModalElement = document.getElementById('payment3');
var payment4ModalElement = document.getElementById('payment4');

var payment1Modal = new bootstrap.Modal(payment1ModalElement);
var payment2Modal = new bootstrap.Modal(payment2ModalElement);
var payment3Modal = new bootstrap.Modal(payment3ModalElement);
var payment4Modal = new bootstrap.Modal(payment4ModalElement);

document.getElementById('proceedButton').addEventListener('click', function () {
  payment1Modal.hide();
  payment2Modal.show();
});

document.getElementById('backButton').addEventListener('click', function () {
  payment2Modal.hide();
  payment1Modal.show();
});

document.getElementById('toPayment3Button').addEventListener('click', function () {
  payment2Modal.hide();
  payment3Modal.show();
});

document.getElementById('backTo2Button').addEventListener('click', function () {
  payment3Modal.hide();
  payment2Modal.show();
});

document.getElementById('toPayment4Button').addEventListener('click', function () {
    payment3Modal.hide();
    payment4Modal.show();
  });
  
  document.getElementById('backTo3Button').addEventListener('click', function () {
    payment4Modal.hide();
    payment3Modal.show();
  });
  


//Untuk bagian nambah angka di quantity payment1
document.addEventListener("DOMContentLoaded", function () {
  const minusButtons = document.querySelectorAll('.bi-dash-circle');
  const plusButtons = document.querySelectorAll('.bi-plus-circle');
  const quantityDisplays = document.querySelectorAll('#quantity');

  function updateQuantity(index, change) {
    const quantityDisplay = quantityDisplays[index];
    let currentQuantity = parseInt(quantityDisplay.innerText);
    currentQuantity += change;

    if (currentQuantity < 0) {
      currentQuantity = 0;
    } else if (currentQuantity > 200) {
      currentQuantity = 200;
    }

    quantityDisplay.innerText = currentQuantity;
    minusButtons[index].disabled = currentQuantity == 0;
  }
  
  minusButtons.forEach((button, index) => {
    button.addEventListener('click', function () {
      updateQuantity(index, -1);
    });
  });

  plusButtons.forEach((button, index) => {
    button.addEventListener('click', function () {
      updateQuantity(index, 1);
    });
  });
});