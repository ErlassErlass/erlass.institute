import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
import 'datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css';
import 'select2/dist/css/select2.min.css';
import 'select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css';
import "flatpickr/dist/flatpickr.min.css";

// Import jQuery and make it available globally
import jQuery from 'jquery';
window.$ = jQuery;
window.jQuery = jQuery;

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

// Initialize application components when DOM is loaded
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

    // Initialize Bootstrap components
    const bootstrap = window.bootstrap;
    if (bootstrap) {
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
});

// Export for global use
window.DataTableManager = DataTableManager;
