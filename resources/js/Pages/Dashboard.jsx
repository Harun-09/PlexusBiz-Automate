import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import KpiCard from '@/Components/KpiCard';
import PageHeader from '@/Components/PageHeader';
import StatusBadge from '@/Components/StatusBadge';
import { Head, Link } from '@inertiajs/react';

export default function Dashboard({ auth, dashboard }) {
    const quickLinkRows = dashboard.quickLinks.map((link) => ({
        Workspace: link.label,
        Status: 'active',
        Action: link.href,
    }));

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <PageHeader
                    eyebrow="Live snapshot"
                    title={`${dashboard.role.label} Dashboard`}
                    description={`Account status: ${dashboard.status}. Metrics are resolved from live records on every visit.`}
                    actions={<StatusBadge status={dashboard.status}>{dashboard.status}</StatusBadge>}
                />
            }
        >
            <Head title="Dashboard" />

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
                    <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 className="text-base font-black text-slate-950">Permissions</h2>
                            <p className="mt-1 text-sm leading-6 text-slate-600">Resolved from assigned roles and direct permissions.</p>
                        </div>
                        <StatusBadge status="active">{dashboard.permissions.length} active</StatusBadge>
                    </div>

                    <div className="mt-5 flex flex-wrap gap-2">
                        {dashboard.permissions.map((permission) => (
                            <span
                                key={permission}
                                className="rounded-md border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm font-semibold text-slate-700"
                            >
                                {permission.replaceAll('_', ' ')}
                            </span>
                        ))}
                    </div>
                </section>

                <section className="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 className="text-base font-black text-slate-950">Role Workspace</h2>
                    <div className="mt-4 divide-y divide-slate-100">
                        {dashboard.quickLinks.map((link) => (
                            <Link
                                key={link.href}
                                href={link.href}
                                className="flex items-center justify-between py-3 text-sm font-bold text-slate-700 transition hover:text-blue-800"
                            >
                                <span>{link.label}</span>
                                <span aria-hidden="true">-&gt;</span>
                            </Link>
                        ))}
                    </div>
                </section>
            </div>

            <section>
                <div className="mb-3">
                    <h2 className="text-base font-black text-slate-950">Quick Access</h2>
                    <p className="mt-1 text-sm text-slate-600">Primary workspaces available to this role.</p>
                </div>
                <DataTable
                    columns={['Workspace', 'Status', 'Action']}
                    rows={quickLinkRows}
                    emptyTitle="No workspace links"
                    emptyDescription="This role does not have workspace shortcuts configured yet."
                />
            </section>
        </AuthenticatedLayout>
    );
}
