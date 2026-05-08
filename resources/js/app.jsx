import './bootstrap';
import '../css/app.css';

import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

const appName = import.meta.env.VITE_APP_NAME || 'PlexusBiz Automate';

const collapseLeadingPublicRepeat = (pathname) => {
    if (typeof pathname !== 'string' || pathname === '') {
        return pathname;
    }

    const hasTrailingSlash = pathname.endsWith('/');
    let segments = pathname.split('/').filter(Boolean);

    if (segments.length < 4) {
        return pathname;
    }

    let updated = false;

    do {
        updated = false;

        for (let len = Math.floor(segments.length / 2); len >= 2; len -= 1) {
            const head = segments.slice(0, len);
            const next = segments.slice(len, len * 2);

            if (head.length === next.length && head.every((item, index) => item === next[index]) && head.includes('public')) {
                segments = [...head, ...segments.slice(len * 2)];
                updated = true;
                break;
            }
        }
    } while (updated);

    const collapsed = `/${segments.join('/')}`;

    if (collapsed !== '/' && hasTrailingSlash) {
        return `${collapsed}/`;
    }

    return collapsed;
};

const extractPublicBase = (pathname) => {
    if (typeof pathname !== 'string' || pathname === '') {
        return '';
    }

    const segments = pathname.split('/').filter(Boolean);
    const publicIndex = segments.indexOf('public');

    if (publicIndex <= 0) {
        return '';
    }

    return `/${segments.slice(0, publicIndex + 1).join('/')}`;
};

const canonicalizeDuplicateSubfolderUrl = () => {
    if (typeof window === 'undefined') {
        return;
    }

    const currentPath = window.location.pathname || '/';
    const canonicalPath = collapseLeadingPublicRepeat(currentPath);

    if (canonicalPath !== currentPath) {
        const canonicalUrl = `${canonicalPath}${window.location.search}${window.location.hash}`;
        window.history.replaceState(window.history.state, '', canonicalUrl);
    }

    const publicBase = extractPublicBase(canonicalPath);

    if (publicBase === '') {
        return;
    }

    window.__APP_BASE_PATH__ = publicBase;

    if (!window.Ziggy || typeof window.Ziggy.url !== 'string' || window.Ziggy.url === '') {
        return;
    }

    try {
        const parsed = new URL(window.Ziggy.url, window.location.origin);
        parsed.pathname = publicBase;
        window.Ziggy.url = parsed.toString().replace(/\/$/, '');
    } catch {
        // Keep runtime stable if Ziggy URL parsing fails.
    }
};

const canonicalizeRouteOutput = (urlLike) => {
    if (typeof urlLike !== 'string' || urlLike.trim() === '') {
        return urlLike;
    }

    try {
        if (/^[a-z][a-z0-9+.-]*:\/\//i.test(urlLike)) {
            const parsed = new URL(urlLike);
            parsed.pathname = collapseLeadingPublicRepeat(parsed.pathname);
            return parsed.toString();
        }
    } catch {
        return urlLike;
    }

    if (urlLike.startsWith('/')) {
        return collapseLeadingPublicRepeat(urlLike);
    }

    return urlLike;
};

const patchGlobalRouteHelper = () => {
    if (typeof window === 'undefined' || typeof window.route !== 'function') {
        return;
    }

    const originalRoute = window.route.bind(window);

    window.route = (...args) => canonicalizeRouteOutput(originalRoute(...args));
};

canonicalizeDuplicateSubfolderUrl();
patchGlobalRouteHelper();

createInertiaApp({
    title: (title) => (title ? `${title} | ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./Pages/${name}.jsx`, import.meta.glob('./Pages/**/*.jsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(<App {...props} />);
    },
    progress: {
        color: '#4B5563',
    },
});
