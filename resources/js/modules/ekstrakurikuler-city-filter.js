/**
 * Ekstrakurikuler City Filter Module
 * 
 * Menangani dynamic filtering sekolah berdasarkan kota yang dipilih
 */

class EkstrakurikulerCityFilter {
    constructor() {
        this.citySelect = null;
        this.schoolSelect = null;
        this.apiUrl = '/api/sekolah/by-city';
        
        this.init();
    }

    init() {
        this.citySelect = document.querySelector('#city');
        this.schoolSelect = document.querySelector('#sekolah_kodlan');
        
        if (this.citySelect) {
            this.citySelect.addEventListener('change', this.handleCityChange.bind(this));
        }
    }

    async handleCityChange(event) {
        const selectedCity = event.target.value;
        
        if (!this.schoolSelect) return;

        // Clear school options except the first one
        this.schoolSelect.innerHTML = '<option value="">Pilih Sekolah</option>';
        
        if (!selectedCity) {
            // If no city selected, show all schools (reload page or use default)
            this.showLoadingState(false);
            return;
        }

        try {
            this.showLoadingState(true);
            
            const response = await fetch(`${this.apiUrl}?city=${encodeURIComponent(selectedCity)}`);
            const data = await response.json();
            
            if (data.status === 'success' && data.data) {
                this.populateSchoolOptions(data.data);
            } else {
                this.showError(data.message || 'Gagal memuat data sekolah');
            }
        } catch (error) {
            console.error('Error fetching schools:', error);
            this.showError('Terjadi kesalahan saat memuat data sekolah');
        } finally {
            this.showLoadingState(false);
        }
    }

    populateSchoolOptions(schools) {
        schools.forEach(school => {
            const option = document.createElement('option');
            option.value = school.kodlan;
            option.textContent = `${school.namasekolah} - ${school.kec}`;
            option.setAttribute('data-kotkab', school.kotkab);
            option.setAttribute('data-kec', school.kec);
            
            this.schoolSelect.appendChild(option);
        });

        // Show count of schools found
        if (schools.length === 0) {
            const noDataOption = document.createElement('option');
            noDataOption.value = '';
            noDataOption.textContent = 'Tidak ada sekolah di kota ini';
            noDataOption.disabled = true;
            this.schoolSelect.appendChild(noDataOption);
        }

        // Dispatch custom event for other components
        this.citySelect.dispatchEvent(new CustomEvent('schoolsLoaded', {
            detail: { city: this.citySelect.value, schools: schools }
        }));
    }

    showLoadingState(loading) {
        if (loading) {
            this.schoolSelect.disabled = true;
            this.schoolSelect.innerHTML = '<option value="">Memuat sekolah...</option>';
        } else {
            this.schoolSelect.disabled = false;
        }
    }

    showError(message) {
        // Clear existing options
        this.schoolSelect.innerHTML = '<option value="">Pilih Sekolah</option>';
        
        // Add error option
        const errorOption = document.createElement('option');
        errorOption.value = '';
        errorOption.textContent = message;
        errorOption.disabled = true;
        errorOption.style.color = '#dc3545';
        this.schoolSelect.appendChild(errorOption);

        // Show toast notification if available
        if (typeof window.showToast === 'function') {
            window.showToast('error', message);
        }
    }
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new EkstrakurikulerCityFilter();
});

// Export for manual initialization
window.EkstrakurikulerCityFilter = EkstrakurikulerCityFilter;