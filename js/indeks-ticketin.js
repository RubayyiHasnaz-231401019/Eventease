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

   
    function showTicketedEvent() {
        ticketNameGroup.style.display = 'block';  
        ticketDetails.style.display = 'flex';     
        ticketPriceGroup.style.display = 'block'; 
        ticketedEvent.classList.add('selected');
        freeEvent.classList.remove('selected');
    }

    // Fungsi untuk menampilkan form Free Event
    function showFreeEvent() {
        ticketNameGroup.style.display = 'block';  
        ticketDetails.style.display = 'flex';    
        ticketPriceGroup.style.display = 'none';  
        freeEvent.classList.add('selected');
        ticketedEvent.classList.remove('selected');
    }

    
    ticketedEvent.addEventListener('click', showTicketedEvent);
    freeEvent.addEventListener('click', showFreeEvent);

   
    showTicketedEvent();
});


function saveInputs() {
    const ticketName = document.getElementById('ticket-name').value;
    const ticketQuantity = document.getElementById('ticket-quantity').value;
    const ticketPrice = document.getElementById('ticket-price').value;

    localStorage.setItem('ticketName', ticketName);
    localStorage.setItem('ticketQuantity', ticketQuantity);
    localStorage.setItem('ticketPrice', ticketPrice);
}

