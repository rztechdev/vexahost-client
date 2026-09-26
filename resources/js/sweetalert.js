import Swal from 'sweetalert2';

const isDark = () => document.documentElement.classList.contains('dark');

const getThemeColors = () => {
    const dark = isDark();
    return {
        popup: dark ? '#18181b' : '#ffffff',
        border: dark ? '#27272a' : '#e4e4e7',
        title: dark ? '#fafafa' : '#18181b',
        text: dark ? '#a1a1aa' : '#52525b',
        confirmBg: '#BAFF39',
        confirmHover: '#a6ec27',
        cancelBg: dark ? '#27272a' : '#ffffff',
        cancelBorder: dark ? '#71717a' : '#d4d4d8',
        cancelText: dark ? '#d4d4d8' : '#3f3f46',
        dangerBg: '#e11d48',
        dangerHover: '#be123c',
    };
};

const themed = (customOptions = {}) => {
    const c = getThemeColors();
    return Swal.mixin({
        background: c.popup,
        color: c.title,
        customClass: {
            popup: 'vh-swal-popup',
            title: 'vh-swal-title',
            htmlContainer: 'vh-swal-text',
            confirmButton: 'vh-swal-confirm',
            cancelButton: 'vh-swal-cancel',
            denyButton: 'vh-swal-deny',
        },
        buttonsStyling: false,
        ...customOptions,
    });
};

window.VhSwal = {
    alert(message, icon = 'info') {
        const c = getThemeColors();
        return themed().fire({
            text: message,
            icon: icon,
            confirmButtonText: 'OK',
            background: c.popup,
            color: c.title,
        });
    },

    confirm(message, onConfirm) {
        const c = getThemeColors();
        return themed().fire({
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal',
            background: c.popup,
            color: c.title,
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed && onConfirm) {
                onConfirm();
            }
            return result;
        });
    },

    confirmDelete(message, formEl) {
        const c = getThemeColors();
        return themed().fire({
            title: 'Konfirmasi Hapus',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            background: c.popup,
            color: c.title,
            reverseButtons: true,
            customClass: {
                popup: 'vh-swal-popup',
                title: 'vh-swal-title',
                htmlContainer: 'vh-swal-text',
                confirmButton: 'vh-swal-danger',
                cancelButton: 'vh-swal-cancel',
                denyButton: 'vh-swal-deny',
            },
        }).then((firstResult) => {
            if (firstResult.isConfirmed) {
                return themed().fire({
                    title: 'Apakah Anda benar-benar yakin?',
                    text: 'Data yang dihapus tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Saya Yakin',
                    cancelButtonText: 'Batal',
                    background: c.popup,
                    color: c.title,
                    reverseButtons: true,
                    customClass: {
                        popup: 'vh-swal-popup',
                        title: 'vh-swal-title',
                        htmlContainer: 'vh-swal-text',
                        confirmButton: 'vh-swal-danger',
                        cancelButton: 'vh-swal-cancel',
                        denyButton: 'vh-swal-deny',
                    },
                });
            }
            return firstResult;
        }).then((finalResult) => {
            if (finalResult.isConfirmed && formEl) {
                formEl.submit();
            }
            return finalResult;
        });
    },

    success(message) {
        return this.alert(message, 'success');
    },

    error(message) {
        return this.alert(message, 'error');
    },

    warning(message) {
        return this.alert(message, 'warning');
    },
};

window.Swal = Swal;
