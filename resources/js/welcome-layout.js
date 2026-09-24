import Alpine from 'alpinejs';

Alpine.data('welcomeLayout', () => ({
    darkMode: document.documentElement.classList.contains('dark') || localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
    activeRole: 'pm',
    
    init() {
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        window.addEventListener('theme-changed', (e) => {
            if (e.detail && typeof e.detail.darkMode === 'boolean') {
                this.darkMode = e.detail.darkMode;
            }
        });
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
