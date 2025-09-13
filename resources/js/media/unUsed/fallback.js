/**
 * Fallback untuk media library jika komponen utama gagal load
 */

// Fallback MediaLibrary class
class FallbackMediaLibrary {
    constructor() {
        console.log('Using fallback MediaLibrary');
        this.selectedItems = new Set();
        this.currentView = 'grid';
        this.mediaData = [];
        this.currentFolder = '';
        
        this.init();
    }

    init() {
        this.bindBasicEvents();
    }

    bindBasicEvents() {
        // Basic upload handling
        const fileInput = document.getElementById('fileInput');
        const uploadArea = document.getElementById('uploadArea');
        
        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                this.handleBasicUpload(e.target.files);
            });
        }

        if (uploadArea) {
            uploadArea.addEventListener('click', () => {
                if (fileInput) fileInput.click();
            });
        }

        // Basic view toggle
        document.querySelectorAll('.view-toggle').forEach(btn => {
            btn.addEventListener('click', (e) => {
                document.querySelectorAll('.view-toggle').forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                this.currentView = e.target.dataset.view;
                this.toggleView();
            });
        });
    }

    async handleBasicUpload(files) {
        if (!files || files.length === 0) return;
        
        for (const file of files) {
            const formData = new FormData();
            formData.append('file', file);
            
            try {
                const response = await fetch('/api/media/upload/file', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                });
                
                if (response.ok) {
                    this.showMessage('success', `${file.name} uploaded successfully`);
                } else {
                    this.showMessage('error', `Failed to upload ${file.name}`);
                }
            } catch (error) {
                this.showMessage('error', `Upload failed: ${error.message}`);
            }
        }
        
        // Reload page to show new files
        setTimeout(() => window.location.reload(), 1000);
    }

    toggleView() {
        const grid = document.getElementById('mediaGrid');
        const list = document.getElementById('mediaList');
        
        if (this.currentView === 'grid') {
            if (grid) grid.style.display = 'grid';
            if (list) list.style.display = 'none';
        } else {
            if (grid) grid.style.display = 'none';
            if (list) list.style.display = 'block';
        }
    }

    showMessage(type, message) {
        if (window.showToast) {
            window.showToast(message, type);
        } else {
            alert(message);
        }
    }
}

// Export fallback
window.FallbackMediaLibrary = FallbackMediaLibrary;

export { FallbackMediaLibrary };