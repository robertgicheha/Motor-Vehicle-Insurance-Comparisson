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
                    blue: '#0066B3',
                    'blue-dark': '#004f8c',
                    'blue-light': '#e8f2fa',
                    red: '#D0021B',
                   
                },
                micblue: '#005F9E',
                ga: '#005baa',
                sanlam: '#1d3c6e',
            },
        },
    },

    plugins: [forms],
};
