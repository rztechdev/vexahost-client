document.addEventListener('DOMContentLoaded', () => {
    if (window.authUserId && window.Echo) {
        window.Echo.private(`App.Models.User.${window.authUserId}`)
            .notification((notification) => {
                showToast(notification.message ?? 'Ada pembaruan baru.');
                updateBadge(1);
            });
    }
});

function showToast(message) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = 'max-w-xs bg-[#1E293B] border border-[#334155] rounded-xl shadow-lg transition-all duration-300 ease-out transform translate-y-4 opacity-0 mb-3 pointer-events-auto';
    toast.innerHTML = `
        <div class="flex p-4 gap-3">
            <span class="material-symbols-outlined text-[#38BDF8] text-[20px]">notifications_active</span>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white">Notifikasi Baru</p>
                <p class="text-xs text-[#94A3B8] mt-1">${message}</p>
            </div>
            <button type="button" class="text-[#64748B] hover:text-white shrink-0" onclick="this.closest('.max-w-xs').remove()" aria-label="Tutup">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
    `;

    container.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.remove('opacity-0', 'translate-y-4');
        toast.classList.add('opacity-100', 'translate-y-0');
    });

    setTimeout(() => {
        toast.classList.remove('opacity-100', 'translate-y-0');
        toast.classList.add('opacity-0', 'translate-y-4');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

function updateBadge(delta = 1) {
    const badge = document.getElementById('notification-badge');
    const countLabel = document.getElementById('notification-count-label');
    if (!badge) return;

    const current = parseInt(badge.textContent || '0', 10);
    const next = Math.max(0, current + delta);

    badge.textContent = next;
    if (countLabel) {
        countLabel.textContent = `${next} Baru`;
    }

    if (next > 0) {
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }
}

function showEmptyNotificationsState() {
    const list = document.getElementById('notification-list');
    if (!list || list.querySelector('[data-notification-empty]')) return;

    list.innerHTML = `
        <div data-notification-empty class="px-4 py-8 text-center bg-[#1E293B]">
            <span class="material-symbols-outlined text-[#475569] text-[36px] block mb-2">notifications_off</span>
            <p class="text-xs text-[#64748B] italic">Kotak masuk bersih. Tidak ada notifikasi baru.</p>
        </div>
    `;
}

window.showToast = showToast;

window.markAsRead = async function (id, btn) {
    if (!window.axios) {
        console.error('Axios belum dimuat. Pastikan Vite assets berjalan.');
        return;
    }

    const item = btn.closest('[data-notification-item]');
    btn.disabled = true;
    btn.textContent = 'Memproses...';

    try {
        await window.axios.post(`/notifications/${id}/read`);
        if (item) {
            item.remove();
        }
        updateBadge(-1);

        const list = document.getElementById('notification-list');
        if (list && !list.querySelector('[data-notification-item]')) {
            showEmptyNotificationsState();
        }
    } catch (e) {
        console.error('Failed to mark notification as read', e);
        btn.disabled = false;
        btn.textContent = 'Tandai Dibaca';
    }
};
