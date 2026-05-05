import EmptyState from '@/Components/EmptyState';
import KpiCard from '@/Components/KpiCard';
import PageHeader from '@/Components/PageHeader';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { actionButtonClasses } from '@/Utils/pillStyles';

const toneClasses = {
    success: 'border-emerald-200 bg-emerald-50 text-emerald-700',
    warning: 'border-amber-200 bg-amber-50 text-amber-700',
    danger: 'border-rose-200 bg-rose-50 text-rose-700',
    neutral: 'border-slate-200 bg-slate-50 text-slate-700',
};

const formatLabel = (value) =>
    String(value || '-')
        .replace(/_/g, ' ')
        .split(/\s+/)
        .filter(Boolean)
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
        .join(' ');

const formatDate = (value) => {
    if (!value) {
        return 'n/a';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return 'n/a';
    }

    return new Intl.DateTimeFormat('en-GB', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
};

const formatAddress = (address) => {
    if (!address || typeof address !== 'object') {
        return 'n/a';
    }

    const parts = [address.line_1, address.line_2, address.city, address.state, address.postal_code, address.country]
        .filter(Boolean)
        .map((part) => String(part).trim())
        .filter(Boolean);

    return parts.length > 0 ? parts.join(', ') : 'n/a';
};

const statusTone = (value) => {
    const normalized = String(value || '').toLowerCase();

    if (['active', 'customer', 'repeat customer', 'repeat_customer', 'completed', 'paid', 'issued', 'qualified', 'converted'].includes(normalized)) {
        return toneClasses.success;
    }

    if (['lead', 'prospect', 'pending', 'new', 'draft'].includes(normalized)) {
        return toneClasses.warning;
    }

    if (['inactive', 'blocked', 'lost', 'cancelled', 'void', 'failed', 'at_risk'].includes(normalized)) {
        return toneClasses.danger;
    }

    return toneClasses.neutral;
};

function StatusPill({ value, label = null }) {
    return (
        <span className={`inline-flex rounded-full border px-2.5 py-1 text-xs font-bold capitalize ${statusTone(value)}`}>
            {label || formatLabel(value)}
        </span>
    );
}

function DetailRow({ label, value }) {
    return (
        <div className="flex items-start justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0">
            <dt className="text-sm font-semibold text-slate-500">{label}</dt>
            <dd className="text-right text-sm font-bold text-slate-950">{value || 'n/a'}</dd>
        </div>
    );
}

function ActionCell({ action }) {
    if (!action) {
        return <StatusPill value="n/a" label="n/a" />;
    }

    if (action.kind === 'link') {
        return (
            <Link
                href={action.href}
                className={`inline-flex items-center justify-center rounded-lg border px-3 py-1.5 text-xs font-bold transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('secondary')}`}
            >
                {action.label}
            </Link>
        );
    }

    if (action.kind === 'status') {
        return <StatusPill value={action.status} label={action.label} />;
    }

    return <span className="text-sm text-slate-400">n/a</span>;
}

function SectionCard({ eyebrow, title, description, children, className = '' }) {
    return (
        <section className={`overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm ${className}`}>
            <div className="border-b border-slate-100 px-5 py-4">
                <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">{eyebrow}</p>
                <h3 className="mt-1 text-lg font-black tracking-tight text-slate-950">{title}</h3>
                {description ? <p className="mt-1 text-sm leading-6 text-slate-500">{description}</p> : null}
            </div>
            <div className="px-5 py-5">{children}</div>
        </section>
    );
}

export default function Show({ auth, customer, summary, recentOrders = [], recentLeads = [], recentInteractions = [] }) {
    const displayName = customer.company_name || customer.contact_name || 'Customer profile';

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <PageHeader
                    eyebrow="CRM"
                    title={displayName}
                    description="Customer registration, purchase history, lead context, and interaction timeline in one place."
                    actions={
                        <>
                            <Link
                                href={route('crm.customers.edit', customer.id)}
                                className={`inline-flex items-center justify-center rounded-full border px-4 py-2 text-sm font-bold transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('primary')}`}
                            >
                                Edit profile
                            </Link>
                            <Link
                                href={route('crm.customers.index')}
                                className={`inline-flex items-center justify-center rounded-full border px-4 py-2 text-sm font-bold transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('neutral')}`}
                            >
                                Back to customers
                            </Link>
                            <Link
                                href={route('crm.purchases.index')}
                                className="inline-flex items-center justify-center rounded-full bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800"
                            >
                                Purchase history
                            </Link>
                        </>
                    }
                />
            }
        >
            <Head title={displayName} />

            <div className="py-8">
                <div className="space-y-6">
                    <section className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <KpiCard
                            label="Orders"
                            value={summary.orders_count}
                            description="Confirmed purchase records linked to this account."
                            tone="blue"
                        />
                        <KpiCard
                            label="Total spent"
                            value={summary.total_spent}
                            description="All completed order value tied to the customer profile."
                            tone="emerald"
                        />
                        <KpiCard
                            label="Leads"
                            value={summary.leads_count}
                            description="Open or historical lead records connected to the customer."
                            tone="amber"
                        />
                        <KpiCard
                            label="Interactions"
                            value={summary.interactions_count}
                            description="Messages, support tickets, orders, RFQ events, and internal notes."
                            tone="rose"
                        />
                    </section>

                    <section className="grid gap-6 xl:grid-cols-[1.05fr_.95fr]">
                        <div className="space-y-6">
                            <SectionCard
                                eyebrow="Profile"
                                title="Customer record"
                                description="Identity, lifecycle stage, and CRM notes for the current account."
                            >
                                <div className="grid gap-6 lg:grid-cols-[1.1fr_.9fr]">
                                    <div className="space-y-4">
                                        <div className="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                            <div className="flex flex-wrap items-start justify-between gap-3">
                                                <div>
                                                    <p className="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">
                                                        Contact
                                                    </p>
                                                    <h4 className="mt-1 text-2xl font-black tracking-tight text-slate-950">
                                                        {customer.contact_name}
                                                    </h4>
                                                    <p className="mt-1 text-sm font-medium text-slate-600">
                                                        {customer.company_name || 'No company name set'}
                                                    </p>
                                                </div>
                                                <div className="flex flex-col items-end gap-2">
                                                    <StatusPill value={customer.status} />
                                                    <StatusPill value={customer.lifecycle_stage} />
                                                </div>
                                            </div>
                                        </div>

                                        <dl className="rounded-2xl border border-slate-200 px-4">
                                            <DetailRow label="Email" value={customer.email} />
                                            <DetailRow label="Phone" value={customer.phone} />
                                            <DetailRow label="Business type" value={customer.business_type} />
                                            <DetailRow label="Address" value={formatAddress(customer.address)} />
                                            <DetailRow label="Linked user" value={customer.user ? `${customer.user.name} (${customer.user.email})` : 'n/a'} />
                                            <DetailRow label="Last activity" value={formatDate(customer.last_activity_at)} />
                                            <DetailRow label="Created" value={formatDate(customer.created_at)} />
                                        </dl>
                                    </div>

                                    <div className="space-y-4">
                                        <div className="rounded-2xl border border-slate-200 bg-white p-4">
                                            <p className="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">
                                                Tags
                                            </p>
                                            <div className="mt-3 flex flex-wrap gap-2">
                                                {Array.isArray(customer.tags) && customer.tags.length > 0 ? (
                                                    customer.tags.map((tag) => (
                                                        <span
                                                            key={tag}
                                                            className="inline-flex rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700"
                                                        >
                                                            {tag}
                                                        </span>
                                                    ))
                                                ) : (
                                                    <span className="text-sm text-slate-500">No tags assigned</span>
                                                )}
                                            </div>
                                        </div>

                                        <div className="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                            <p className="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">
                                                Notes
                                            </p>
                                            <p className="mt-3 whitespace-pre-line text-sm leading-6 text-slate-700">
                                                {customer.notes || 'No internal notes saved for this account.'}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </SectionCard>

                            <SectionCard
                                eyebrow="Commerce"
                                title="Recent orders"
                                description="Completed, pending, and confirmed purchase history linked to the customer."
                            >
                                {recentOrders.length === 0 ? (
                                    <EmptyState
                                        title="No orders yet"
                                        description="Once the customer places a purchase, the order history will appear here."
                                    />
                                ) : (
                                    <div className="overflow-x-auto">
                                        <table className="min-w-full divide-y divide-slate-100 text-sm">
                                            <thead className="bg-slate-50/70">
                                                <tr>
                                                    {['Order', 'Status', 'Payment', 'Total', 'Placed', 'Invoice', 'Action'].map((column) => (
                                                        <th
                                                            key={column}
                                                            className="whitespace-nowrap px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                                        >
                                                            {column}
                                                        </th>
                                                    ))}
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-slate-50 bg-white">
                                                {recentOrders.map((order) => (
                                                    <tr key={order.id} className="transition hover:bg-blue-50/30">
                                                        <td className="whitespace-nowrap px-4 py-4">
                                                            <div className="font-mono text-xs font-semibold text-slate-500">{order.order_number}</div>
                                                            <div className="mt-1 text-sm font-bold text-slate-950">{order.currency}</div>
                                                        </td>
                                                        <td className="whitespace-nowrap px-4 py-4">
                                                            <StatusPill value={order.status} />
                                                        </td>
                                                        <td className="whitespace-nowrap px-4 py-4">
                                                            <StatusPill value={order.payment_status} />
                                                        </td>
                                                        <td className="whitespace-nowrap px-4 py-4 font-bold text-slate-900">
                                                            {order.total}
                                                        </td>
                                                        <td className="whitespace-nowrap px-4 py-4 text-slate-600">
                                                            {order.placed_at}
                                                        </td>
                                                        <td className="whitespace-nowrap px-4 py-4 text-slate-600">
                                                            {order.invoice_number || 'n/a'}
                                                        </td>
                                                        <td className="whitespace-nowrap px-4 py-4">
                                                            <ActionCell action={order.action} />
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                )}
                            </SectionCard>
                        </div>

                        <div className="space-y-6">
                            <SectionCard
                                eyebrow="Lead Management"
                                title="Recent leads"
                                description="Prospects and outreach records associated with this customer."
                            >
                                {recentLeads.length === 0 ? (
                                    <EmptyState
                                        title="No leads linked"
                                        description="Once the customer enters the sales pipeline, the lead history will be listed here."
                                    />
                                ) : (
                                    <div className="overflow-x-auto">
                                        <table className="min-w-full divide-y divide-slate-100 text-sm">
                                            <thead className="bg-slate-50/70">
                                                <tr>
                                                    {['Lead', 'Source', 'Status', 'Value', 'Assigned', 'Follow Up'].map((column) => (
                                                        <th
                                                            key={column}
                                                            className="whitespace-nowrap px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                                        >
                                                            {column}
                                                        </th>
                                                    ))}
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-slate-50 bg-white">
                                                {recentLeads.map((lead) => (
                                                    <tr key={lead.id} className="transition hover:bg-blue-50/30">
                                                        <td className="whitespace-nowrap px-4 py-4">
                                                            <div className="font-bold text-slate-950">{lead.contact_name}</div>
                                                            <div className="mt-1 text-xs text-slate-500">{lead.company_name || 'n/a'}</div>
                                                        </td>
                                                        <td className="whitespace-nowrap px-4 py-4 text-slate-700">{lead.source}</td>
                                                        <td className="whitespace-nowrap px-4 py-4">
                                                            <StatusPill value={lead.status} />
                                                        </td>
                                                        <td className="whitespace-nowrap px-4 py-4 font-bold text-slate-900">
                                                            {lead.value}
                                                        </td>
                                                        <td className="whitespace-nowrap px-4 py-4 text-slate-700">{lead.assigned_to}</td>
                                                        <td className="whitespace-nowrap px-4 py-4 text-slate-700">{lead.next_follow_up_at}</td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                )}
                            </SectionCard>

                            <SectionCard
                                eyebrow="Timeline"
                                title="Interaction history"
                                description="Messages, support tickets, orders, RFQ events, and internal notes in chronological order."
                            >
                                {recentInteractions.length === 0 ? (
                                    <EmptyState
                                        title="No interactions yet"
                                        description="Customer activity will appear here once the CRM logs an event."
                                    />
                                ) : (
                                    <div className="space-y-3">
                                        {recentInteractions.map((interaction) => (
                                            <article
                                                key={interaction.id}
                                                className="rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:border-blue-200 hover:bg-blue-50/30"
                                            >
                                                <div className="flex items-start justify-between gap-4">
                                                    <div>
                                                        <p className="text-sm font-black text-slate-950">{formatLabel(interaction.type)}</p>
                                                        <p className="mt-1 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                                                            {formatLabel(interaction.direction)}
                                                        </p>
                                                    </div>
                                                    <p className="text-xs font-semibold text-slate-500">{interaction.occurred_at}</p>
                                                </div>

                                                <p className="mt-3 text-sm leading-6 text-slate-700">
                                                    {interaction.summary || 'No summary saved.'}
                                                </p>

                                                <div className="mt-4 flex flex-wrap gap-2">
                                                    <span className="inline-flex rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-bold text-slate-700">
                                                        {interaction.related}
                                                    </span>
                                                    <span className="inline-flex rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-bold text-slate-700">
                                                        {interaction.actor}
                                                    </span>
                                                </div>
                                            </article>
                                        ))}
                                    </div>
                                )}
                            </SectionCard>
                        </div>
                    </section>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
