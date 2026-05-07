import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

function normalizeBuildBase(baseValue) {
    const trimmed = (baseValue ?? '').trim();

    if (trimmed === '') {
        return '/build/';
    }

    const normalized = `/${trimmed.replace(/^\/+|\/+$/g, '')}/`;

    return normalized === '//' ? '/build/' : normalized;
}

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const buildBase = normalizeBuildBase(env.VITE_BUILD_BASE);

    return {
        base: buildBase,
        build: {
            manifest: 'manifest.json',
            // Windows on Laragon can intermittently lock public/build/assets.
            // Keep old hashed assets and avoid pre-build directory cleanup failures.
            emptyOutDir: false,
        },
        plugins: [
            laravel({
                input: 'resources/js/app.jsx',
                refresh: true,
            }),
            react(),
        ],
    };
});
