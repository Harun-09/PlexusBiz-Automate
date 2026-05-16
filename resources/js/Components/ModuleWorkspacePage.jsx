import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import EmptyState from '@/Components/EmptyState';
import KpiCard from '@/Components/KpiCard';
import PageHeader from '@/Components/PageHeader';
import { canAccess } from '@/Utils/access';
import { actionButtonClasses, statusBadgeClasses, statusFilterChipClasses } from '@/Utils/pillStyles';
import { Head, Link, router, useForm } from '@inertiajs/react';

const METRIC_TONES = ['blue', 'emerald', 'amber', 'rose'];

const MODULE_THEMES = {
    slate: {
        hero: 'bg-white',
        accent: 'bg-slate-900',
        badge: 'border-slate-200 bg-slate-50 text-slate-700',
        title: 'text-slate-950',
        copy: 'text-slate-600',
        card: 'border-slate-200 bg-slate-50',
        cardLabel: 'text-slate-500',
        cardText: 'text-slate-700',
        side: 'border-slate-200 bg-slate-50',
        sideCopy: 'text-slate-600',
        chipActive: 'border-slate-900 bg-slate-900 text-white shadow-sm',
        chipInactive: 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50',
        filterFrame: 'border-slate-200',
        actionPrimary: 'border-slate-900 bg-slate-900 text-white hover:bg-slate-800',
        actionSecondary: 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50',
    },
    social: {
        hero: 'bg-white',
        accent: 'bg-sky-600',
        badge: 'border-sky-200 bg-sky-50 text-sky-700',
        title: 'text-slate-950',
        copy: 'text-slate-600',
        card: 'border-sky-100 bg-sky-50',
        cardLabel: 'text-sky-700',
        cardText: 'text-slate-700',
        side: 'border-sky-100 bg-sky-50',
        sideCopy: 'text-slate-600',
        chipActive: 'border-sky-600 bg-sky-600 text-white shadow-sm',
        chipInactive: 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50',
        filterFrame: 'border-sky-100',
        actionPrimary: 'border-sky-700 bg-sky-700 text-white hover:bg-sky-800',
        actionSecondary: 'border-sky-200 bg-white text-sky-700 hover:bg-sky-50',
    },
    marketing: {
        hero: 'bg-white',
        accent: 'bg-rose-600',
        badge: 'border-rose-200 bg-rose-50 text-rose-700',
        title: 'text-slate-950',
        copy: 'text-slate-600',
        card: 'border-rose-100 bg-rose-50',
        cardLabel: 'text-rose-700',
        cardText: 'text-slate-700',
        side: 'border-rose-100 bg-rose-50',
        sideCopy: 'text-slate-600',
        chipActive: 'border-rose-600 bg-rose-600 text-white shadow-sm',
        chipInactive: 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50',
        filterFrame: 'border-rose-100',
        actionPrimary: 'border-rose-700 bg-rose-700 text-white hover:bg-rose-800',
        actionSecondary: 'border-rose-200 bg-white text-rose-700 hover:bg-rose-50',
    },
    workflow: {
        hero: 'bg-white',
        accent: 'bg-teal-600',
        badge: 'border-teal-200 bg-teal-50 text-teal-700',
        title: 'text-slate-950',
        copy: 'text-slate-600',
        card: 'border-teal-100 bg-teal-50',
        cardLabel: 'text-teal-700',
        cardText: 'text-slate-700',
        side: 'border-teal-100 bg-teal-50',
        sideCopy: 'text-slate-600',
        chipActive: 'border-teal-600 bg-teal-600 text-white shadow-sm',
        chipInactive: 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50',
        filterFrame: 'border-teal-100',
        actionPrimary: 'border-teal-700 bg-teal-700 text-white hover:bg-teal-800',
        actionSecondary: 'border-teal-200 bg-white text-teal-700 hover:bg-teal-50',
    },
    support: {
        hero: 'bg-white',
        accent: 'bg-amber-500',
        badge: 'border-amber-200 bg-amber-50 text-amber-800',
        title: 'text-slate-950',
        copy: 'text-slate-600',
        card: 'border-amber-100 bg-amber-50',
        cardLabel: 'text-amber-800',
        cardText: 'text-slate-700',
        side: 'border-amber-100 bg-amber-50',
        sideCopy: 'text-slate-600',
        chipActive: 'border-amber-500 bg-amber-500 text-slate-950 shadow-sm',
        chipInactive: 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50',
        filterFrame: 'border-amber-100',
        actionPrimary: 'border-amber-500 bg-amber-500 text-slate-950 hover:bg-amber-400',
        actionSecondary: 'border-amber-200 bg-white text-amber-800 hover:bg-amber-50',
    },
    crm: {
        hero: 'bg-white',
        accent: 'bg-indigo-600',
        badge: 'border-indigo-200 bg-indigo-50 text-indigo-700',
        title: 'text-slate-950',
        copy: 'text-slate-600',
        card: 'border-indigo-100 bg-indigo-50',
        cardLabel: 'text-indigo-700',
        cardText: 'text-slate-700',
        side: 'border-indigo-100 bg-indigo-50',
        sideCopy: 'text-slate-600',
        chipActive: 'border-indigo-600 bg-indigo-600 text-white shadow-sm',
        chipInactive: 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50',
        filterFrame: 'border-indigo-100',
        actionPrimary: 'border-indigo-700 bg-indigo-700 text-white hover:bg-indigo-800',
        actionSecondary: 'border-indigo-200 bg-white text-indigo-700 hover:bg-indigo-50',
    },
};

