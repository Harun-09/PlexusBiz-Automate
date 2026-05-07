const ABSOLUTE_PROTOCOL_PATTERN = /^[a-z][a-z0-9+.-]*:/i;

const normalizeBasePath = (value) => {
    if (typeof value !== 'string') {
        return '';
    }

    const trimmed = value.trim();

    if (trimmed === '' || trimmed === '/') {
        return '';
    }

    let withoutSlashes = trimmed.replace(/^\/+|\/+$/g, '');

    if (withoutSlashes === 'public') {
        return '';
    }

    if (withoutSlashes.endsWith('/public')) {
        withoutSlashes = withoutSlashes.slice(0, -7);
    }

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

    const value = href.trim();

    if (value === '' || isExternalHref(value) || !value.startsWith('/')) {
        return value;
    }

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
