/**
 * Media Library Test Suite
 * Test semua komponen media library untuk memastikan migrasi berhasil
 */

// Test configuration
const TEST_CONFIG = {
    verbose: true,
    timeout: 5000,
    testImageUrl: '/media/settings/logo.webp',
    testFolder: 'test-folder'
};

class MediaLibraryTest {
    constructor() {
        this.results = [];
        this.passed = 0;
        this.failed = 0;
    }

    /**
     * Run all tests
     */
    async runAllTests() {
        console.log('🧪 Starting Media Library Test Suite...');
        
        try {
            await this.testComponentsAvailability();
            await this.testEasyDataTable();
            await this.testMediaUpload();
            await this.testImageCropper();
            await this.testMediaPicker();
            await this.testContextMenu();
            await this.testMediaLibraryClass();
            
            this.showResults();
        } catch (error) {
            console.error('❌ Test suite failed:', error);
        }
    }

    /**
     * Test if all components are available
     */
    async testComponentsAvailability() {
        this.log('Testing components availability...');
        
        const components = [
            'MediaLibrary',
            'MediaUpload', 
            'ImageCropper',
            'MediaPicker',
            'ContextMenu',
            'openMediaPicker'
        ];

        for (const component of components) {
            this.assert(
                window[component] !== undefined,
                `${component} should be available globally`,
                `${component} is not available`
            );
        }
    }

    /**
     * Test EasyDataTable integration
     */
    async testEasyDataTable() {
        this.log('Testing EasyDataTable integration...');
        
        const tableElement = document.getElementById('table-media');
        if (tableElement) {
            this.assert(
                true,
                'Media table element exists',
                'Media table element not found'
            );
            
            // Check if DataTable is initialized
            const hasDataTable = tableElement.classList.contains('dataTable') || 
                                tableElement.querySelector('.dataTables_wrapper');
            
            this.assert(
                hasDataTable,
                'DataTable is initialized on media table',
                'DataTable not initialized'
            );
        } else {
            this.log('⚠️ Media table not found - skipping DataTable tests');
        }
    }

    /**
     * Test MediaUpload component
     */
    async testMediaUpload() {
        this.log('Testing MediaUpload component...');
        
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        
        if (uploadArea && fileInput) {
            // Test component initialization
            const mediaUpload = new window.MediaUpload({
                autoUpload: false,
                collection: 'test'
            });
            
            this.assert(
                mediaUpload instanceof window.MediaUpload,
                'MediaUpload instance created successfully',
                'Failed to create MediaUpload instance'
            );
            
            // Test file validation
            const testFile = new File(['test'], 'test.txt', { type: 'text/plain' });
            const isValid = mediaUpload.validateFile(testFile);
            
            this.assert(
                typeof isValid === 'boolean',
                'File validation works',
                'File validation failed'
            );
            
            // Test format file size
            const formattedSize = mediaUpload.formatFileSize(1024);
            this.assert(
                formattedSize === '1 KB',
                'File size formatting works',
                'File size formatting failed'
            );
            
        } else {
            this.log('⚠️ Upload area not found - skipping MediaUpload tests');
        }
    }

    /**
     * Test ImageCropper component
     */
    async testImageCropper() {
        this.log('Testing ImageCropper component...');
        
        const imageEditor = document.getElementById('imageEditor');
        const cropperImage = document.getElementById('cropperImage');
        
        if (imageEditor && cropperImage) {
            const imageCropper = new window.ImageCropper({
                onSave: () => console.log('Test save callback'),
                onCancel: () => console.log('Test cancel callback')
            });
            
            this.assert(
                imageCropper instanceof window.ImageCropper,
                'ImageCropper instance created successfully',
                'Failed to create ImageCropper instance'
            );
            
            // Test cropper methods
            this.assert(
                typeof imageCropper.setCropMode === 'function',
                'setCropMode method exists',
                'setCropMode method missing'
            );
            
            this.assert(
                typeof imageCropper.startEdit === 'function',
                'startEdit method exists',
                'startEdit method missing'
            );
            
        } else {
            this.log('⚠️ Image editor not found - skipping ImageCropper tests');
        }
    }

    /**
     * Test MediaPicker component
     */
    async testMediaPicker() {
        this.log('Testing MediaPicker component...');
        
        // Test MediaPicker class
        const mediaPicker = new window.MediaPicker({
            multiple: false,
            allowedTypes: ['image'],
            onSelect: (media) => console.log('Test select:', media)
        });
        
        this.assert(
            mediaPicker instanceof window.MediaPicker,
            'MediaPicker instance created successfully',
            'Failed to create MediaPicker instance'
        );
        
        // Test openMediaPicker function
        this.assert(
            typeof window.openMediaPicker === 'function',
            'openMediaPicker function is available',
            'openMediaPicker function missing'
        );
        
        // Test media picker button initialization
        const testButton = document.createElement('button');
        testButton.setAttribute('data-media-picker', '');
        testButton.setAttribute('data-multiple', 'false');
        testButton.setAttribute('data-types', 'image');
        document.body.appendChild(testButton);
        
        // Simulate initialization
        window.initializeMediaPickerButtons();
        
        this.assert(
            testButton.dataset.bound === 'true',
            'Media picker button initialized',
            'Media picker button not initialized'
        );
        
        // Cleanup
        document.body.removeChild(testButton);
    }

