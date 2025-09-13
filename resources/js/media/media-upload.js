import { fetchAxios } from "@handlers/fetchAxios.js";
import axiosClient from "@api/axiosClient.js";

/**
 * Media Upload Component
 * Handles file upload with drag & drop, progress tracking, and WebP conversion
 */
class MediaUpload {
    constructor(options = {}) {
        this.options = {
            uploadUrl: '/api/media/upload/file',
            allowedTypes: ['image/*', 'video/*', 'audio/*', 'application/*'],
            maxFileSize: 10 * 1024 * 1024, // 10MB
            multiple: true,
            autoUpload: true,
            collection: null,
            onUploadStart: null,
            onUploadProgress: null,
            onUploadSuccess: null,
            onUploadError: null,
            onUploadComplete: null,
            ...options
        };
        
        this.uploadQueue = [];
        this.isUploading = false;
        
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Upload area events
        const uploadArea = document.getElementById("uploadArea");
        const fileInput = document.getElementById("fileInput");
        const selectButton = uploadArea?.querySelector("button");

        if (!uploadArea || !fileInput) return;

        // Prevent double binding
        if (uploadArea.dataset.bound) return;
        uploadArea.dataset.bound = 'true';

        // File input change
        fileInput.addEventListener("change", (e) => {
            this.handleFiles(e.target.files);
        });

        // Button click
        if (selectButton) {
            selectButton.addEventListener("click", (e) => {
                e.stopPropagation();
                fileInput.click();
            });
        }

        // Drag & drop events
        uploadArea.addEventListener("dragover", (e) => {
            e.preventDefault();
            uploadArea.classList.add("dragover");
        });

        uploadArea.addEventListener("dragleave", (e) => {
            // Only remove dragover if we're leaving the upload area completely
            if (!uploadArea.contains(e.relatedTarget)) {
                uploadArea.classList.remove("dragover");
            }
        });

        uploadArea.addEventListener("drop", (e) => {
            e.preventDefault();
            uploadArea.classList.remove("dragover");
            this.handleFiles(e.dataTransfer.files);
        });
    }

    /**
     * Handle file selection
     */
    async handleFiles(files) {
        if (!files || files.length === 0) return;

        // Validate and compress files
        const processedFiles = [];
        
        for (const file of Array.from(files)) {
            if (!this.validateFile(file, false)) continue; // Skip size check for now
            
            try {
                // Compress image if it's an image and too large
                const processedFile = await this.processFile(file);
                processedFiles.push(processedFile);
            } catch (error) {
                this.showError(`Gagal memproses ${file.name}: ${error.message}`);
            }
        }
        
        if (processedFiles.length === 0) {
            this.showError('Tidak ada file yang valid untuk diupload');
            return;
        }

        // Add to upload queue
        processedFiles.forEach(file => {
            // Generate secure random ID
            let randomId;
            if (typeof crypto !== 'undefined' && crypto.getRandomValues) {
                const array = new Uint32Array(1);
                crypto.getRandomValues(array);
                randomId = array[0];
            } else {
                // Fallback for older browsers
                randomId = Math.random();
            }
            
            this.uploadQueue.push({
                file,
                id: Date.now() + randomId,
                status: 'pending',
                progress: 0
            });
        });

        // Start upload if auto-upload is enabled
        if (this.options.autoUpload) {
            await this.startUpload();
        }

        // Clear file input
        const fileInput = document.getElementById("fileInput");
        if (fileInput) fileInput.value = "";
    }

    /**
     * Validate file
     */
    validateFile(file, checkSize = true) {
        // Check file size (optional)
        if (checkSize && file.size > this.options.maxFileSize) {
            this.showError(`File "${file.name}" terlalu besar (${this.formatFileSize(file.size)}). Maksimal ${this.formatFileSize(this.options.maxFileSize)} per file.`);
            return false;
        }

        // Check file type
        if (this.options.allowedTypes.length > 0) {
            const isAllowed = this.options.allowedTypes.some(type => {
                if (type.endsWith('/*')) {
                    return file.type.startsWith(type.replace('/*', '/'));
                }
                return file.type === type;
            });

            if (!isAllowed) {
                const fileExt = file.name.split('.').pop().toUpperCase();
                this.showError(`File "${file.name}" (${fileExt}) tidak didukung. Pilih file gambar, video, atau dokumen.`);
                return false;
            }
        }

        return true;
    }

    /**
     * Process file (compress if image and too large)
     */
    async processFile(file) {
        // If not an image, return as is
        if (!file.type.startsWith('image/')) {
            return file;
        }

        // If image is small enough, return as is
        const maxImageSize = 2 * 1024 * 1024; // 2MB for images
        if (file.size <= maxImageSize) {
            return file;
        }

        // Compress image
        this.showSuccess(`Mengompres gambar "${file.name}"...`);
        return await this.compressImage(file, maxImageSize);
    }

