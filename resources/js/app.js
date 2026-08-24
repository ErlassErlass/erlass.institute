import './bootstrap';
import '../css/app.css';
import '../css/onboarding-driver.css';
import 'bootstrap/dist/css/bootstrap.min.css';

// Import Alpine.js & Onboarding Tour
import Alpine from 'alpinejs';
import { showToast } from './utils/helpers.js';
import { initErlassOnboarding } from './onboarding/onboarding-engine.js';

// Make utilities globally available
window.showToast = showToast;
window.Alpine = Alpine;

// Initialize Alpine.js
Alpine.start();

// Initialize Onboarding Tour
document.addEventListener('DOMContentLoaded', function () {
    initErlassOnboarding();
});

// Handle AJAX form submissions globally if needed
document.addEventListener('DOMContentLoaded', function () {
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
    if (!submitBtn) return;
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