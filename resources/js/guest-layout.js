export function updateThemeIcon() {
    const icon = document.getElementById('theme-toggle-icon');
    if (!icon) return;
    
    if (document.documentElement.classList.contains('dark')) {
        icon.textContent = 'light_mode';
        icon.classList.remove('text-zinc-650');
        icon.classList.add('text-amber-400');
    } else {
        icon.textContent = 'dark_mode';
        icon.classList.remove('text-amber-400');
        icon.classList.add('text-zinc-650');
    }
}

export function toggleTheme() {
    if (document.documentElement.classList.contains('dark')) {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    } else {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    }
    updateThemeIcon();
}

// Centralized Entrance Animation for Login & Register Card
export function triggerPageLoadAnimation() {
    if (window.innerWidth < 768) return;
    
    const gp = document.getElementById('green-panel');
    const fp = document.getElementById('form-panel');
    if (!gp || !fp) return;
    
    gp.style.transition = 'none';
    fp.style.transition = 'none';
    
    const path = window.location.pathname;
    if (path.includes('/register')) {
        gp.style.transform = 'translateX(-140%)';
        gp.style.opacity = '0';
        fp.style.transform = 'translateX(60%)';
        fp.style.opacity = '0';
    } else {
        gp.style.transform = 'translateX(140%)';
        gp.style.opacity = '0';
        fp.style.transform = 'translateX(-60%)';
        fp.style.opacity = '0';
    }
    
    // Force Reflow
    gp.offsetHeight;
    fp.offsetHeight;
    
    // Trigger transition
    gp.style.transition = 'transform 600ms cubic-bezier(0.16, 1, 0.3, 1), opacity 600ms ease';
    fp.style.transition = 'transform 600ms cubic-bezier(0.16, 1, 0.3, 1), opacity 600ms ease';
    gp.style.transform = 'translateX(0)';
    gp.style.opacity = '1';
    fp.style.transform = 'translateX(0)';
    fp.style.opacity = '1';
}

// Expose functions globally to be accessible by inline HTML event handlers
window.updateThemeIcon = updateThemeIcon;
window.toggleTheme = toggleTheme;
window.triggerPageLoadAnimation = triggerPageLoadAnimation;

document.addEventListener('DOMContentLoaded', () => {
    updateThemeIcon();
    triggerPageLoadAnimation();
});
