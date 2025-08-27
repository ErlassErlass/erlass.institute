import './bootstrap';
import '../css/app.css';

// Bootstrap JS is loaded via CDN in layouts to avoid conflicts

// Import Alpine.js
import Alpine from 'alpinejs';

// Import DataTables
import 'datatables.net';
import 'datatables.net-dt';

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
    flashMessages.forEach(function(message) {
        const type = message.dataset.type || 'info';
        const text = message.textContent.trim();
        if (text) {
            showToast(text, type);
        }
        message.remove();
    });
    
    // Handle AJAX form submissions
    document.addEventListener('submit', function(e) {
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