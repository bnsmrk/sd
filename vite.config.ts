import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),

        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    // server: {
    //     host: '192.168.169.6', // 👈 use your machine's IP
    //     port: 5173,
    //     hmr: {
    //         host: '192.168.169.6', // 👈 ensure hot-reload also uses same IP
    //     },
    // },
    // resolve: {
    //     alias: {
    //         '@': path.resolve(__dirname, './resources/js'),
    //     },
    // },
});
