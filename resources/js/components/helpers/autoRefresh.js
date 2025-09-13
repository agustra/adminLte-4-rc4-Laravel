/**
 * Auto Refresh Helper
 * Automatically refresh data when tab becomes active/focused
 * Supports callback functions for maximum flexibility
 */
export function initAutoRefresh(refreshCallback) {
    if (typeof refreshCallback !== "function") {
        console.warn("AutoRefresh: Refresh callback function is required");
        return;
    }

    function handleRefresh() {
        refreshCallback();
    }

    // Refresh saat tab browser fokus/aktif
    window.addEventListener("focus", handleRefresh);

    // Return cleanup function
    return function cleanup() {
        window.removeEventListener("focus", handleRefresh);
    };
}

/**
 * Multi API Refresh Helper
 * For refreshing multiple API endpoints
 */
export function initMultiApiRefresh(apiUrls, onDataReceived) {
    if (!Array.isArray(apiUrls) || apiUrls.length === 0) {
        console.warn("MultiApiRefresh: Array of API URLs is required");
        return;
    }

    async function refreshMultipleApis() {
        try {
            const promises = apiUrls.map((url) =>
                fetch(url).then((response) => response.json())
            );

            const results = await Promise.all(promises);

            if (typeof onDataReceived === "function") {
                onDataReceived(results);
            }
        } catch (error) {
            console.error("MultiApiRefresh error:", error);
        }
    }

    return initAutoRefresh(refreshMultipleApis);
}

// Export default untuk backward compatibility
export default initAutoRefresh;

/*
📝 CARA PENGGUNAAN:

1. Single Table/Grid:
   const table = new EasyDataTable(...);
   const cleanup = initAutoRefresh(() => {
       table.reload();
   });

2. Multi Mode (Table + Grid):
   const table = new EasyDataTable(...);
   const grid = new GridComponent(...);
   let currentMode = 'table';
   
   const cleanup = initAutoRefresh(() => {
       if (currentMode === 'table') {
           table.reload();
       } else {
           grid.reload();
       }
   });

3. Direct API Call (Paling Fleksibel):
   const cleanup = initAutoRefresh(async () => {
       const response = await fetch('/api/pinjaman/');
       const data = await response.json();
       
       if (currentMode === 'table') {
           updateTableData(data);
       } else {
           updateGridData(data);
       }
   });

4. Multiple API Endpoints:
   const cleanup = initMultiApiRefresh([
       '/api/pinjaman/',
       '/api/karyawan/',
       '/api/dashboard/stats'
   ], (results) => {
       const [pinjamanData, karyawanData, statsData] = results;
       updateUI(pinjamanData, karyawanData, statsData);
   });

5. Cleanup (Optional):
   // Panggil saat component di-destroy
   cleanup();
*/
