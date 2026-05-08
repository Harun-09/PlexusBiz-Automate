const ABSOLUTE_PROTOCOL_PATTERN = /^[a-z][a-z0-9+.-]*:/i;

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

const normalizeBasePath = (value) => {
    if (typeof value !== 'string') {
        return '';
    }

    const trimmed = value.trim();

    if (trimmed === '' || trimmed === '/') {
        return '';
    }

    let withoutSlashes = trimmed.replace(/^\/+|\/+$/g, '');

    const collapsed = collapseLeadingPublicRepeat(`/${withoutSlashes}`);
    withoutSlashes = collapsed.replace(/^\/+|\/+$/g, '');

    return withoutSlashes ? `/${withoutSlashes}` : '';
};

const splitPath = (value) => {
    const [pathAndQuery, hash = ''] = value.split('#');
    const [path = '', query = ''] = pathAndQuery.split('?');

    return {
        path,
        query: query ? `?${query}` : '',
        hash: hash ? `#${hash}` : '',
    };
};

export const appBasePath = () => {
    if (typeof window === 'undefined') {
        return '';
    }

    const explicitBasePath = normalizeBasePath(window.__APP_BASE_PATH__);

    if (explicitBasePath) {
        return explicitBasePath;
    }

    const ziggyUrl = window.Ziggy?.url;

    if (typeof ziggyUrl === 'string' && ziggyUrl !== '') {
        try {
            const parsed = new URL(ziggyUrl, window.location.origin);

            return normalizeBasePath(parsed.pathname);
        } catch {
            return '';
        }
    }

    return '';
};

export const isExternalHref = (href) => {
    if (typeof href !== 'string') {
        return false;
    }

    const value = href.trim();

    if (value === '') {
        return false;
    }

    return value.startsWith('#')
        || value.startsWith('//')
        || value.startsWith('mailto:')
        || value.startsWith('tel:')
        || value.startsWith('javascript:')
        || ABSOLUTE_PROTOCOL_PATTERN.test(value);
};

export const appHref = (href) => {
    if (typeof href !== 'string') {
        return href;
    }

    let value = href.trim();

    if (value === '' || isExternalHref(value) || !value.startsWith('/')) {
        return value;
    }

    value = collapseLeadingPublicRepeat(value);

    const basePath = appBasePath();

    if (!basePath || value === basePath || value.startsWith(`${basePath}/`)) {
        return value;
    }

    return `${basePath}${value}`;
};

export const assetHref = (path) => appHref(path);

export const normalizedPathAndQuery = (href) => {
    if (typeof href !== 'string' || href.trim() === '') {
        return '';
    }

    if (isExternalHref(href) && !href.startsWith('/')) {
        try {
            const parsed = new URL(href);
            return `${parsed.pathname}${parsed.search}`;
        } catch {
            return href;
        }
    }

    const resolved = appHref(href);
    const { path, query } = splitPath(resolved);
    const normalizedBase = appBasePath();

    if (!normalizedBase || !path.startsWith(normalizedBase)) {
        return `${path || '/'}${query}`;
    }

    const strippedPath = path.slice(normalizedBase.length) || '/';

    return `${strippedPath}${query}`;
};
