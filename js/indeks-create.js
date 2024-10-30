function toggleDateInput() {
    const singleEvent = document.getElementById('single-event');
    const endDateSection = document.getElementById('end-date-section');
    const dateTimeInputs = document.getElementById('date-time-inputs');

    if (singleEvent.checked) {
        endDateSection.style.display = 'none';
        dateTimeInputs.style.display = 'flex';
    } else {
        endDateSection.style.display = 'flex';
        dateTimeInputs.style.display = 'flex';
    }
}

    // Initialize Flatpickr for date inputs
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#start-date", {
            dateFormat: "d/m/Y",
            allowInput: true, // Allow manual input
        });
        flatpickr("#end-date", {
            dateFormat: "d/m/Y",
            allowInput: true,
        });
    });

    
function toggleInputFields() {
    const locationSelect = document.getElementById('event-location');
    const offlineInput = document.getElementById('offline-input');
    const onlineInput = document.getElementById('online-input');

    if (locationSelect.value === 'offline') {
        offlineInput.style.display = 'block';
        onlineInput.style.display = 'none';
    } else if (locationSelect.value === 'online') {
        onlineInput.style.display = 'block';
        offlineInput.style.display = 'none';
    } else {
        offlineInput.style.display = 'none';
        onlineInput.style.display = 'none';
    }
}

function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function(){
        var output = document.getElementById('imagePreview');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

