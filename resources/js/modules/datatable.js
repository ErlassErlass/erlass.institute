/**
 * DataTable module for WebApperlass
 * Provides standardized DataTable configurations and utilities
 */

export class DataTableManager {
    constructor() {
        // Add custom date sorting for Indonesian date format
        this.setupDateSorting();
        this.defaultConfig = {
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
            language: {
                search: "Cari:",
                searchPlaceholder: "Ketik untuk mencari...",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                loadingRecords: "Memuat...",
                processing: "Memproses...",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                },
                emptyTable: "Tidak ada data yang tersedia",
                zeroRecords: "Tidak ditemukan data yang sesuai",
                aria: {
                    sortAscending: ": aktifkan untuk mengurutkan kolom naik",
                    sortDescending: ": aktifkan untuk mengurutkan kolom turun"
                }
            },
            order: [[0, 'desc']],
            responsive: true,
            autoWidth: false,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            columnDefs: [
                { orderable: false, targets: -1 },
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: -1 }
            ],
            stateSave: true,
            stateDuration: 300
        };
    }

    /**
     * Initialize standard DataTable
     * @param {string} selector - CSS selector for table
     * @param {object} customConfig - Custom configuration to merge
     */
    init(selector = '.datatable', customConfig = {}) {
        // Properly merge columnDefs to avoid conflicts
        const mergedConfig = { ...this.defaultConfig };
        
        if (customConfig.columnDefs) {
            // Use custom columnDefs completely, don't merge with default
            mergedConfig.columnDefs = customConfig.columnDefs;
            delete customConfig.columnDefs;
        }
        
        const config = { ...mergedConfig, ...customConfig };
        
        $(document).ready(() => {
            // Check if selector exists on page
            const elements = $(selector);
            if (elements.length === 0) {
                return; // No elements found, skip initialization
            }
            
            elements.each(function() {
                const table = $(this);
                
                // Check if DataTable is already initialized
                if ($.fn.DataTable.isDataTable(table)) {
                    table.DataTable().destroy();
                }
                
                try {
                    const dataTable = table.DataTable(config);
                    
                    // Add custom styling to search input
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_filter input').attr('placeholder', 'Ketik untuk mencari...');
                    
                    // Add custom styling to length select
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                    
                    return dataTable;
                } catch (error) {
                    console.warn('DataTables initialization failed for selector:', selector, error);
                }
            });
        });
    }

    /**
     * Initialize DataTable with export buttons
     * @param {string} selector - CSS selector for table
     * @param {object} customConfig - Custom configuration to merge
     */
    initWithExport(selector, customConfig = {}) {
        const exportConfig = {
            ...this.defaultConfig,
            dom: '<"row"<"col-sm-6"l><"col-sm-6"B>>' +
                 '<"row"<"col-sm-12"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-5"i><"col-sm-7"p>>',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                    className: 'btn btn-success btn-sm'
                },
                {
                    extend: 'pdf',
                    text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm'
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer"></i> Print',
                    className: 'btn btn-info btn-sm'
                }
            ],
            ...customConfig
        };

        $(document).ready(() => {
            $(selector).DataTable(exportConfig);
        });
    }

    /**
     * Refresh DataTable data
     * @param {string} selector - CSS selector for table
     */
    refresh(selector) {
        const table = $(selector).DataTable();
        table.ajax.reload();
    }

    /**
     * Destroy DataTable instance
     * @param {string} selector - CSS selector for table
     */
    destroy(selector) {
        const table = $(selector).DataTable();
        table.destroy();
    }
    
    /**
     * Setup custom date sorting for Indonesian date formats
     */
    setupDateSorting() {
        // Add date sorting for Indonesian format (d M Y, d/m/Y, etc.)
        $.fn.dataTable.ext.type.detect.unshift(function (data) {
            if (data === null || data === '') return null;
            
            // Check for Indonesian date formats
            if (data.match(/^\d{1,2}\s+(Jan|Feb|Mar|Apr|Mei|Jun|Jul|Agu|Sep|Okt|Nov|Des)\s+\d{4}$/)) {
                return 'date-id';
            }
            if (data.match(/^\d{1,2}\/\d{1,2}\/\d{4}$/)) {
                return 'date-dd/mm/yyyy';
            }
            return null;
        });
        
        // Add sorting function for Indonesian date format
        $.fn.dataTable.ext.type.order['date-id-pre'] = function (data) {
            if (data === null || data === '') return 0;
            
            const months = {
                'Jan': '01', 'Feb': '02', 'Mar': '03', 'Apr': '04',
                'Mei': '05', 'Jun': '06', 'Jul': '07', 'Agu': '08',
                'Sep': '09', 'Okt': '10', 'Nov': '11', 'Des': '12'
            };
            
            const parts = data.split(' ');
            if (parts.length === 3) {
                const day = parts[0].padStart(2, '0');
                const month = months[parts[1]] || '01';
                const year = parts[2];
                return parseInt(year + month + day);
            }
            
            return 0;
        };
        
        // Add sorting function for dd/mm/yyyy format
        $.fn.dataTable.ext.type.order['date-dd/mm/yyyy-pre'] = function (data) {
            if (data === null || data === '') return 0;
            
            const parts = data.split('/');
            if (parts.length === 3) {
                const day = parts[0].padStart(2, '0');
                const month = parts[1].padStart(2, '0');
                const year = parts[2];
                return parseInt(year + month + day);
            }
            
            return 0;
        };
    }
}