import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                cdf: {
                    deep: '#0B3B5E',
                    teal: '#00A7B7',
                    amber: '#FFC857',
                    sand: '#F4F1EA',
                    ink: '#14161A',
                    slate700: '#334155',
                    slate200: '#E2E8F0',
                },
            },
        },
    },

    plugins: [forms],
};
