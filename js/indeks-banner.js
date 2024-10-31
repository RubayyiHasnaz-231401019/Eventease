function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('preview');
    const fileName = document.getElementById('file-name');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result; 
            preview.style.display = 'block'; 

      
            const img = new Image();
            img.src = e.target.result;
            img.onload = function() {
                const uploadBox = document.getElementById('upload-box');
                uploadBox.style.width = img.width + 'px'; 
                uploadBox.style.height = img.height + 'px'; 
            }
        }

        reader.readAsDataURL(input.files[0]); 
        fileName.textContent = input.files[0].name; 
    } else {
        preview.src = ''; 
        preview.style.display = 'none'; 
        fileName.textContent = 'No file chosen'; 
    }
}

document.getElementById('file-upload').addEventListener('change', function(event) {
    if (!event.target.files.length) {
        alert('Please choose a file.');
    } else {
        previewImage(event);
    }
});
