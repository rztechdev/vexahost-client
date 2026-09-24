import Alpine from 'alpinejs';

Alpine.data('appLayout', () => ({
    sidebarOpen: false,
    darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
    
    init() {
        // Ensure any legacy collapsed class and key are removed
        document.documentElement.classList.remove('sidebar-collapsed');
        localStorage.removeItem('sidebarOpen');

        // Theme initialization
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },
    
    toggleTheme() {
        this.darkMode = !this.darkMode;
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { darkMode: this.darkMode } }));
    }
}));
