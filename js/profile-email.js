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
