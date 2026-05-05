import KpiCard from '@/Components/KpiCard';
import PageHeader from '@/Components/PageHeader';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

const shortcuts = [
    {
        label: 'User management',
        description: 'Review roles, status, and access for platform users.',
        href: '/admin/users',
    },
    {
        label: 'Supplier onboarding',
        description: 'Approve vendors and keep onboarding state visible.',
        href: '/admin/suppliers',
    },
    {
        label: 'Product operations',
        description: 'Inspect catalog status, stock, and supplier ownership.',
        href: '/admin/products',
    },
    {
        label: 'Bulk pricing & MOQ',
        description: 'Manage tiered pricing rules in one canonical screen.',
        href: '/admin/bulk-pricing',
    },
    {
        label: 'Audit trail',
        description: 'Review critical admin and automation changes.',
        href: '/admin/audit-logs',
    },
    {
        label: 'Marketplace',
        description: 'Jump to the live catalog view used by buyers.',
        href: '/marketplace',
    },
];

export default function Index({ auth, workspace }) {
    const metrics = workspace.metrics || [];
    const primaryShortcuts = shortcuts.slice(0, 4);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <PageHeader
                    eyebrow="Admin Panel"
                    title={workspace.title}
                    description={workspace.description}
                    actions={(
                        <>
                            <Link
                                href="/admin/users"
                                className="inline-flex items-center justify-center rounded-lg bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                            >
                                Users
                            </Link>
                            <Link
                                href="/admin/suppliers"
                                className="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                            >
                                Suppliers
                            </Link>
                        </>
                    )}
                />
            }
        >
            <Head title={workspace.title} />

            <div className="space-y-6">
                <section className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div className="h-1.5 bg-blue-700" />
                    <div className="grid gap-0 lg:grid-cols-[minmax(0,1.4fr)_380px]">
                        <div className="px-6 py-8 sm:px-8">
                            <div className="flex flex-wrap gap-2">
                                <span className="inline-flex rounded-md border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-bold uppercase text-blue-700">
                                    Platform control
                                </span>
                                <span className="inline-flex rounded-md border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-bold uppercase text-slate-600">
                                    {metrics.length} live metrics
                                </span>
                            </div>

                            <h1 className="mt-4 text-2xl font-extrabold text-slate-950 sm:text-3xl">
                                Admin command center
                            </h1>
                            <p className="mt-3 max-w-3xl text-sm leading-6 text-slate-600">
                                Manage users, suppliers, products, audit logs, and feature switches from one panel without reusing the generic workspace table shell.
                            </p>

                            <div className="mt-6 flex flex-wrap gap-2">
                                {primaryShortcuts.map((item) => (
                                    <Link
                                        key={item.href}
                                        href={item.href}
                                        className="inline-flex items-center justify-center rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-100"
                                    >
                                        {item.label}
                                    </Link>
                                ))}
                            </div>
                        </div>

                        <aside className="border-t border-slate-200 bg-slate-50 px-6 py-7 sm:px-8 lg:border-l lg:border-t-0">
                            <p className="text-xs font-bold uppercase text-slate-500">
                                Operational shortcuts
                            </p>
                            <div className="mt-4 space-y-3">
                                {shortcuts.slice(0, 4).map((item) => (
                                    <Link
                                        key={item.href}
                                        href={item.href}
                                        className="block rounded-lg border border-slate-200 bg-white p-4 shadow-sm transition hover:border-blue-200 hover:shadow-md"
                                    >
                                        <div className="flex items-start justify-between gap-3">
                                            <div>
                                                <p className="text-sm font-bold text-slate-950">{item.label}</p>
                                                <p className="mt-1 text-xs leading-5 text-slate-500">{item.description}</p>
                                            </div>
                                            <span className="rounded-md bg-slate-100 px-2 py-1 text-[10px] font-bold uppercase text-slate-500">
                                                Open
                                            </span>
                                        </div>
                                    </Link>
                                ))}
                            </div>
                        </aside>
                    </div>
                </section>

                    {metrics.length > 0 ? (
                        <section className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            {metrics.map((metric) => (
                                <KpiCard
                                    key={metric.label}
                                    label={metric.label}
                                    value={metric.value}
                                    description={metric.description}
                                    tone={metric.tone || 'slate'}
                                />
                            ))}
                        </section>
                    ) : null}

                <section className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    {shortcuts.map((item) => (
                        <Link
                            key={item.href}
                            href={item.href}
                            className="group rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-blue-200 hover:shadow-md"
                        >
                            <div className="flex items-start justify-between gap-4">
                                <div>
                                    <p className="text-xs font-bold uppercase text-slate-500">Admin module</p>
                                    <h2 className="mt-2 text-base font-extrabold text-slate-950">{item.label}</h2>
                                    <p className="mt-2 text-sm leading-6 text-slate-600">{item.description}</p>
                                </div>
                                <span className="rounded-md border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600 transition group-hover:border-blue-200 group-hover:text-blue-700">
                                    Open
                                </span>
                            </div>
                        </Link>
                    ))}
                </section>
            </div>
        </AuthenticatedLayout>
    );
}
