// Badge Config Cache Manager for Efficient Badge Updates
import axiosClient from "@api/axiosClient.js";

class BadgeConfigCache {
    constructor() {
        this.cache = null;
        this.cacheKey = 'badge_config_urls';
        this.lastUpdated = null;
        this.cacheTimeout = 5 * 60 * 1000; // 5 minutes
        
        // Load from localStorage on init
        this.loadFromStorage();
        
        // Listen for cache clear events
        document.addEventListener('badgeConfigChanged', () => {
            this.clearCache();
        });
    }
    
    /**
     * Get active badge config URLs
     */
    async getActiveUrls() {
        if (!this.cache || this.isCacheExpired()) {
            await this.refreshCache();
        }
        return this.cache || [];
    }
    
    /**
     * Check if URL should trigger badge update
     */
    async shouldUpdateBadge(url) {
        const activeUrls = await this.getActiveUrls();
        const urlResource = this.extractResource(url);
        
        return activeUrls.some(configUrl => {
            const configResource = this.extractResource(configUrl);
            return urlResource === configResource;
        });
    }
    
    /**
     * Refresh cache from API
     */
    async refreshCache() {
        try {
            const response = await axiosClient.get('/api/menu/active-urls');
            if (response.data.success) {
                this.cache = response.data.urls;
                this.lastUpdated = Date.now();
                this.saveToStorage();
            }
        } catch (error) {
            console.error('❌ Error refreshing badge config cache:', error);
            // Fallback to empty array if API fails
            this.cache = [];
        }
    }
    
    /**
     * Clear cache
     */
    clearCache() {
        this.cache = null;
        this.lastUpdated = null;
        localStorage.removeItem(this.cacheKey);
        localStorage.removeItem(this.cacheKey + '_timestamp');
    }
    
    /**
     * Check if cache is expired
     */
    isCacheExpired() {
        if (!this.lastUpdated) return true;
        return (Date.now() - this.lastUpdated) > this.cacheTimeout;
    }
    
    /**
     * Load cache from localStorage
     */
    loadFromStorage() {
        try {
            const cached = localStorage.getItem(this.cacheKey);
            const timestamp = localStorage.getItem(this.cacheKey + '_timestamp');
            
            if (cached && timestamp) {
                this.cache = JSON.parse(cached);
                this.lastUpdated = parseInt(timestamp);
            }
        } catch (error) {
            // Ignore localStorage errors
            this.clearCache();
        }
    }
    
    /**
     * Save cache to localStorage
     */
    saveToStorage() {
        try {
            localStorage.setItem(this.cacheKey, JSON.stringify(this.cache));
            localStorage.setItem(this.cacheKey + '_timestamp', this.lastUpdated.toString());
        } catch (error) {
            // Ignore localStorage errors (quota exceeded, etc.)
        }
    }
    
    /**
     * Extract path from URL for comparison
     */
    extractPath(url) {
        try {
            // Remove protocol, domain, and query params
            return url.replace(/^https?:\/\/[^\/]+/, '').split('?')[0].toLowerCase();
        } catch (error) {
            return url.toLowerCase();
        }
    }
    
    /**
     * Extract resource name from URL
     */
    extractResource(url) {
        try {
            const path = this.extractPath(url);
            // Extract resource: /api/permissions -> permissions, /admin/roles -> roles
            const match = path.match(/\/(api|admin)\/([^/]+)/);
            return match ? match[2] : null;
        } catch (error) {
            return null;
        }
    }
}

// Export singleton instance
export const badgeCache = new BadgeConfigCache();