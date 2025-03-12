import './bootstrap';
import '../css/app.css';


import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// resources/js/app.js


// Initialize DataTables
document.addEventListener('DOMContentLoaded', function () {
    $('.datatable').DataTable();
});

document.addEventListener('DOMContentLoaded', function () {
    $('.datatable').DataTable({
        "pageLength": 10, // Items per page
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "language": {
            "search": "Cari:",
            "paginate": {
                "previous": "Sebelumnya",
                "next": "Selanjutnya"
            }
        },
        "order": [[0, 'asc']], // Sort by first column ascending
    });
});