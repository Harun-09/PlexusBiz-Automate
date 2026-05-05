import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import EmptyState from '@/Components/EmptyState';
import KpiCard from '@/Components/KpiCard';
import PageHeader from '@/Components/PageHeader';
import { Head, Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';

const normalize = (value) => String(value || '').toLowerCase().trim();

const SearchIcon = () => (
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" className="h-4 w-4">
        <circle cx="10.5" cy="10.5" r="5.75" stroke="currentColor" strokeWidth="1.8" />
        <path d="m15.25 15.25 4 4" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" />
    </svg>
);

const roleThemes = {
    admin: {
        accent: 'bg-blue-700',
        border: 'border-blue-100',
        badge: 'border-blue-200 bg-blue-50 text-blue-700',
        soft: 'bg-blue-50 text-blue-700',
        summary: 'Admin command center',
        overview: 'Control users, suppliers, products, modules, and governance from a clean executive dashboard.',
        focus: [
            'Watch platform-wide order, payment, and customer movement.',
            'Keep admin, audit, and module controls one click away.',
            'Use live counts before acting on approvals or configuration.',
        ],
    },
    supplier: {
        accent: 'bg-emerald-600',
        border: 'border-emerald-100',
        badge: 'border-emerald-200 bg-emerald-50 text-emerald-700',
        soft: 'bg-emerald-50 text-emerald-700',
        summary: 'Supplier operations hub',
        overview: 'Track products, inventory, fulfillment, and buyer support in one supplier-facing workspace.',
        focus: [
            'Keep catalog health, stock alerts, and fulfillment together.',
            'Approved suppliers get product creation shortcuts quickly.',
            'Support visibility stays connected to supplier-side work.',
        ],
    },
    marketing_manager: {
        accent: 'bg-rose-600',
        border: 'border-rose-100',
        badge: 'border-rose-200 bg-rose-50 text-rose-700',
        soft: 'bg-rose-50 text-rose-700',
        summary: 'Marketing command board',
        overview: 'Keep email campaigns, social campaigns, templates, social publishing, and workflow logs in a single performance view.',
        focus: [
            'Campaign and template actions stay close to performance signals.',
            'Scheduled and published social posts remain easy to compare.',
            'Workflow failures stay visible before they affect campaigns.',
        ],
    },
    workflow_manager: {
        accent: 'bg-teal-600',
        border: 'border-teal-100',
        badge: 'border-teal-200 bg-teal-50 text-teal-700',
        soft: 'bg-teal-50 text-teal-700',
        summary: 'Workflow operations board',
        overview: 'Monitor automation rules, execution logs, and failed runs with an operations-first layout.',
        focus: [
            'Active rules and run states stay visible without deep navigation.',
            'Failed runs are treated as an operational queue.',
            'Automation shortcuts keep troubleshooting focused.',
        ],
    },
    buyer: {
        accent: 'bg-amber-500',
        border: 'border-amber-100',
        badge: 'border-amber-200 bg-amber-50 text-amber-800',
        soft: 'bg-amber-50 text-amber-800',
        summary: 'Buyer workspace',
        overview: 'Review orders, spending, and support activity from a business-ready buyer dashboard.',
        focus: [
            'Order, invoice, and support paths stay grouped for repeat work.',
            'Pending activity is visible before returning to the marketplace.',
            'Checkout shortcuts keep the buying workflow fast.',
        ],
    },
};

const quickLinkCopy = {
    Users: 'Manage access, roles, and platform accounts.',
    Customers: 'Review CRM profiles and buying activity.',
    Suppliers: 'Approve vendors and track onboarding state.',
    Products: 'Open catalog, pricing, and stock operations.',
    Inventory: 'Check stock, availability, and catalog health.',
    Orders: 'Track buyer orders and fulfillment progress.',
    'Supplier Orders': 'Handle supplier-side fulfillment work.',
    'Add Product': 'Create a new supplier catalog item.',
    'Email Campaigns': 'Manage email campaigns and schedules.',
    'Social Campaigns': 'Manage social campaign groups and scheduled post timelines.',
    'Social Calendar': 'Plan and review future social posts.',
    'Workflow Logs': 'Audit automation runs and failures.',
    Marketplace: 'Browse the live product catalog.',
    Cart: 'Continue the buyer checkout flow.',
    Support: 'Open buyer and supplier support requests.',
    'Failed Logs': 'Review failed workflow executions.',
    Dashboard: 'Return to the role overview.',
    'Module Settings': 'Enable or disable platform modules.',
    'Audit Logs': 'Review critical administrative actions.',
};

const statusTone = (value) => {
    const normalized = normalize(value);

    if (normalized === 'active') {
        return 'border-emerald-200 bg-emerald-50 text-emerald-700';
    }

    if (normalized === 'inactive') {
        return 'border-slate-200 bg-slate-50 text-slate-600';
    }

    return 'border-blue-200 bg-blue-50 text-blue-700';
};

const formatStatus = (value) => String(value || 'active').replace(/[_-]/g, ' ');

export default function Dashboard({ auth, dashboard }) {
    const [query, setQuery] = useState('');
    const normalizedQuery = normalize(query);
    const theme = roleThemes[dashboard.role.key] || roleThemes.buyer;

    const filteredPermissions = useMemo(() => {
        if (normalizedQuery === '') {
            return dashboard.permissions;
        }

        return dashboard.permissions.filter((permission) => normalize(permission).includes(normalizedQuery));
    }, [dashboard.permissions, normalizedQuery]);

    const filteredQuickLinks = useMemo(() => {
        if (normalizedQuery === '') {
            return dashboard.quickLinks;
        }

        return dashboard.quickLinks.filter((link) => {
            const href = normalize(link.href);
            const label = normalize(link.label);

            return label.includes(normalizedQuery) || href.includes(normalizedQuery);
        });
    }, [dashboard.quickLinks, normalizedQuery]);

    const searchField = (
        <form onSubmit={(event) => event.preventDefault()} className="w-full min-w-[260px] sm:w-[360px]">
            <label htmlFor="dashboard-search" className="sr-only">
                Search dashboard
            </label>
            <div className="relative">
                <span className="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <SearchIcon />
                </span>
                <input
                    id="dashboard-search"
                    type="search"
                    value={query}
                    onChange={(event) => setQuery(event.target.value)}
                    placeholder="Search shortcuts or permissions"
                    className="h-11 w-full rounded-lg border border-slate-200 bg-white pl-10 text-sm text-slate-700 shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                />
            </div>
        </form>
    );

    const featuredCards = dashboard.cards.slice(0, 3);
    const topQuickLinks = filteredQuickLinks.slice(0, 4);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <PageHeader
                    eyebrow="Role workspace"
                    title={`${dashboard.role.label} Dashboard`}
                    description={theme.overview}
                    actions={searchField}
                />
            }
        >
            <Head title={`${dashboard.role.label} Dashboard`} />

            <div className="space-y-6">
                <section className="grid gap-6 xl:grid-cols-[minmax(0,1.35fr)_360px]">
                    <div className={`overflow-hidden rounded-2xl border bg-white shadow-sm ${theme.border}`}>
                        <div className={`h-1.5 ${theme.accent}`} />
                        <div className="p-6 sm:p-8">
                            <div className="flex flex-wrap items-center gap-2">
                                <span className={`rounded-md border px-3 py-1 text-xs font-bold uppercase ${theme.badge}`}>
                                    {dashboard.role.label}
                                </span>
                                <span className="rounded-md border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-bold uppercase text-slate-600">
                                    {dashboard.cards.length} live metrics
                                </span>
                                <span className="rounded-md border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-bold uppercase text-slate-600">
                                    {dashboard.quickLinks.length} shortcuts
                                </span>
                                <span className={`rounded-md border px-3 py-1 text-xs font-bold uppercase ${statusTone(dashboard.status)}`}>
                                    {formatStatus(dashboard.status)}
                                </span>
                            </div>

                            <div className="mt-5 max-w-3xl">
                                <h2 className="text-2xl font-extrabold tracking-[-0.04em] text-slate-950 sm:text-3xl">
                                    {theme.summary}
                                </h2>
                                <p className="mt-3 text-sm leading-6 text-slate-600">
                                    {theme.overview}
                                </p>
                            </div>

                            {featuredCards.length > 0 ? (
                                <dl className="mt-6 grid gap-4 sm:grid-cols-3">
                                    {featuredCards.map((card) => (
                                        <div key={card.label} className="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 shadow-sm">
                                            <dt className="text-xs font-bold uppercase tracking-wider text-slate-500">{card.label}</dt>
                                            <dd className="mt-3 text-2xl font-extrabold tracking-[-0.04em] text-slate-950">{card.value}</dd>
                                            <p className="mt-2 text-xs leading-5 text-slate-500">{card.description}</p>
                                        </div>
                                    ))}
                                </dl>
                            ) : null}

                            {dashboard.role.key === 'buyer' ? (
                                <div className="mt-6 rounded-2xl border border-amber-200 bg-amber-50/80 p-4 text-sm leading-6 text-amber-900">
                                    <p className="font-extrabold text-amber-950">Why Pending Payments appears</p>
                                    <p className="mt-2">
                                        Your order is already created at checkout. This card stays visible when the payment
                                        gateway has not confirmed the transaction yet, or when a failed/cancelled payment needs
                                        to be retried.
                                    </p>
                                    <div className="mt-3">
                                        <Link
                                            href="/orders"
                                            className="inline-flex items-center rounded-md border border-amber-300 bg-white px-3 py-1.5 text-xs font-bold text-amber-800 transition hover:border-amber-400 hover:text-amber-900"
                                        >
                                            View orders and continue payment
                                        </Link>
                                    </div>
                                </div>
                            ) : null}
                        </div>
                    </div>

                    <aside className="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 shadow-sm">
                        <div className={`h-1.5 ${theme.accent}`} />
                        <div className="p-6 sm:p-8">
                            <p className="text-xs font-bold uppercase tracking-wider text-slate-500">Operational focus</p>
                            <h3 className="mt-2 text-lg font-extrabold tracking-[-0.04em] text-slate-950">
                                {dashboard.role.label} priorities
                            </h3>

                            <div className="mt-4 space-y-3">
                                {theme.focus.map((item) => (
                                    <div key={item} className="flex gap-3">
                                        <span className={`mt-2 h-2 w-2 rounded-sm ${theme.accent}`} />
                                        <p className="text-sm leading-6 text-slate-600">{item}</p>
                                    </div>
                                ))}
                            </div>

                            {topQuickLinks.length > 0 ? (
                                <div className="mt-6 border-t border-slate-200 pt-5">
                                    <p className="text-xs font-bold uppercase tracking-wider text-slate-500">Fast path</p>
                                    <div className="mt-3 grid gap-2">
                                        {topQuickLinks.map((link) => (
                                            <Link
                                                key={link.href}
                                                href={link.href}
                                                className="group flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-blue-200 hover:shadow-md hover:-translate-y-0.5"
                                            >
                                                <span>{link.label}</span>
                                                <span className={`rounded-md px-2.5 py-1 text-xs font-bold ${theme.soft}`}>
                                                    Open
                                                </span>
                                            </Link>
                                        ))}
                                    </div>
                                </div>
                            ) : null}
                        </div>
                    </aside>
                </section>

                {dashboard.cards.length > 0 ? (
                    <section className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        {dashboard.cards.map((card) => (
                            <KpiCard
                                key={card.label}
                                label={card.label}
                                value={card.value}
                                description={card.description}
                                tone={card.tone}
                            />
                        ))}
                    </section>
                ) : null}

                <div className="grid gap-6 xl:grid-cols-[minmax(0,1fr)_390px]">
                    <section className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p className="text-xs font-bold uppercase text-slate-500">Access</p>
                                <h2 className="mt-1 text-xl font-extrabold text-slate-950">Permissions</h2>
                                <p className="mt-2 text-sm leading-6 text-slate-600">
                                    Resolved from assigned roles and direct permissions for the current workspace.
                                </p>
                            </div>
                            <span className="inline-flex rounded-md border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-bold text-slate-600">
                                {filteredPermissions.length} / {dashboard.permissions.length}
                            </span>
                        </div>

                        {filteredPermissions.length > 0 ? (
                            <div className="mt-5 flex flex-wrap gap-2">
                                {filteredPermissions.map((permission) => (
                                    <span
                                        key={permission}
                                        className="rounded-md border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm font-semibold text-slate-700"
                                    >
                                        {permission.replaceAll('_', ' ')}
                                    </span>
                                ))}
                            </div>
                        ) : (
                            <div className="mt-5">
                                <EmptyState
                                    title="No permissions match"
                                    description="Try a different search term or clear the search box to view the full permission set."
                                />
                            </div>
                        )}
                    </section>

                    <section className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p className="text-xs font-bold uppercase text-slate-500">Navigation</p>
                                <h2 className="mt-1 text-xl font-extrabold text-slate-950">Quick Actions</h2>
                                <p className="mt-2 text-sm leading-6 text-slate-600">
                                    Live shortcuts for the current role.
                                </p>
                            </div>
                            <span className={`inline-flex rounded-md border px-3 py-1 text-xs font-bold ${theme.badge}`}>
                                {filteredQuickLinks.length} shortcuts
                            </span>
                        </div>

                        {filteredQuickLinks.length > 0 ? (
                            <div className="mt-5 grid gap-3">
                                {filteredQuickLinks.map((link) => (
                                    <Link
                                        key={link.href}
                                        href={link.href}
                                        className="group rounded-lg border border-slate-200 bg-white p-4 shadow-sm transition hover:border-blue-200 hover:shadow-md"
                                    >
                                        <div className="flex items-start justify-between gap-4">
                                            <div>
                                                <p className="text-sm font-extrabold text-slate-950">{link.label}</p>
                                                <p className="mt-1 text-xs leading-5 text-slate-500">
                                                    {quickLinkCopy[link.label] || 'Open this workspace area in one click.'}
                                                </p>
                                                <p className="mt-2 break-all text-xs font-semibold text-slate-400">
                                                    {link.href}
                                                </p>
                                            </div>
                                            <span className="rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-bold text-slate-500 transition group-hover:border-blue-200 group-hover:text-blue-700">
                                                Open
                                            </span>
                                        </div>
                                    </Link>
                                ))}
                            </div>
                        ) : (
                            <div className="mt-5">
                                <EmptyState
                                    title="No shortcuts match"
                                    description="The role shortcuts are still available, but none match the current search text."
                                />
                            </div>
                        )}
                    </section>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
