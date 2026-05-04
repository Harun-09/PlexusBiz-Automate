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

export default function Dashboard({ auth, dashboard }) {
    const [query, setQuery] = useState('');
    const normalizedQuery = normalize(query);

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
        <form
            onSubmit={(event) => event.preventDefault()}
            className="w-full min-w-[280px] sm:w-[360px]"
        >
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
                    className="h-11 w-full rounded-xl border-slate-200 bg-slate-50 pl-10 text-sm focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                />
            </div>
        </form>
    );

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<PageHeader title={`${dashboard.role.label} Dashboard`} actions={searchField} />}
        >
            <Head title={`${dashboard.role.label} Dashboard`} />

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

            <div className="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
                <section className="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 className="text-base font-black text-slate-950">Permissions</h2>
                            <p className="mt-1 text-sm leading-6 text-slate-600">
                                Resolved from assigned roles and direct permissions.
                            </p>
                        </div>
                        <span className="inline-flex rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-bold text-slate-600">
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

                <section className="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 className="text-base font-black text-slate-950">Quick Actions</h2>
                            <p className="mt-1 text-sm leading-6 text-slate-600">
                                Live shortcuts for the current role.
                            </p>
                        </div>
                        <span className="inline-flex rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">
                            {filteredQuickLinks.length} shortcuts
                        </span>
                    </div>

                    {filteredQuickLinks.length > 0 ? (
                        <div className="mt-5 grid gap-3">
                            {filteredQuickLinks.map((link) => (
                                <Link
                                    key={link.href}
                                    href={link.href}
                                    className="group flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-800"
                                >
                                    <span>{link.label}</span>
                                    <span
                                        aria-hidden="true"
                                        className="text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-blue-700"
                                    >
                                        -&gt;
                                    </span>
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
        </AuthenticatedLayout>
    );
}
