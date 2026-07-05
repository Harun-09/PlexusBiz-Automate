import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import { VitePWA } from 'vite-plugin-pwa';

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
        // Keep Vite cache out of node_modules to avoid Windows EPERM locks
        // while unlinking pre-bundled dependency files.
        cacheDir: 'storage/framework/vite',
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
            VitePWA({
                registerType: 'autoUpdate',
                workbox: {
                    runtimeCaching: [
                        {
                            urlPattern: /^https:\/\/fonts\.(?:googleapis|gstatic)\.com\/.*/i,
                            handler: 'CacheFirst',
                            options: {
                                cacheName: 'google-fonts',
                                expiration: {
                                    maxEntries: 10,
                                    maxAgeSeconds: 60 * 60 * 24 * 365 // <== 365 days
                                },
                                cacheableResponse: {
                                    statuses: [0, 200]
                                }
                            }
                        },
                        {
                            urlPattern: /\.(?:png|gif|jpg|jpeg|svg)$/,
                            handler: 'StaleWhileRevalidate',
                            options: {
                                cacheName: 'images',
                                expiration: {
                                    maxEntries: 50,
                                    maxAgeSeconds: 30 * 24 * 60 * 60 // 30 Days
                                }
                            }
                        },
                        {
                            // Offline Cart API Sync
                            urlPattern: /\/api\/cart\/.*$/i,
                            handler: 'NetworkOnly',
                            options: {
                                backgroundSync: {
                                    name: 'cart-queue',
                                    options: {
                                        maxRetentionTime: 24 * 60 // Retry for max of 24 Hours
                                    }
                                }
                            }
                        }
                    ]
                }
            })
        ],
    };
});
