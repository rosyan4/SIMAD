// resources/js/app.js
import Alpine from 'alpinejs';

// Inisialisasi Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Helper functions untuk SIMAD
window.formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(number);
};

window.confirmDelete = (message, callback) => {
    if (confirm(message || 'Apakah Anda yakin ingin menghapus?')) {
        callback();
    }
};

// Auto-hide flash messages
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const flashes = document.querySelectorAll('.flash-message');
        flashes.forEach(flash => {
            flash.style.display = 'none';
        });
    }, 5000);
});