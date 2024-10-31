function saveChanges() {
    
    const currentPassword = document.getElementById('first-name').value;
    const newPassword = document.getElementById('first-name').value;
    const confirmPassword = document.getElementById('last-name').value;
  
    
    if (currentPassword.trim() === '' || newPassword.trim() === '' || confirmPassword.trim() === '') {
 
      alert('Please fill in all the required fields.');
    } else if (newPassword !== confirmPassword) {
     
      alert('New password and confirm password do not match!');
    } else {
     
      alert('Changes saved successfully!');
    }
  }
