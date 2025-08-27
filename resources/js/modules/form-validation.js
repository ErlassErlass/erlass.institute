/**
 * Form validation utilities for WebApperlass
 * Provides client-side validation and form handling
 */

export class FormValidator {
    constructor() {
        this.init();
    }

    init() {
        this.setupBootstrapValidation();
        this.setupCustomValidators();
        this.setupFormSubmissionHandlers();
    }

    /**
     * Setup Bootstrap form validation
     */
    setupBootstrapValidation() {
        document.addEventListener('DOMContentLoaded', () => {
            // Fetch all forms with needs-validation class
            const forms = document.querySelectorAll('.needs-validation');

            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            });
        });
    }

    /**
     * Setup custom validation rules
     */
    setupCustomValidators() {
        // Date validation (not in future for reports)
        this.setupDateValidation();
        
        // Time validation (end time after start time)
        this.setupTimeValidation();
        
        // File validation (size and type)
        this.setupFileValidation();
    }

    setupDateValidation() {
        document.addEventListener('change', (e) => {
            if (e.target.matches('.date-input')) {
                const selectedDate = new Date(e.target.value);
                const today = new Date();
                const maxPastDate = new Date();
                maxPastDate.setDate(today.getDate() - 7);

                if (selectedDate > today) {
                    this.showValidationError(e.target, 'Tanggal tidak boleh di masa depan');
                } else if (selectedDate < maxPastDate) {
                    this.showValidationError(e.target, 'Tanggal tidak boleh lebih dari 7 hari yang lalu');
                } else {
                    this.clearValidationError(e.target);
                }
            }
        });
    }

    setupTimeValidation() {
        document.addEventListener('change', (e) => {
            if (e.target.matches('.time-end')) {
                const startTimeInput = document.querySelector('.time-start');
                const endTimeInput = e.target;

                if (startTimeInput && endTimeInput.value && startTimeInput.value) {
                    const startTime = new Date(`2000-01-01T${startTimeInput.value}`);
                    const endTime = new Date(`2000-01-01T${endTimeInput.value}`);

                    if (endTime <= startTime) {
                        this.showValidationError(endTimeInput, 'Jam selesai harus setelah jam mulai');
                    } else {
                        this.clearValidationError(endTimeInput);
                    }
                }
            }
        });
    }

    setupFileValidation() {
        document.addEventListener('change', (e) => {
            if (e.target.matches('input[type="file"]')) {
                const file = e.target.files[0];
                if (file) {
                    // Check file size (2MB limit)
                    if (file.size > 2 * 1024 * 1024) {
                        this.showValidationError(e.target, 'Ukuran file maksimal 2MB');
                        e.target.value = '';
                        return;
                    }

                    // Check file type
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                    if (!allowedTypes.includes(file.type)) {
                        this.showValidationError(e.target, 'Hanya file gambar (JPEG, PNG, JPG, GIF) yang diizinkan');
                        e.target.value = '';
                        return;
                    }

                    this.clearValidationError(e.target);
                    this.showFilePreview(e.target, file);
                }
            }
        });
    }

    setupFormSubmissionHandlers() {
        document.addEventListener('submit', (e) => {
            const form = e.target;
            
            // Add loading state to submit button
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
                
                // Re-enable after 10 seconds as fallback
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitBtn.dataset.originalText || 'Simpan';
                }, 10000);
            }
        });
    }

    showValidationError(input, message) {
        input.classList.add('is-invalid');
        
        // Remove existing error message
        const existingError = input.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }

        // Add new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        input.parentNode.appendChild(errorDiv);
    }

    clearValidationError(input) {
        input.classList.remove('is-invalid');
        const errorDiv = input.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    showFilePreview(input, file) {
        // Remove existing preview
        const existingPreview = input.parentNode.querySelector('.file-preview');
        if (existingPreview) {
            existingPreview.remove();
        }

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = document.createElement('img');
                preview.src = e.target.result;
                preview.className = 'file-preview mt-2';
                preview.style.maxWidth = '200px';
                preview.style.maxHeight = '200px';
                input.parentNode.appendChild(preview);
            };
            reader.readAsDataURL(file);
        }
    }
}