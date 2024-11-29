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

document.addEventListener('DOMContentLoaded', () => {
  const proceedButton = document.getElementById('toPayment3Button');

  proceedButton.addEventListener('click', (event) => {
    event.preventDefault();
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.querySelector('input[type="number"]');
    if (!nameInput.value.trim() || !emailInput.value.trim() || !phoneInput.value.trim()) {
      alert('Please fill in all required fields.');
      return;
    }
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(emailInput.value)) {
      alert('Please enter a valid email address.');
      return;
    }
    payment2Modal.hide(); 
    payment3Modal.show(); 
  });
});

document.getElementById('toPayment3Button').addEventListener('click', function () {
  // Tangkap inputan user
  const name = document.getElementById('name').value;
  const email = document.getElementById('email').value;
  const countryCode = document.getElementById('country-code').value;
  const phone = document.getElementById('no_telp').value;

  // Gabungkan kode negara dengan nomor telepon
  const fullPhone = `${countryCode} ${phone}`;

  // Tampilkan inputan user di tempat yang sesuai
  document.getElementById('display-name').textContent = `${name}`;
  document.getElementById('display-email').textContent = `${email}`;
  document.getElementById('display-phone').textContent = `${fullPhone}`;
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

//BAGIAN QUANTITY POP UP
const ticketPriceElement = document.getElementById('ticket-price');
const ticketPrice = parseFloat(ticketPriceElement.getAttribute('data-price')) || 0;

const quantityInput = document.getElementById('quantity');
const increaseBtn = document.getElementById('increase-btn');
const decreaseBtn = document.getElementById('decrease-btn');

function updateDisplay() {
  const quantity = parseInt(quantityInput.value);
  document.querySelectorAll('.total-quantity').forEach(el => {
    el.textContent = quantity;
  });

  document.querySelectorAll('.total-price').forEach(el => {
    el.textContent = `Rp${(ticketPrice * quantity).toLocaleString('id-ID')}.00`;
  });

  document.querySelectorAll('.tax-price').forEach(el => {
    el.textContent = `Rp${(ticketPrice * 0.05).toLocaleString('id-ID')}.00`;
  });
  document.querySelectorAll('.fixtotal-price').forEach(el => {
    el.textContent = `Rp${((ticketPrice * quantity) + (ticketPrice * 0.05)).toLocaleString('id-ID')}.00`;
  });
}

increaseBtn.addEventListener('click', () => {
  quantityInput.value = parseInt(quantityInput.value) + 1;
  updateDisplay();
});

decreaseBtn.addEventListener('click', () => {
  if (parseInt(quantityInput.value) > 1) {
    quantityInput.value = parseInt(quantityInput.value) - 1;
    updateDisplay();
  }
});

quantityInput.addEventListener('input', () => {
  if (parseInt(quantityInput.value) < 1) {
    quantityInput.value = 1;
  }
  updateDisplay();
});
updateDisplay();