import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                // Mantenemos Figtree como la fuente principal
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Sobrescribimos la paleta de grises con tus tonos "Night"
                gray: {
                    700: '#25262c', // Bordes e interacciones (hover)
                    800: '#1a1c21', // Fondo de tarjetas (Cards)
                    900: '#0e0f15', // Sidebar o secciones secundarias
                    950: '#0C0C14', // Fondo principal (Background total)
                },
               
            },
        },
    },

    plugins: [forms],
};