const SearchIcon = () => (
    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" className="h-4 w-4">
        <circle cx="10.5" cy="10.5" r="5.75" stroke="currentColor" strokeWidth="1.8" />
        <path d="m15.25 15.25 4 4" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" />
    </svg>
);

const formatStatusLabel = (value) => {
    const normalized = String(value || '').replace(/[_-]/g, ' ').trim();

    if (normalized === '') {
        return '-';
    }

    return normalized
        .split(/\s+/)
        .filter(Boolean)
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1).toLowerCase())
        .join(' ');
};

const formatGatewayLabel = (value) => {
    const normalized = String(value || '').toLowerCase();

    if (!normalized || normalized === 'stripe') {
        return 'Stripe';
    }

    if (normalized === 'sslcommerz') {
        return 'SSLCOMMERZ';
    }

    return normalized
        .split(/[_-]/)
        .filter(Boolean)
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
        .join(' ');
};

const renderStatusPill = (status, label) => (
    <span className={`inline-flex rounded-md border px-2.5 py-1 text-xs font-semibold ${statusBadgeClasses(status)}`}>
        {label || status || '-'}
    </span>
);

const renderWorkspaceCell = (_column, value) => {
    if (value === null || value === undefined || value === '') {
        return <span className="text-slate-400">-</span>;
    }

    if (typeof value === 'object' && !Array.isArray(value)) {
        if (value.kind === 'payment-summary') {
            return (
                <div className="flex flex-col gap-1">
                    {renderStatusPill(value.status, formatStatusLabel(value.status))}
                    <span className="text-xs text-slate-500">{formatGatewayLabel(value.method)}</span>
                </div>
            );
        }

        if (value.kind === 'payment-action') {
            return (
                <div className="flex flex-col gap-1">
                    <Link
                        href={value.href}
                        method="post"
                        as="button"
                        preserveScroll
                        className={`inline-flex items-center justify-center rounded-lg border px-3.5 py-1.5 text-xs font-semibold transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('primary')}`}
                    >
                        {value.label}
                    </Link>
                    {value.gateway ? <span className="text-xs text-slate-500">via {value.gateway}</span> : null}
                </div>
            );
        }

        if (value.kind === 'json') {
            const jsonValue = typeof value.value === 'string'
                ? value.value
                : JSON.stringify(value.value ?? {}, null, 2);

            return (
                <pre className="max-h-64 overflow-auto whitespace-pre-wrap rounded-lg border border-slate-200 bg-slate-50 p-3 text-[11px] leading-5 text-slate-700">
                    {jsonValue}
                </pre>
            );
        }

        if (value.kind === 'post-action') {
            const buttonVariant = value.variant === 'secondary' ? 'secondary' : value.variant === 'danger' ? 'danger' : 'primary';
            const buttonClassName = `inline-flex items-center justify-center rounded-lg border px-3.5 py-1.5 text-xs font-semibold transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses(buttonVariant)}`;

            return (
                <div className="flex flex-col gap-1">
                    <Link
                        href={value.href}
                        method="post"
                        as="button"
                        preserveScroll
                        className={buttonClassName}
                    >
                        {value.label}
                    </Link>
                    {value.note ? <span className="text-xs text-slate-500">{value.note}</span> : null}
                </div>
            );
        }

        if (value.kind === 'stock') {
            return (
                <div className="flex flex-wrap items-center gap-2">
                    <span className={`text-sm font-semibold ${value.lowStock ? 'text-rose-600' : 'text-slate-700'}`}>
                        {value.value}
                    </span>
                    {value.lowStock ? (
                        <span className="inline-flex rounded-md border border-rose-200 bg-rose-50 px-2.5 py-1 text-[11px] font-bold uppercase text-rose-600">
                            Low stock
                        </span>
                    ) : null}
                </div>
            );
        }

        if (value.kind === 'link') {
            const isDelete = value.method === 'delete' || value.variant === 'danger';
            const normalizedLabel = String(value.label || '').toLowerCase();
            const inferredVariant = value.variant
                || (isDelete ? 'danger'
                    : normalizedLabel.includes('edit') ? 'secondary'
                        : normalizedLabel.includes('view') || normalizedLabel.includes('open') || normalizedLabel.includes('profile') || normalizedLabel.includes('manage') ? 'primary'
                            : 'neutral');
            const linkClassName = value.className || `inline-flex items-center justify-center rounded-lg border px-3.5 py-1.5 text-xs font-semibold transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses(inferredVariant)}`;

            if (isDelete) {
                return (
                    <button
                        type="button"
                        onClick={() => {
                            if (value.confirm && !window.confirm(value.confirm)) {
                                return;
                            }

                            router.delete(value.href, {
                                preserveScroll: value.preserveScroll ?? true,
                            });
                        }}
                        className={linkClassName}
                    >
                        {value.label}
                    </button>
                );
            }

            return (
                <Link
                    href={value.href}
                    preserveScroll={value.preserveScroll ?? true}
                    className={linkClassName}
                >
                    {value.label}
                </Link>
            );
        }

        if (value.kind === 'status') {
            return renderStatusPill(value.status, value.label || formatStatusLabel(value.status));
        }
    }

    if (Array.isArray(value)) {
        if (value.every((item) => item && typeof item === 'object' && !Array.isArray(item))) {
            return (
                <div className="flex flex-wrap gap-2">
                    {value.map((item, index) => (
                        <span key={item.label || item.href || index}>
                            {renderWorkspaceCell(null, item)}
                        </span>
                    ))}
                </div>
            );
        }

        return value.join(', ');
    }

    return String(value);
};

