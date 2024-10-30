document.getElementById('increase').addEventListener('click', function() {
    var quantityInput = document.getElementById('ticket-quantity');
    quantityInput.value = parseInt(quantityInput.value) + 1;
});

document.addEventListener('DOMContentLoaded', function () {
    const ticketedEvent = document.getElementById('ticketed-event');
    const freeEvent = document.getElementById('free-event');
    const ticketNameGroup = document.getElementById('ticket-name-group');
    const ticketDetails = document.getElementById('ticket-details');
    const ticketPriceGroup = document.getElementById('ticket-price-group');

    // Fungsi untuk menampilkan form Ticketed Event
    function showTicketedEvent() {
        ticketNameGroup.style.display = 'block';  // Tampilkan nama tiket
        ticketDetails.style.display = 'flex';     // Tampilkan semua detail tiket
        ticketPriceGroup.style.display = 'block'; // Tampilkan harga tiket
        ticketedEvent.classList.add('selected');
        freeEvent.classList.remove('selected');
    }

    // Fungsi untuk menampilkan form Free Event
    function showFreeEvent() {
        ticketNameGroup.style.display = 'block';  // Tampilkan nama tiket
        ticketDetails.style.display = 'flex';     // Tetap tampilkan jumlah tiket
        ticketPriceGroup.style.display = 'none';  // Sembunyikan harga tiket
        freeEvent.classList.add('selected');
        ticketedEvent.classList.remove('selected');
    }

    // Tambahkan event listener untuk kedua opsi
    ticketedEvent.addEventListener('click', showTicketedEvent);
    freeEvent.addEventListener('click', showFreeEvent);

    // Set kondisi awal (default ke Ticketed Event)
    showTicketedEvent();
});

// Function to save input values to localStorage
function saveInputs() {
    const ticketName = document.getElementById('ticket-name').value;
    const ticketQuantity = document.getElementById('ticket-quantity').value;
    const ticketPrice = document.getElementById('ticket-price').value;

    localStorage.setItem('ticketName', ticketName);
    localStorage.setItem('ticketQuantity', ticketQuantity);
    localStorage.setItem('ticketPrice', ticketPrice);
}

