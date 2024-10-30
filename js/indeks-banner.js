function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('preview');
    const fileName = document.getElementById('file-name');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result; // Set the image source to the uploaded file
            preview.style.display = 'block'; // Ensure the image is displayed

            // Adjust the upload box size to the image size
            const img = new Image();
            img.src = e.target.result;
            img.onload = function() {
                const uploadBox = document.getElementById('upload-box');
                uploadBox.style.width = img.width + 'px'; // Set width based on image
                uploadBox.style.height = img.height + 'px'; // Set height based on image
            }
        }

        reader.readAsDataURL(input.files[0]); // Read the file as a data URL
        fileName.textContent = input.files[0].name; // Show the name of the file
    } else {
        preview.src = ''; // Reset to empty
        preview.style.display = 'none'; // Hide the image
        fileName.textContent = 'No file chosen'; // Reset the file name
    }
}

document.getElementById('file-upload').addEventListener('change', function(event) {
    if (!event.target.files.length) {
        alert('Please choose a file.');
    } else {
        previewImage(event);
    }
});
