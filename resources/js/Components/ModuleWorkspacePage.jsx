import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import EmptyState from '@/Components/EmptyState';
import KpiCard from '@/Components/KpiCard';
import PageHeader from '@/Components/PageHeader';
import { Head, Link, router, useForm } from '@inertiajs/react';

const METRIC_TONES = ['blue', 'emerald', 'amber', 'rose'];

const MODULE_THEMES = {
    slate: {
        hero: 'bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800',
        badge: 'border-white/10 bg-white/10 text-white/90',
        title: 'text-white',
        copy: 'text-slate-200',
        card: 'border-white/10 bg-white/10',
        cardLabel: 'text-slate-300',
        cardText: 'text-white',
        side: 'border-white/10 bg-white/10',
        sideCopy: 'text-slate-200',
        chipActive: 'border-sky-500 bg-sky-500 text-white shadow-sm',
        chipInactive: 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50',
        filterFrame: 'border-slate-200',
        actionPrimary: 'bg-white text-slate-950 hover:bg-slate-100',
        actionSecondary: 'border-white/15 bg-white/5 text-white/90 hover:bg-white/10',
    },
    social: {
        hero: 'bg-gradient-to-br from-slate-950 via-sky-950 to-blue-900',
        badge: 'border-white/10 bg-white/10 text-white/90',
        title: 'text-white',
        copy: 'text-sky-100/90',
        card: 'border-white/10 bg-white/10',
        cardLabel: 'text-sky-100/80',
        cardText: 'text-white',
        side: 'border-white/10 bg-white/10',
        sideCopy: 'text-sky-100/90',
        chipActive: 'border-sky-500 bg-sky-500 text-white shadow-sm',
        chipInactive: 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50',
        filterFrame: 'border-sky-100',
        actionPrimary: 'bg-white text-slate-950 hover:bg-slate-100',
        actionSecondary: 'border-white/15 bg-white/5 text-white/90 hover:bg-white/10',
    },
    marketing: {
        hero: 'bg-gradient-to-br from-slate-950 via-fuchsia-950 to-rose-900',
        badge: 'border-white/10 bg-white/10 text-white/90',
        title: 'text-white',
        copy: 'text-rose-100/90',
        card: 'border-white/10 bg-white/10',
        cardLabel: 'text-rose-100/80',
        cardText: 'text-white',
        side: 'border-white/10 bg-white/10',
        sideCopy: 'text-rose-100/90',
        chipActive: 'border-rose-500 bg-rose-500 text-white shadow-sm',
        chipInactive: 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50',
        filterFrame: 'border-rose-100',
        actionPrimary: 'bg-white text-slate-950 hover:bg-slate-100',
        actionSecondary: 'border-white/15 bg-white/5 text-white/90 hover:bg-white/10',
    },
    workflow: {
        hero: 'bg-gradient-to-br from-slate-950 via-emerald-950 to-teal-900',
        badge: 'border-white/10 bg-white/10 text-white/90',
        title: 'text-white',
        copy: 'text-emerald-100/90',
        card: 'border-white/10 bg-white/10',
        cardLabel: 'text-emerald-100/80',
        cardText: 'text-white',
        side: 'border-white/10 bg-white/10',
        sideCopy: 'text-emerald-100/90',
        chipActive: 'border-emerald-500 bg-emerald-500 text-white shadow-sm',
        chipInactive: 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50',
        filterFrame: 'border-emerald-100',
        actionPrimary: 'bg-white text-slate-950 hover:bg-slate-100',
        actionSecondary: 'border-white/15 bg-white/5 text-white/90 hover:bg-white/10',
    },
    support: {
        hero: 'bg-gradient-to-br from-slate-950 via-amber-950 to-orange-900',
        badge: 'border-white/10 bg-white/10 text-white/90',
        title: 'text-white',
        copy: 'text-amber-100/90',
        card: 'border-white/10 bg-white/10',
        cardLabel: 'text-amber-100/80',
        cardText: 'text-white',
        side: 'border-white/10 bg-white/10',
        sideCopy: 'text-amber-100/90',
        chipActive: 'border-amber-500 bg-amber-500 text-white shadow-sm',
        chipInactive: 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50',
        filterFrame: 'border-amber-100',
        actionPrimary: 'bg-white text-slate-950 hover:bg-slate-100',
        actionSecondary: 'border-white/15 bg-white/5 text-white/90 hover:bg-white/10',
    },
    crm: {
        hero: 'bg-gradient-to-br from-slate-950 via-indigo-950 to-blue-900',
        badge: 'border-white/10 bg-white/10 text-white/90',
        title: 'text-white',
        copy: 'text-indigo-100/90',
        card: 'border-white/10 bg-white/10',
        cardLabel: 'text-indigo-100/80',
        cardText: 'text-white',
        side: 'border-white/10 bg-white/10',
        sideCopy: 'text-indigo-100/90',
        chipActive: 'border-indigo-500 bg-indigo-500 text-white shadow-sm',
        chipInactive: 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50',
        filterFrame: 'border-indigo-100',
        actionPrimary: 'bg-white text-slate-950 hover:bg-slate-100',
        actionSecondary: 'border-white/15 bg-white/5 text-white/90 hover:bg-white/10',
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

const statusTone = (value) => {
    const normalized = String(value || '').toLowerCase();

    if (['active', 'approved', 'confirmed', 'completed', 'sent', 'success', 'published', 'resolved', 'closed'].includes(normalized)) {
        return 'border-emerald-200 bg-emerald-50 text-emerald-700';
    }

    if (['failed', 'cancelled', 'rejected', 'suspended'].includes(normalized)) {
        return 'border-rose-200 bg-rose-50 text-rose-700';
    }

    if (['pending', 'processing', 'scheduled', 'running', 'draft', 'waiting_supplier', 'open'].includes(normalized)) {
        return 'border-amber-200 bg-amber-50 text-amber-700';
    }

    if (['inactive', 'skipped'].includes(normalized)) {
        return 'border-slate-200 bg-slate-50 text-slate-600';
    }

    return 'border-gray-200 bg-gray-50 text-gray-700';
};

const renderStatusPill = (status, label) => (
    <span className={`inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold ${statusTone(status)}`}>
        {label || status || '-'}
    </span>
);

const renderWorkspaceCell = (_column, value) => {
    if (value === null || value === undefined || value === '') {
        return <span className="text-gray-400">-</span>;
    }

    if (typeof value === 'object' && !Array.isArray(value)) {
        if (value.kind === 'payment-summary') {
            return (
                <div className="flex flex-col gap-1">
                    {renderStatusPill(value.status, formatStatusLabel(value.status))}
                    <span className="text-xs text-gray-500">{formatGatewayLabel(value.method)}</span>
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
                        className="inline-flex items-center justify-center rounded-full bg-blue-700 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        {value.label}
                    </Link>
                    {value.gateway ? <span className="text-xs text-gray-500">via {value.gateway}</span> : null}
                </div>
            );
        }

        if (value.kind === 'post-action') {
            const buttonClassName = value.variant === 'secondary'
                ? 'inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2'
                : 'inline-flex items-center justify-center rounded-full bg-blue-700 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2';

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
                    {value.note ? <span className="text-xs text-gray-500">{value.note}</span> : null}
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
                        <span className="inline-flex rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-rose-600">
                            Low stock
                        </span>
                    ) : null}
                </div>
            );
        }

        if (value.kind === 'link') {
            const isDelete = value.method === 'delete' || value.variant === 'danger';
            const linkClassName = value.className || (isDelete
                ? 'inline-flex items-center justify-center rounded-full border border-rose-200 bg-rose-50 px-3.5 py-1.5 text-xs font-semibold text-rose-700 shadow-sm transition hover:border-rose-300 hover:bg-rose-100 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2'
                : 'inline-flex items-center justify-center rounded-full border border-gray-300 bg-white px-3.5 py-1.5 text-xs font-semibold text-gray-700 shadow-sm transition hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2');

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
            className={`inline-flex items-center justify-center rounded-full border px-4 py-2 text-sm font-semibold shadow-sm transition ${classes}`}
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
    const currentPath = typeof window !== 'undefined' ? window.location.pathname : '';
    const { data, setData } = useForm({
        search: filters?.search || '',
        status: filters?.status || '',
    });

    const buildParams = (overrides = {}) => {
        const next = {
            search: data.search,
            status: data.status,
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
        setData({ search: '', status: '' });

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

    const moduleHighlights = module.highlights || [];
    const moduleBullets = module.panelBullets || module.highlights || [];

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <PageHeader
                    eyebrow={module.eyebrow || 'Workspace'}
                    title={workspace.title}
                    description={workspace.description}
                />
            }
        >
            <Head title={workspace.title} />

            <div className="py-8">
                <div className="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                    <section className="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_22px_80px_rgba(15,23,42,0.08)]">
                        <div className={`grid gap-0 lg:grid-cols-[minmax(0,1.55fr)_360px] ${theme.hero}`}>
                            <div className="relative px-6 py-7 sm:px-8 sm:py-8">
                                <div className="pointer-events-none absolute inset-0 overflow-hidden">
                                    <div className="absolute -right-20 top-0 h-72 w-72 rounded-full bg-white/10 blur-3xl" />
                                    <div className="absolute bottom-0 left-1/3 h-48 w-48 rounded-full bg-sky-400/10 blur-3xl" />
                                </div>

                                <div className="relative z-10">
                                    <div className="flex flex-wrap gap-2">
                                        <span className={`inline-flex rounded-full border px-3 py-1 text-[11px] font-black uppercase tracking-[0.22em] ${theme.badge}`}>
                                            {module.tag || module.eyebrow || 'Module'}
                                        </span>
                                        <span className={`inline-flex rounded-full border px-3 py-1 text-[11px] font-black uppercase tracking-[0.22em] ${theme.badge}`}>
                                            {rows.length} rows
                                        </span>
                                        {statusOptions.length > 0 ? (
                                            <span className={`inline-flex rounded-full border px-3 py-1 text-[11px] font-black uppercase tracking-[0.22em] ${theme.badge}`}>
                                                {statusOptions.length} statuses
                                            </span>
                                        ) : null}
                                    </div>

                                    <h1 className={`mt-4 text-3xl font-black tracking-tight sm:text-4xl ${theme.title}`}>
                                        {module.heroTitle || workspace.title}
                                    </h1>
                                    <p className={`mt-3 max-w-3xl text-sm leading-7 ${theme.copy}`}>
                                        {module.heroCopy || workspace.description}
                                    </p>

                                    {moduleHighlights.length > 0 ? (
                                        <div className="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                            {moduleHighlights.map((item) => (
                                                <div key={item.label} className={`rounded-2xl border px-4 py-3 ${theme.card}`}>
                                                    <p className={`text-xs font-black uppercase tracking-[0.18em] ${theme.cardLabel}`}>
                                                        {item.label}
                                                    </p>
                                                    <p className={`mt-1 text-sm leading-6 ${theme.cardText}`}>
                                                        {item.detail}
                                                    </p>
                                                </div>
                                            ))}
                                        </div>
                                    ) : null}

                                    {module.actions?.length > 0 ? (
                                        <div className="mt-6 flex flex-wrap gap-2">
                                            {module.actions.map((action) => (
                                                <ActionButton key={action.href} action={action} theme={theme} />
                                            ))}
                                        </div>
                                    ) : null}
                                </div>
                            </div>

                            <aside className={`border-t border-white/10 bg-white/10 px-6 py-7 sm:px-8 lg:border-l lg:border-t-0 ${theme.side}`}>
                                <div className="h-full rounded-2xl border border-white/10 bg-white/10 p-5 backdrop-blur">
                                    <p className="text-[11px] font-black uppercase tracking-[0.22em] text-white/60">
                                        {module.panelEyebrow || 'Operational notes'}
                                    </p>
                                    <p className="mt-2 text-xl font-black tracking-tight text-white">
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
                                                <div key={item.label} className="flex gap-3 rounded-xl border border-white/10 bg-white/5 p-3">
                                                    <span className="mt-1 h-2.5 w-2.5 rounded-full bg-white/70" />
                                                    <div>
                                                        <p className="text-sm font-semibold text-white">{item.label}</p>
                                                        <p className="mt-0.5 text-xs leading-5 text-slate-200">{item.detail}</p>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    ) : null}
                                </div>
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
                        <section className={`rounded-2xl border ${theme.filterFrame} bg-white p-5 shadow-sm`}>
                            <form onSubmit={submitFilters} className="grid gap-5 xl:grid-cols-[minmax(0,1fr)_auto] xl:items-end">
                                <div className="space-y-3">
                                    <label htmlFor="workspace-search" className="block text-xs font-bold uppercase tracking-wider text-slate-500">
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
                                                className="h-11 w-full rounded-xl border-slate-200 bg-slate-50 pl-10 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                                            />
                                        </div>

                                        <div className="flex gap-2">
                                            <button
                                                type="submit"
                                                className="inline-flex h-11 items-center justify-center rounded-xl bg-slate-950 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2"
                                            >
                                                Apply
                                            </button>
                                            <button
                                                type="button"
                                                onClick={resetFilters}
                                                className="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2"
                                            >
                                                Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {statusOptions.length > 0 ? (
                                    <div className="flex flex-wrap gap-2 xl:justify-end">
                                        <button
                                            type="button"
                                            onClick={() => selectStatus('')}
                                            className={`rounded-full border px-3 py-1 text-xs font-semibold transition ${
                                                !data.status ? theme.chipActive : theme.chipInactive
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
                                                    className={`rounded-full border px-3 py-1 text-xs font-semibold capitalize transition ${
                                                        active ? theme.chipActive : theme.chipInactive
                                                    }`}
                                                >
                                                    {formatStatusLabel(status)}
                                                </button>
                                            );
                                        })}
                                    </div>
                                ) : null}
                            </form>
                        </section>
                    ) : null}

                    <section className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div className="flex flex-col gap-3 border-b border-slate-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 className="text-base font-black text-slate-950">{module.tableTitle || 'Records'}</h3>
                                <p className="mt-1 text-sm text-slate-500">{rows.length} visible rows</p>
                            </div>

                            <div className="flex flex-wrap gap-2">
                                {filters?.search ? (
                                    <span className="inline-flex rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                                        Search: {filters.search}
                                    </span>
                                ) : null}
                                {filters?.status ? (
                                    <span className="inline-flex rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                                        Status: {formatStatusLabel(filters.status)}
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
                                    <thead className="bg-slate-50/80">
                                        <tr>
                                            {workspace.columns.map((column) => (
                                                <th key={column} className="whitespace-nowrap px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
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
            </div>
        </AuthenticatedLayout>
    );
}
