import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import flatpickr from 'flatpickr';

// Make Chart.js available globally
window.Chart = Chart;

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Initialize Flatpickr for date inputs
document.addEventListener('DOMContentLoaded', function() {
    flatpickr('input[type="date"]', {
        dateFormat: 'Y-m-d',
        allowInput: true
    });
});