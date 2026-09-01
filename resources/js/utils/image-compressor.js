/**
 * Utility Kompresi & Memory Detach File Client-Side untuk WebApperlass
 * Menggunakan HTML5 Canvas & in-memory Blob cloning untuk:
 * 1. Memperkecil ukuran foto kamera (5-15MB) menjadi <400KB sebelum dikirim.
 * 2. Mengisolasi file ke memori browser (RAM) untuk MENCEGAH ERR_UPLOAD_FILE_CHANGED
 *    yang sering terjadi pada Android saat kamera OS memodifikasi temp file di latar belakang.
 */

/**
 * Konversi file OS menjadi File in-memory murni (decoupled dari filesystem Android)
 * @param {File} file
 * @returns {Promise<File>}
 */
export async function detachFileToMemory(file) {
    if (!file) return file;
    try {
        const buffer = await file.arrayBuffer();
        return new File([buffer], file.name, {
            type: file.type || 'application/octet-stream',
            lastModified: Date.now()
        });
    } catch (e) {
        return file;
    }
}

/**
 * Kompres satu file gambar
 * @param {File} file - File gambar asli
 * @param {Object} options - Opsi kompresi
 * @param {number} options.maxWidth - Lebar maksimum (default: 1600)
 * @param {number} options.maxHeight - Tinggi maksimum (default: 1600)
 * @param {number} options.quality - Kualitas JPEG (0.1 - 1.0, default: 0.8)
 * @param {string} options.outputType - MIME type output (default: 'image/jpeg')
 * @returns {Promise<File>} File terkompresi & decoupled in-memory
 */
export function compressImageFile(file, options = {}) {
    return new Promise((resolve) => {
        if (!file) {
            return resolve(file);
        }

        if (!file.type || !file.type.startsWith('image/')) {
            detachFileToMemory(file).then(resolve);
            return;
        }

        // Jika file SVG/GIF atau sudah sangat kecil (< 150 KB), lewati kompresi canvas namun tetap decouple ke RAM
        if (file.type === 'image/svg+xml' || file.type === 'image/gif' || file.size < 150 * 1024) {
            detachFileToMemory(file).then(resolve);
            return;
        }

        const maxWidth = options.maxWidth || 1600;
        const maxHeight = options.maxHeight || 1600;
        const quality = options.quality !== undefined ? options.quality : 0.8;
        const outputType = options.outputType || 'image/jpeg';

        const reader = new FileReader();
        reader.readAsDataURL(file);

        reader.onload = (event) => {
            const img = new Image();
            img.src = event.target.result;

            img.onload = () => {
                let width = img.width;
                let height = img.height;

                // Hitung dimensi rasio aspek baru
                if (width > maxWidth || height > maxHeight) {
                    if (width / maxWidth > height / maxHeight) {
                        height = Math.round((height * maxWidth) / width);
                        width = maxWidth;
                    } else {
                        width = Math.round((width * maxHeight) / height);
                        height = maxHeight;
                    }
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob(
                    (blob) => {
                        if (!blob) {
                            detachFileToMemory(file).then(resolve);
                            return;
                        }

                        // Ganti ekstensi jika perlu
                        let newName = file.name;
                        if (!newName.toLowerCase().endsWith('.jpg') && !newName.toLowerCase().endsWith('.jpeg')) {
                            newName = newName.replace(/\.[^/.]+$/, '') + '.jpg';
                        }

                        const compressedFile = new File([blob], newName, {
                            type: outputType,
                            lastModified: Date.now()
                        });

                        resolve(compressedFile);
                    },
                    outputType,
                    quality
                );
            };

            img.onerror = () => detachFileToMemory(file).then(resolve);
        };

        reader.onerror = () => detachFileToMemory(file).then(resolve);
    });
}

/**
 * Otomatis pasang kompresi dan memory-isolation pada elemen input file
 * @param {HTMLInputElement} inputElement
 */
export function attachImageCompressionToInput(inputElement) {
    if (!inputElement || inputElement.dataset.compressedAttached || inputElement.dataset.noAutoCompress === 'true') return;
    inputElement.dataset.compressedAttached = 'true';

    inputElement.addEventListener('change', async function () {
        if (!this.files || this.files.length === 0) return;

        const originalFiles = Array.from(this.files);
        const dataTransfer = new DataTransfer();
        let wasModified = false;

        for (const file of originalFiles) {
            try {
                if (file.type && file.type.startsWith('image/')) {
                    const processed = await compressImageFile(file);
                    dataTransfer.items.add(processed);
                    wasModified = true;
                } else {
                    const detached = await detachFileToMemory(file);
                    dataTransfer.items.add(detached);
                    wasModified = true;
                }
            } catch (e) {
                console.warn('Memory detach fallback for file:', file.name, e);
                dataTransfer.items.add(file);
            }
        }

        if (wasModified) {
            try {
                this.files = dataTransfer.files;
            } catch (err) {
                console.warn('Cannot update input.files via DataTransfer:', err);
            }

            // Trigger visual feedback jika ada badge/feedback container
            const feedbackEl = this.closest('.form-group, .mb-3, .mb-4, .upload-zone')?.parentElement?.querySelector('.image-compression-hint');
            if (feedbackEl) {
                feedbackEl.innerHTML = '<i class="bi bi-check-circle-fill text-success me-1"></i>Foto dioptimalkan otomatis';
                feedbackEl.classList.remove('d-none');
            }
        }
    });
}

/**
 * Inisialisasi otomatis untuk seluruh form file di halaman
 */
export function initAutoImageCompressor() {
    const inputs = document.querySelectorAll(
        'input[type="file"]:not([data-no-auto-compress="true"]), input[type="file"].auto-compress, form.auto-compress-form input[type="file"]'
    );
    inputs.forEach(attachImageCompressionToInput);
}