    /**
     * Test ContextMenu component
     */
    async testContextMenu() {
        this.log('Testing ContextMenu component...');
        
        const contextMenu = document.getElementById('contextMenu');
        
        if (contextMenu) {
            const contextMenuComponent = new window.ContextMenu({
                onCopy: () => console.log('Test copy'),
                onMove: () => console.log('Test move'),
                onRename: () => console.log('Test rename')
            });
            
            this.assert(
                contextMenuComponent instanceof window.ContextMenu,
                'ContextMenu instance created successfully',
                'Failed to create ContextMenu instance'
            );
            
            // Test context menu methods
            this.assert(
                typeof contextMenuComponent.show === 'function',
                'show method exists',
                'show method missing'
            );
            
            this.assert(
                typeof contextMenuComponent.hide === 'function',
                'hide method exists', 
                'hide method missing'
            );
            
        } else {
            this.log('⚠️ Context menu not found - skipping ContextMenu tests');
        }
    }

    /**
     * Test MediaLibrary main class
     */
    async testMediaLibraryClass() {
        this.log('Testing MediaLibrary main class...');
        
        const mediaGrid = document.getElementById('mediaGrid');
        
        if (mediaGrid) {
            // Test if global instance exists
            this.assert(
                window.mediaLibrary instanceof window.MediaLibrary,
                'Global MediaLibrary instance exists',
                'Global MediaLibrary instance missing'
            );
            
            // Test MediaLibrary methods
            const mediaLibrary = window.mediaLibrary;
            
            this.assert(
                typeof mediaLibrary.loadMedia === 'function',
                'loadMedia method exists',
                'loadMedia method missing'
            );
            
            this.assert(
                typeof mediaLibrary.renderMedia === 'function',
                'renderMedia method exists',
                'renderMedia method missing'
            );
            
            this.assert(
                typeof mediaLibrary.navigateToFolder === 'function',
                'navigateToFolder method exists',
                'navigateToFolder method missing'
            );
            
            // Test component integration
            this.assert(
                mediaLibrary.mediaUpload instanceof window.MediaUpload,
                'MediaUpload component integrated',
                'MediaUpload component not integrated'
            );
            
            this.assert(
                mediaLibrary.imageCropper instanceof window.ImageCropper,
                'ImageCropper component integrated',
                'ImageCropper component not integrated'
            );
            
            this.assert(
                mediaLibrary.contextMenu instanceof window.ContextMenu,
                'ContextMenu component integrated',
                'ContextMenu component not integrated'
            );
            
        } else {
            this.log('⚠️ Media grid not found - skipping MediaLibrary tests');
        }
    }

    /**
     * Assert function for tests
     */
    assert(condition, successMessage, errorMessage) {
        const result = {
            passed: !!condition,
            message: condition ? successMessage : errorMessage,
            timestamp: new Date().toISOString()
        };
        
        this.results.push(result);
        
        if (result.passed) {
            this.passed++;
            if (TEST_CONFIG.verbose) {
                console.log(`✅ ${result.message}`);
            }
        } else {
            this.failed++;
            console.error(`❌ ${result.message}`);
        }
    }

    /**
     * Log function
     */
    log(message) {
        if (TEST_CONFIG.verbose) {
            console.log(`📝 ${message}`);
        }
    }

    /**
     * Show test results
     */
    showResults() {
        console.log('\n📊 Test Results:');
        console.log(`✅ Passed: ${this.passed}`);
        console.log(`❌ Failed: ${this.failed}`);
        console.log(`📈 Success Rate: ${((this.passed / (this.passed + this.failed)) * 100).toFixed(2)}%`);
        
        if (this.failed === 0) {
            console.log('🎉 All tests passed! Media Library migration successful.');
        } else {
            console.log('⚠️ Some tests failed. Please check the implementation.');
        }
        
        return {
            passed: this.passed,
            failed: this.failed,
            total: this.passed + this.failed,
            results: this.results
        };
    }
}

// Auto-run tests if in development mode
if (window.location.hostname === 'localhost' || window.location.hostname.includes('127.0.0.1')) {
    document.addEventListener('DOMContentLoaded', () => {
        // Wait a bit for components to initialize
        setTimeout(() => {
            const tester = new MediaLibraryTest();
            tester.runAllTests();
        }, 2000);
    });
}

// Make test class available globally for manual testing
window.MediaLibraryTest = MediaLibraryTest;

export { MediaLibraryTest };