function saveChanges() {
    // Mendapatkan nilai input
    const currentPassword = document.getElementById('first-name').value;
    const newPassword = document.getElementById('first-name').value;
    const confirmPassword = document.getElementById('last-name').value;
  
    // Memeriksa apakah semua input sudah terisi
    if (currentPassword.trim() === '' || newPassword.trim() === '' || confirmPassword.trim() === '') {
      // Tampilkan pesan error jika ada input yang kosong
      alert('Please fill in all the required fields.');
    } else if (newPassword !== confirmPassword) {
      // Tampilkan pesan error jika password baru dan konfirmasi tidak cocok
      alert('New password and confirm password do not match!');
    } else {
      // Tampilkan alert bahwa perubahan berhasil disimpan
      alert('Changes saved successfully!');
    }
  }