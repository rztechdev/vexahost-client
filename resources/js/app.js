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
import Chart from 'chart.js/auto';
import Alpine from 'alpinejs';

window.Chart = Chart;
window.Alpine = Alpine;

// Import Layout scripts
import './app-layout';
import './guest-layout';
import './dashboard-charts';
import './welcome-layout';

Alpine.start();
