const ticketPrice =  document.querySelectorAll('ticket-price');

document.querySelectorAll('.ticket-price').forEach(el => {
    el.textContent = `Rp${(ticketPrice).toLocaleString('id-ID')}`;
  });