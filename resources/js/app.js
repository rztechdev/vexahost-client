// Inline theme check to prevent flickering (FOUC)
if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
} else {
    document.documentElement.classList.remove('dark');
}

import './bootstrap';

// Initialize global user ID from meta tag
const userIdMeta = document.querySelector('meta[name="user-id"]');
window.authUserId = userIdMeta ? userIdMeta.getAttribute('content') : null;

import './notifications';
import './sweetalert';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Import Layout scripts
import './app-layout';
import './guest-layout';
import './welcome-layout';

// Lazy load dashboard-charts only when the dashboard page is loaded
if (document.getElementById('dashboard-data')) {
    import('./dashboard-charts');
}

Alpine.start();
