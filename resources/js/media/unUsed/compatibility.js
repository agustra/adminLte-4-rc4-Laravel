// Compatibility layer for old MediaLibrary usage
// This ensures old code still works while we transition

// Re-export new MediaLibrary as old one
export { MediaLibrary } from '../../components/media/MediaLibrary.js';

// Global compatibility
import { MediaLibrary } from '../../components/media/MediaLibrary.js';
window.MediaLibrary = MediaLibrary;

console.log('📦 MediaLibrary compatibility layer loaded - old imports will still work');