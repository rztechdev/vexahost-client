import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import colors from 'tailwindcss/colors';

/**
 * Palet brand VexaHost:
 * #BAFF39 (Electric Lime)
 */
const vexaBrand = {
    50: '#f7fee7',
    100: '#f0fdd4',
    200: '#e1fba8',
    300: '#d0f870',
    400: '#c2f84a',
    500: '#BAFF39',
    600: '#99db1e',
    700: '#77ac17',
    800: '#5a8215',
    900: '#466614',
    950: '#223506',
    DEFAULT: '#BAFF39',
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
                glow: '0 0 20px -5px rgba(186, 255, 57, 0.45)',
            },
            colors: {
                brand: vexaBrand,
                emerald: vexaBrand,
                indigo: vexaBrand,
                orange: vexaBrand,
                lime: {
                    ...colors.lime,
                    DEFAULT: '#BAFF39',
                },
                gray: colors.zinc,
                slate: colors.zinc,
                zinc: colors.zinc,
            },
        },
    },

    plugins: [forms],
};
