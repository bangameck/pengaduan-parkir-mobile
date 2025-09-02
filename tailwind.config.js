
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            // TAMBAHKAN INI
            colors: {
                'dishub-blue': {
                    '50': '#eef7ff',
                    '100': '#d9eeff',
                    '200': '#bce3ff',
                    '300': '#8ed5ff',
                    '400': '#5ac2ff',
                    '500': '#31a9ff',
                    '600': '#178fff',
                    '700': '#0f75ff',
                    '800': '#1562d7', // Warna utama sidebar
                    '900': '#1755b3',
                    '950': '#11346c',
                },
                'dishub-yellow': {
                    '50': '#fefce8',
                    '100': '#fef9c3',
                    '200': '#fef08a',
                    '300': '#fde047', // Warna utama aksen
                    '400': '#facc15',
                    '500': '#eab308',
                    '600': '#ca8a04',
                    '700': '#a16207',
                    '800': '#854d0e',
                    '900': '#713f12',
                    '950': '#422006',
                },
            }
            // SAMPAI SINI
        },
    },

    plugins: [forms],
};
