import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: 'localhost',
        hmr: {
            host: 'localhost',
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/home/home.css',
                'resources/js/home/home.js',
                'resources/css/home/study-progress.css',
                'resources/js/home/study-progress.js',
                'resources/css/home/profile-menu.css',
                'resources/css/settings.css',
                'resources/js/settings.js',
                'resources/css/admin/admin-qualifications.css',
                'resources/js/admin/admin-qualifications.js',
                'resources/css/admin/admin-backups.css',
                'resources/js/admin/admin-backups.js',
                'resources/css/admin/admin-users.css',
                'resources/js/admin/admin-users.js',
                'resources/js/admin/profile-menu.js',
            ],
            refresh: true,
        }),
    ],
});
