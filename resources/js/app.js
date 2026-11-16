import './bootstrap';
import Alpine from 'alpinejs'
window.Alpine = Alpine
Alpine.start()

document.addEventListener('click', (event) => {
    const link = event.target.closest('a[href]');
    const table = link?.closest('[data-table-container]');

    if (table) {
        table.dataset.loading = 'true';
    }
});

document.addEventListener('submit', (event) => {
    const table = event.target.closest('[data-table-container]');

    if (table) {
        table.dataset.loading = 'true';
    }
});
