/**
 * Password Toggle Functionality
 * Toggles password visibility for input fields with toggle button
 */

export function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const toggleIcon = document.getElementById(inputId + 'Toggle');
    
    if (!input || !toggleIcon) {
        console.error('Password toggle elements not found for ID:', inputId);
        return;
    }
    
    if (input.type === 'password') {
        input.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Make function globally available
// window.togglePassword = togglePassword;