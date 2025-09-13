export function initializeDarkMode() {
    const themeDropdown = document.getElementById("theme-dropdown");
    
    // Get theme based on real time (day/night)
    function getSystemTheme() {
        const hour = new Date().getHours();
        return (hour >= 18 || hour < 6) ? 'dark' : 'light';
    }

    // Apply theme
    function applyTheme(mode) {
        let actualTheme = mode === 'system' ? getSystemTheme() : mode;
        
        document.documentElement.setAttribute("data-bs-theme", actualTheme);
        localStorage.setItem("themeMode", mode);
        
        // Update dropdown selection
        if (themeDropdown) {
            themeDropdown.value = mode;
        }
        
        // Dispatch theme change event
        document.dispatchEvent(new CustomEvent("themeChanged", { 
            detail: { 
                mode: mode,           // 'light', 'dark', or 'system'
                theme: actualTheme,   // 'light' or 'dark' (actual applied theme)
                actualTheme: actualTheme  // backward compatibility
            } 
        }));
        
        return { mode, actualTheme };
    }

    // Initialize theme
    const savedMode = localStorage.getItem("themeMode") || "system";
    applyTheme(savedMode);

    // Check time every minute for system mode
    setInterval(() => {
        const currentMode = localStorage.getItem("themeMode");
        if (currentMode === 'system') {
            applyTheme('system');
        }
    }, 60000);

    // Handle dropdown change
    if (themeDropdown) {
        themeDropdown.addEventListener("change", function (e) {
            const selectedMode = e.target.value;
            const result = applyTheme(selectedMode);
            
            // Show toast notification
            if (typeof showToast === 'function') {
                const modeNames = {
                    'light': 'Light mode',
                    'dark': 'Dark mode',
                    'system': `Auto mode (${result.actualTheme})`
                };
                showToast(modeNames[selectedMode], "success");
            }
        });
    }
}