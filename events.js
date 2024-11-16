// Fungsi untuk memfilter event cards
function filterEvents() {
    const selectedCategories = [];
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox:checked');
    
    // Debug: Log kategori yang dipilih
    console.log('Checkbox yang dicentang:', categoryCheckboxes.length);
    
    // Mengumpulkan kategori yang dipilih
    categoryCheckboxes.forEach(checkbox => {
        selectedCategories.push(checkbox.getAttribute('data-category'));
    });
    console.log('Kategori yang dipilih:', selectedCategories);
    
    const eventCards = document.querySelectorAll('.event-card');
    console.log('Jumlah event cards:', eventCards.length);
    
    // Memfilter cards
    eventCards.forEach(card => {
        // Ambil kategori dari card, jika tidak ada berikan array kosong
        const cardCategories = card.getAttribute('data-categories')?.split(',') || [];
        console.log('Kategori card:', cardCategories);
        
        if (selectedCategories.length === 0) {
            // Jika tidak ada kategori yang dipilih, tampilkan semua cards
            card.style.display = 'block';
        } else {
            // Cek apakah card memiliki salah satu dari kategori yang dipilih
            const shouldShow = cardCategories.some(category => 
                selectedCategories.includes(category)
            );
            card.style.display = shouldShow ? 'block' : 'none';
            console.log('Card ditampilkan:', shouldShow);
        }
    });
}

// Menambahkan event listener ke setiap checkbox
const checkboxes = document.querySelectorAll('.category-checkbox');
checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', filterEvents);
});

// Inisialisasi kategori untuk setiap card
const eventCards = document.querySelectorAll('.event-card');
eventCards.forEach((card, index) => {
    let categories;
    if (index === 0) {
        categories = 'music';
    } else if (index === 1) {
        categories = 'Entertainment';
    } else if (index === 2) {
        categories = 'education-learning';
    } else if (index === 3) {
        categories = 'Art & Culture';
    } else if (index === 4) {
        categories = 'Food & Beverage';
    } else if (index === 5) {
        categories = 'Sports';
    } else {
        categories = 'Others';
    }
    card.setAttribute('data-categories', categories);
    console.log(`Card ${index + 1} kategori:`, categories);
});


// Panggil filterEvents sekali untuk menginisialisasi tampilan
filterEvents();
console.log('Kategori yang dipilih:', selectedCategories);
console.log('Kategori card:', cardCategories);
