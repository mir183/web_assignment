// Dark Theme JavaScript - Include this in all your pages

// Dark Theme Toggle Functionality
function initializeDarkTheme() {
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const body = document.body;

    if (!themeToggle) return; // Exit if toggle button doesn't exist

    // Load saved theme or default to light
    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);

    // Theme toggle event listener
    themeToggle.addEventListener('click', () => {
        const currentTheme = body.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
        localStorage.setItem('theme', newTheme);
    });

    function setTheme(theme) {
        body.setAttribute('data-theme', theme);
        
        if (themeIcon) {
            if (theme === 'dark') {
                themeIcon.className = 'bi bi-sun-fill';
                themeToggle.title = 'Switch to Light Mode';
            } else {
                themeIcon.className = 'bi bi-moon-fill';
                themeToggle.title = 'Switch to Dark Mode';
            }
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeDarkTheme);

// Also initialize immediately if DOM is already loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeDarkTheme);
} else {
    initializeDarkTheme();
}