import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
                montserrat: ['Montserrat', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    blue: '#263B8B',
                    'blue-dark': '#1b2c6b',
                    'blue-light': '#e9ecf7',
                    red: '#ED1D25',
                   
                },
                micblue: '#263B8B',
                ga: '#005baa',
                sanlam: '#1d3c6e',
                lolc: {
                    red: '#ED1D25',
                    'red-dark': '#B6161C',
                    navy: '#263B8B',
                },
            },
        },
    },

    plugins: [forms],
};
