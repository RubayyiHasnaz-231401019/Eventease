function loadFile(event) {
    var image = document.getElementById('profileImage');
    image.src = URL.createObjectURL(event.target.files[0]);
}
function saveChanges() {
   
    const firstName = document.getElementById('first-name').value;
    const lastName = document.getElementById('last-name').value;
    const phoneNumber = document.getElementById('phone-number').value;
    const address = document.getElementById('address').value;
    const city = document.getElementById('city').value;
  
    
    if (firstName.trim() !== '' && lastName.trim() !== '' && phoneNumber.trim() !== '' && address.trim() !== '' && city.trim() !== '') {
    
      alert('Changes saved successfully!');
    } else {
   
      alert('Please fill in all the required fields.');
    }
  }