const themeFor = (key = 'slate') => MODULE_THEMES[key] || MODULE_THEMES.slate;

const ActionButton = ({ action, theme }) => {
    const isPrimary = action.variant !== 'secondary';
    const classes = isPrimary ? theme.actionPrimary : theme.actionSecondary;

    return (
        <Link
            href={action.href}
            className={`inline-flex items-center justify-center rounded-lg border px-4 py-2 text-sm font-semibold shadow-sm transition ${classes}`}
        >
            {action.label}
        </Link>
    );
};

export default function ModuleWorkspacePage({ auth, workspace, module = {} }) {
    const filters = workspace.filters || null;
    const theme = themeFor(module.theme);
    const rows = workspace.rows || [];
    const metrics = workspace.metrics || [];
    const statusOptions = filters?.statuses || [];
    const platformOptions = filters?.platforms || [];
    const visibleActions = (module.actions || []).filter((action) => canAccess(auth?.user, action));
    const currentPath = typeof window !== 'undefined' ? window.location.pathname : '';
    const { data, setData } = useForm({
        search: filters?.search || '',
        status: filters?.status || '',
        platform: filters?.platform || '',
    });

    const buildParams = (overrides = {}) => {
        const next = {
            search: data.search,
            status: data.status,
            platform: data.platform,
            ...overrides,
        };

        return Object.fromEntries(
            Object.entries(next).filter(([, value]) => value !== ''),
        );
    };

    const applyFilters = (overrides = {}) => {
        router.get(currentPath || '/', buildParams(overrides), {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        });
    };

    const submitFilters = (event) => {
        event.preventDefault();
        applyFilters();
    };

    const resetFilters = () => {
        setData({ search: '', status: '', platform: '' });

        router.get(currentPath || '/', {}, {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        });
    };

    const selectStatus = (status) => {
        setData('status', status);
        applyFilters({ status });
    };

    const selectPlatform = (platform) => {
        setData('platform', platform);
        applyFilters({ platform });
    };

    const moduleHighlights = module.highlights || [];
    const moduleBullets = module.panelBullets || module.highlights || [];

    return (
        <AuthenticatedLayout
            user={auth.user}
            showBreadcrumbs={!module.compactHeader}
            header={
                <PageHeader
                    compact={Boolean(module.compactHeader)}
                    eyebrow={module.eyebrow || 'Workspace'}
                    title={workspace.title}
                    description={workspace.description}
                />
            }
        >
            <Head title={workspace.title} />

            <div className="space-y-6">
                <section className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div className={`h-1.5 ${theme.accent}`} />
                    <div className={`grid gap-0 lg:grid-cols-[minmax(0,1.55fr)_360px] ${theme.hero}`}>
                        <div className="px-6 py-7 sm:px-8 sm:py-8">
                            <div className="flex flex-wrap gap-2">
                                <span className={`inline-flex rounded-md border px-3 py-1 text-xs font-bold uppercase ${theme.badge}`}>
                                    {module.tag || module.eyebrow || 'Module'}
                                </span>
                                <span className={`inline-flex rounded-md border px-3 py-1 text-xs font-bold uppercase ${theme.badge}`}>
                                    {rows.length} rows
                                </span>
                                {statusOptions.length > 0 ? (
                                    <span className={`inline-flex rounded-md border px-3 py-1 text-xs font-bold uppercase ${theme.badge}`}>
                                        {statusOptions.length} statuses
                                    </span>
                                ) : null}
                            </div>

                            <h1 className={`mt-4 text-2xl font-extrabold sm:text-3xl ${theme.title}`}>
                                {module.heroTitle || workspace.title}
                            </h1>
                            <p className={`mt-3 max-w-3xl text-sm leading-6 ${theme.copy}`}>
                                {module.heroCopy || workspace.description}
                            </p>

                            {moduleHighlights.length > 0 ? (
                                <div className="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                    {moduleHighlights.map((item) => (
                                        <div key={item.label} className={`rounded-lg border px-4 py-3 ${theme.card}`}>
                                            <p className={`text-xs font-bold uppercase ${theme.cardLabel}`}>
                                                {item.label}
                                            </p>
                                            <p className={`mt-1 text-sm leading-6 ${theme.cardText}`}>
                                                {item.detail}
                                            </p>
                                        </div>
                                    ))}
                                </div>
                            ) : null}

                            {visibleActions.length > 0 ? (
                                <div className="mt-6 flex flex-wrap gap-2">
                                    {visibleActions.map((action) => (
                                        <ActionButton key={action.href} action={action} theme={theme} />
                                    ))}
                                </div>
                            ) : null}
                        </div>

                        <aside className={`border-t px-6 py-7 sm:px-8 lg:border-l lg:border-t-0 ${theme.side}`}>
                            <p className="text-xs font-bold uppercase text-slate-500">
                                {module.panelEyebrow || 'Operational notes'}
                            </p>
                            <p className="mt-2 text-xl font-extrabold text-slate-950">
                                {module.panelTitle || 'What this page covers'}
                            </p>
                            {module.panelCopy ? (
                                <p className={`mt-3 text-sm leading-6 ${theme.sideCopy}`}>
                                    {module.panelCopy}
                                </p>
                            ) : null}

                            {moduleBullets.length > 0 ? (
                                <div className="mt-5 space-y-3">
                                    {moduleBullets.map((item) => (
                                        <div key={item.label} className="flex gap-3 border-t border-slate-200 pt-3 first:border-t-0 first:pt-0">
                                            <span className={`mt-1.5 h-2 w-2 rounded-sm ${theme.accent}`} />
                                            <div>
                                                <p className="text-sm font-semibold text-slate-950">{item.label}</p>
                                                <p className="mt-0.5 text-xs leading-5 text-slate-600">{item.detail}</p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : null}
                        </aside>
                    </div>
                </section>

                {metrics.length > 0 ? (
                    <section className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        {metrics.map((metric, index) => (
                            <KpiCard
                                key={metric.label}
                                label={metric.label}
                                value={metric.value}
                                description={metric.description}
                                tone={metric.tone || METRIC_TONES[index % METRIC_TONES.length]}
                            />
                        ))}
                    </section>
                ) : null}

                {filters ? (
                    <section className={`rounded-lg border ${theme.filterFrame} bg-white p-5 shadow-sm`}>
                        <form onSubmit={submitFilters} className="grid gap-5 xl:grid-cols-[minmax(0,1fr)_auto] xl:items-end">
                            <div className="space-y-3">
                                <label htmlFor="workspace-search" className="block text-xs font-bold uppercase text-slate-500">
                                        Search
                                </label>
                                <div className="flex flex-col gap-3 sm:flex-row">
                                    <div className="relative flex-1">
                                        <span className="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                                            <SearchIcon />
                                        </span>
                                        <input
                                            id="workspace-search"
                                            type="search"
                                            value={data.search}
                                            onChange={(event) => setData('search', event.target.value)}
                                            placeholder={module.searchPlaceholder || `Search ${workspace.title.toLowerCase()}`}
                                            className="h-11 w-full rounded-lg border-slate-200 bg-slate-50 pl-10 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                                        />
                                    </div>

                                    <div className="flex gap-2">
                                        <button
                                            type="submit"
                                            className="inline-flex h-11 items-center justify-center rounded-lg bg-slate-950 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2"
                                        >
                                            Apply
                                        </button>
                                        <button
                                            type="button"
                                            onClick={resetFilters}
                                            className="inline-flex h-11 items-center justify-center rounded-lg border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2"
                                        >
                                            Reset
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {statusOptions.length > 0 || platformOptions.length > 0 ? (
                                <div className="space-y-3 xl:justify-self-end">
                                    {statusOptions.length > 0 ? (
                                        <div className="flex flex-wrap items-center gap-2">
                                            <span className="text-[11px] font-bold uppercase text-slate-500">Status</span>
                                            <button
                                                type="button"
                                                onClick={() => selectStatus('')}
                                                className={`rounded-full border px-3.5 py-1.5 text-xs font-semibold transition ${
                                                    statusFilterChipClasses('', !data.status)
                                                }`}
                                            >
                                                All
                                            </button>
                                            {statusOptions.map((status) => {
                                                const active = data.status === status;

                                                return (
                                                    <button
                                                        key={status}
                                                        type="button"
                                                        onClick={() => selectStatus(status)}
                                                        className={`rounded-full border px-3.5 py-1.5 text-xs font-semibold capitalize transition ${
                                                            statusFilterChipClasses(status, active)
                                                        }`}
                                                    >
                                                        {formatStatusLabel(status)}
                                                    </button>
                                                );
                                            })}
                                        </div>
                                    ) : null}

                                    {platformOptions.length > 0 ? (
                                        <div className="flex flex-wrap items-center gap-2">
                                            <span className="text-[11px] font-bold uppercase text-slate-500">Platform</span>
                                            <button
                                                type="button"
                                                onClick={() => selectPlatform('')}
                                                className={`rounded-full border px-3.5 py-1.5 text-xs font-semibold transition ${
                                                    statusFilterChipClasses('', !data.platform)
                                                }`}
                                            >
                                                All
                                            </button>
                                            {platformOptions.map((platform) => {
                                                const active = data.platform === platform;

                                                return (
                                                    <button
                                                        key={platform}
                                                        type="button"
                                                        onClick={() => selectPlatform(platform)}
                                                        className={`rounded-full border px-3.5 py-1.5 text-xs font-semibold capitalize transition ${
                                                            statusFilterChipClasses(platform, active)
                                                        }`}
                                                    >
                                                        {formatStatusLabel(platform)}
                                                    </button>
                                                );
                                            })}
                                        </div>
                                    ) : null}
                                </div>
                            ) : null}
                        </form>
                    </section>
                ) : null}

                <section className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div className="flex flex-col gap-3 border-b border-slate-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 className="text-base font-extrabold text-slate-950">{module.tableTitle || 'Records'}</h3>
                            <p className="mt-1 text-sm text-slate-500">{rows.length} visible rows</p>
                        </div>

                        <div className="flex flex-wrap gap-2">
                            {filters?.search ? (
                                <span className="inline-flex rounded-md border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                                    Search: {filters.search}
                                </span>
                            ) : null}
                            {filters?.status ? (
                                <span className="inline-flex rounded-md border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                                    Status: {formatStatusLabel(filters.status)}
                                </span>
                            ) : null}
                            {filters?.platform ? (
                                <span className="inline-flex rounded-md border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                                    Platform: {formatStatusLabel(filters.platform)}
                                </span>
                            ) : null}
                        </div>
                    </div>

                    {rows.length === 0 ? (
                        <div className="px-6 py-10">
                            <EmptyState
                                title={module.emptyTitle || 'No records found'}
                                description={workspace.emptyState || 'Try another search term or reset the current filters.'}
                            />
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-slate-200 text-sm">
                                <thead className="bg-slate-50">
                                    <tr>
                                        {workspace.columns.map((column) => (
                                            <th key={column} className="whitespace-nowrap px-6 py-3.5 text-left text-xs font-bold uppercase text-slate-500">
                                                {column}
                                            </th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 bg-white">
                                    {rows.map((row, index) => (
                                        <tr key={index} className="transition hover:bg-sky-50/30">
                                            {workspace.columns.map((column) => {
                                                const value = row[column] ?? '';
                                                const isStatus = column.toLowerCase().includes('status') || column.toLowerCase() === 'stage';
                                                const isAction = column.toLowerCase() === 'action';
                                                const wraps = ['content', 'description', 'error', 'answer'].includes(column.toLowerCase());

                                                return (
                                                    <td
                                                        key={column}
                                                        className={`${wraps ? 'max-w-xl whitespace-normal' : 'whitespace-nowrap'} ${
                                                            isAction ? 'text-left' : ''
                                                        } px-6 py-4 text-slate-700`}
                                                    >
                                                        {isStatus && typeof value !== 'object'
                                                            ? renderStatusPill(value, formatStatusLabel(value))
                                                            : renderWorkspaceCell(column, value)}
                                                    </td>
                                                );
                                            })}
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        )}
                </section>
            </div>
        </AuthenticatedLayout>
    );
}
