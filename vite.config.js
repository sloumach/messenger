import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    // Charge les variables d’environnement du fichier .env
    const env = loadEnv(mode, process.cwd(), 'VITE_');

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
        ],
        define: {
            'import.meta.env': {
                VITE_REVERB_APP_KEY: JSON.stringify(env.VITE_REVERB_APP_KEY),
                VITE_REVERB_HOST: JSON.stringify(env.VITE_REVERB_HOST),
                VITE_REVERB_PORT: JSON.stringify(env.VITE_REVERB_PORT),
                VITE_REVERB_SCHEME: JSON.stringify(env.VITE_REVERB_SCHEME),
            },
        },
    };
});