    /**
     * Compress image to target size
     */
    async compressImage(file, targetSize) {
        return new Promise((resolve, reject) => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();

            img.onload = () => {
                // Calculate new dimensions (maintain aspect ratio)
                let { width, height } = img;
                const maxDimension = 1920; // Max width/height
                
                if (width > maxDimension || height > maxDimension) {
                    if (width > height) {
                        height = (height * maxDimension) / width;
                        width = maxDimension;
                    } else {
                        width = (width * maxDimension) / height;
                        height = maxDimension;
                    }
                }

                canvas.width = width;
                canvas.height = height;

                // Draw and compress
                ctx.drawImage(img, 0, 0, width, height);

                // Try different quality levels to meet target size
                this.tryCompress(canvas, file.name, targetSize, 0.8)
                    .then(resolve)
                    .catch(reject);
            };

            img.onerror = () => reject(new Error('Gagal memuat gambar'));
            img.src = URL.createObjectURL(file);
        });
    }

    /**
     * Try compress with different quality levels
     */
    async tryCompress(canvas, fileName, targetSize, quality) {
        return new Promise((resolve) => {
            canvas.toBlob((blob) => {
                if (blob.size <= targetSize || quality <= 0.1) {
                    // Create new file object
                    const compressedFile = new File([blob], fileName, {
                        type: 'image/webp',
                        lastModified: Date.now()
                    });
                    
                    const reduction = ((1 - blob.size / targetSize) * 100).toFixed(1);
                    this.showSuccess(`Gambar dikompres ${reduction}% (${this.formatFileSize(blob.size)})`);
                    
                    resolve(compressedFile);
                } else {
                    // Try lower quality
                    this.tryCompress(canvas, fileName, targetSize, quality - 0.1)
                        .then(resolve);
                }
            }, 'image/webp', quality);
        });
    }

    /**
     * Start upload process
     */
    async startUpload() {
        if (this.isUploading) return;
        
        this.isUploading = true;
        this.showProgress();

        if (this.options.onUploadStart) {
            this.options.onUploadStart(this.uploadQueue);
        }

        try {
            for (let i = 0; i < this.uploadQueue.length; i++) {
                const item = this.uploadQueue[i];
                
                if (item.status !== 'pending') continue;

                item.status = 'uploading';
                
                try {
                    const result = await this.uploadFile(item.file, (progress) => {
                        item.progress = progress;
                        this.updateProgress();
                    });
                    
                    item.status = 'completed';
                    item.result = result;
                    
                    if (this.options.onUploadSuccess) {
                        this.options.onUploadSuccess(result, item.file);
                    }
                    
                } catch (error) {
                    item.status = 'error';
                    item.error = error;
                    
                    if (this.options.onUploadError) {
                        this.options.onUploadError(error, item.file);
                    }
                    
                    let errorMsg;
                    
                    if (error.response && error.response.data) {
                        // Use user-friendly message if available
                        if (error.response.data.user_message) {
                            errorMsg = `${item.file.name}: ${error.response.data.user_message}`;
                        } else if (error.response.data.message) {
                            errorMsg = `${item.file.name}: ${error.response.data.message}`;
                        } else {
                            errorMsg = `Gagal upload ${item.file.name}`;
                        }
                    } else {
                        errorMsg = `Gagal upload ${item.file.name}: ${error.message}`;
                    }
                    
                    this.showError(errorMsg);
                }
            }
        } finally {
            this.isUploading = false;
            this.hideProgress();
            
            if (this.options.onUploadComplete) {
                this.options.onUploadComplete(this.uploadQueue);
            }
            
            // Clear completed uploads
            this.uploadQueue = this.uploadQueue.filter(item => item.status === 'error');
        }
    }

    /**
     * Upload single file
     */
    async uploadFile(file, onProgress) {
        const formData = new FormData();
        formData.append("file", file);
        
        if (this.options.collection) {
            formData.append("collection", this.options.collection);
        }

        try {
            const response = await axiosClient.post(this.options.uploadUrl, formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
                onUploadProgress: (progressEvent) => {
                    if (onProgress && progressEvent.total) {
                        const progress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                        onProgress(progress);
                    }
                }
            });
            
            return response.data;
        } catch (error) {
            console.error("Upload error:", error);
            throw new Error(error.response?.data?.message || error.message || "Upload failed");
        }
    }

    /**
     * Show upload progress
     */
    showProgress() {
        const uploadContent = document.querySelector(".upload-content");
        const uploadProgress = document.querySelector(".upload-progress");
        
        if (uploadContent) uploadContent.style.display = "none";
        if (uploadProgress) uploadProgress.style.display = "block";
    }

    /**
     * Hide upload progress
     */
    hideProgress() {
        const uploadContent = document.querySelector(".upload-content");
        const uploadProgress = document.querySelector(".upload-progress");
        
        if (uploadContent) uploadContent.style.display = "block";
        if (uploadProgress) uploadProgress.style.display = "none";
    }

    /**
     * Update progress bar
     */
    updateProgress() {
        const totalFiles = this.uploadQueue.length;
        const completedFiles = this.uploadQueue.filter(item => 
            item.status === 'completed' || item.status === 'error'
        ).length;
        
        const currentFile = this.uploadQueue.find(item => item.status === 'uploading');
        const currentProgress = currentFile ? currentFile.progress : 0;
        
        // Calculate overall progress
        const overallProgress = totalFiles > 0 
            ? ((completedFiles + (currentProgress / 100)) / totalFiles) * 100
            : 0;

        const progressBar = document.querySelector(".progress-bar");
        if (progressBar) {
            progressBar.style.width = Math.round(overallProgress) + "%";
        }

        if (this.options.onUploadProgress) {
            this.options.onUploadProgress(overallProgress, completedFiles, totalFiles);
        }
    }

    /**
     * Show error message
     */
    showError(message) {
        if (typeof window.showToast === "function") {
            window.showToast(message, "error");
        } else {
            console.error(message);
            alert(message);
        }
    }

    /**
     * Show success message
     */
    showSuccess(message) {
        if (typeof window.showToast === "function") {
            window.showToast(message, "success");
        } else {
            console.log(message);
        }
    }

    /**
     * Format file size
     */
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    /**
     * Set collection for uploads
     */
    setCollection(collection) {
        this.options.collection = collection;
    }

    /**
     * Clear upload queue
     */
    clearQueue() {
        this.uploadQueue = [];
    }

    /**
     * Get upload queue
     */
    getQueue() {
        return this.uploadQueue;
    }
}

// Don't auto-initialize to prevent multiple instances
// MediaLibrary will handle initialization

export { MediaUpload };