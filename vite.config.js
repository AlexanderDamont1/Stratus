import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
<<<<<<< HEAD
            host: '192.168.1.92', 
=======
            host: '192.168.100.8', 
>>>>>>> e4c1aaa6b73c3e7cee981f8eee8c0a78cc7b1558
        },
    },
});
