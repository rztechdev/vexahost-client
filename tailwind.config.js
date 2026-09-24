import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import colors from 'tailwindcss/colors';

/**
 * Palet brand VexaHost (sama dengan "vexahost - build"):
 * primary #FB923C (400) · deep #EA580C (600) · deep hover #C2410C (700) · cream #FFF7ED (50)
 */
const vexaOrange = {
    ...colors.orange,
    DEFAULT: '#EA580C',
};

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
                mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
            },
            borderRadius: {
                none: '0',
                xs: 'calc(0.625rem - 6px)',
                sm: 'calc(0.625rem - 4px)',
                DEFAULT: 'calc(0.625rem - 2px)',
                md: 'calc(0.625rem - 2px)',
                lg: '0.625rem',
                xl: '0.625rem',
                '2xl': '0.625rem',
                '3xl': '0.75rem',
                full: '9999px',
            },
            boxShadow: {
                xs: '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                sm: '0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1)',
                DEFAULT: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1)',
                md: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1)',
                lg: '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1)',
                xl: '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1)',
                '2xl': '0 25px 50px -12px rgba(0, 0, 0, 0.25)',
                card: '0 1px 3px 0 rgba(0, 0, 0, 0.06), 0 1px 2px 0 rgba(0, 0, 0, 0.04)',
                glow: '0 0 20px -5px rgba(234, 88, 12, 0.35)',
            },
            colors: {
                // Seluruh aksen UI (emerald/indigo lama) diarahkan ke oranye VexaHost
                brand: vexaOrange,
                emerald: vexaOrange,
                indigo: vexaOrange,
                gray: colors.zinc,
                slate: colors.zinc,
                zinc: colors.zinc,
            },
        },
    },

    plugins: [forms],
};
