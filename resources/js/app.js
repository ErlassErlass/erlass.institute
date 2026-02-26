import './bootstrap';
import '../css/app.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
import 'datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css';
import 'select2/dist/css/select2.min.css';
import 'select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css';
import "flatpickr/dist/flatpickr.min.css";

// Import Alpine.js
import Alpine from 'alpinejs';

import 'datatables.net';
import 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';

// Import Select2 and Flatpickr
import select2 from 'select2';
select2(); // Initialize Select2 functionality
import flatpickr from "flatpickr";
import { Indonesian } from "flatpickr/dist/l10n/id.js";

// Expose flatpickr globally
window.flatpickr = flatpickr;
window.flatpickrIndonesian = Indonesian;

// Global initialization function
window.initDatepickers = function () {
    // Standard Date Picker
    flatpickr(".datepicker", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d-m-Y",
        locale: Indonesian,
        allowInput: true
    });

    // Time Picker
    flatpickr(".timepicker, .time-picker", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        locale: Indonesian
    });

    // Date Picker (No Alt Input - for specific cases)
    flatpickr(".date-picker, .datepicker-basic", {
        dateFormat: "Y-m-d",
        locale: Indonesian,
        allowInput: true
    });
};

// Import custom modules
import { DataTableManager } from './modules/datatable.js';
import { FormValidator } from './modules/form-validation.js';
import './modules/ekstrakurikuler-city-filter.js';
import { showToast } from './utils/helpers.js';

// Make utilities globally available
window.showToast = showToast;
window.Alpine = Alpine;

// Initialize Alpine.js
Alpine.start();

// Initialize application when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    // Initialize DataTables only if tables with .datatable class exist
    const dataTableManager = new DataTableManager();
    if (document.querySelector('.datatable')) {
        dataTableManager.init();
    }

    // Initialize form validation
    new FormValidator();

    // Initialize Flatpickr datepickers
    window.initDatepickers();

    // Initialize Bootstrap components (if bootstrap is loaded via CDN)
    if (typeof bootstrap !== 'undefined') {
        // Initialize Bootstrap tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialize Bootstrap popovers
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }

    // Show flash messages as toasts
    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(function (message) {
        const type = message.dataset.type || 'info';
        const text = message.textContent.trim();
        if (text) {
            showToast(text, type);
        }
        message.remove();
    });

    // Handle AJAX form submissions
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (form.classList.contains('ajax-form')) {
            e.preventDefault();
            handleAjaxForm(form);
        }
    });
});

/**
 * Handle AJAX form submission
 * @param {HTMLFormElement} form - Form element to submit
 */
function handleAjaxForm(form) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

    fetch(form.action, {
        method: form.method,
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Operasi berhasil!', 'success');
                if (data.redirect) {
                    setTimeout(() => window.location.href = data.redirect, 1000);
                }
            } else {
                showToast(data.message || 'Terjadi kesalahan!', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan jaringan!', 'error');
        })
        .finally(() => {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
}

// Export for global use
window.DataTableManager = DataTableManager;