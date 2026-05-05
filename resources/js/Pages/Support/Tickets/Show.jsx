import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import FlashBanner from '@/Components/FlashBanner';
import PageHeader from '@/Components/PageHeader';
import { actionButtonClasses } from '@/Utils/pillStyles';
import { Head, Link, useForm } from '@inertiajs/react';

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

const formatLabel = (value) =>
    String(value || '-')
        .replace(/_/g, ' ')
        .split(/\s+/)
        .filter(Boolean)
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
        .join(' ');

const statusTone = (value) => {
    const normalized = String(value || '').toLowerCase();

    if (['resolved', 'closed'].includes(normalized)) {
        return 'border-emerald-200 bg-emerald-50 text-emerald-700';
    }

    if (['open', 'pending', 'waiting_supplier'].includes(normalized)) {
        return 'border-amber-200 bg-amber-50 text-amber-700';
    }

    return 'border-slate-200 bg-slate-50 text-slate-700';
};

function Pill({ value, className = '' }) {
    return (
        <span className={`inline-flex rounded-full border px-2.5 py-1 text-xs font-bold capitalize ${statusTone(value)} ${className}`}>
            {formatLabel(value)}
        </span>
    );
}

function Panel({ title, description, children }) {
    return (
        <section className="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <div className="border-b border-slate-100 px-5 py-4">
                <h3 className="text-lg font-black tracking-tight text-slate-950">{title}</h3>
                {description ? <p className="mt-1 text-sm leading-6 text-slate-500">{description}</p> : null}
            </div>
            <div className="px-5 py-5">{children}</div>
        </section>
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

export default function Show({ auth, flash, errors, ticket, assignees = [], statuses = [], can_manage_status = false, can_assign = false }) {
    const replyForm = useForm({
        message: '',
    });

    const statusForm = useForm({
        status: ticket.status,
    });

    const assignForm = useForm({
        assigned_to: ticket.assignee?.id || '',
    });

    const sendReply = (event) => {
        event.preventDefault();

        replyForm.post(route('support.tickets.reply', ticket.id), {
            preserveScroll: true,
            onSuccess: () => replyForm.reset('message'),
        });
    };

    const updateStatus = (event) => {
        event.preventDefault();

        statusForm.put(route('support.tickets.status', ticket.id), {
            preserveScroll: true,
        });
    };

    const updateAssignment = (event) => {
        event.preventDefault();

        assignForm
            .transform((data) => ({
                ...data,
                assigned_to: data.assigned_to === '' ? null : data.assigned_to,
            }))
            .put(route('support.tickets.assign', ticket.id), {
                preserveScroll: true,
            });
    };

    const validationMessage = Object.values(errors || {}).find(Boolean);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <PageHeader
                    eyebrow="Support"
                    title={ticket.ticket_number}
                    description={ticket.subject}
                    actions={
                        <>
                            <Link
                                href={route('support.tickets.create')}
                                className="inline-flex items-center justify-center rounded-full bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800"
                            >
                                New ticket
                            </Link>
                            <Link
                                href={route('support.tickets.index')}
                                className={`inline-flex items-center justify-center rounded-full border px-4 py-2 text-sm font-bold transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('neutral')}`}
                            >
                                Back to tickets
                            </Link>
                        </>
                    }
                />
            }
        >
            <Head title={ticket.ticket_number} />

            <div className="py-8">
                <div className="space-y-6">
                    <section className="grid gap-4 md:grid-cols-4">
                        <div className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p className="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">Status</p>
                            <div className="mt-2"><Pill value={ticket.status} /></div>
                        </div>
                        <div className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p className="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">Priority</p>
                            <p className="mt-2 text-2xl font-black tracking-tight text-slate-950">{formatLabel(ticket.priority)}</p>
                        </div>
                        <div className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p className="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">Messages</p>
                            <p className="mt-2 text-2xl font-black tracking-tight text-slate-950">{ticket.messages?.length || 0}</p>
                        </div>
                        <div className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p className="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">Last update</p>
                            <p className="mt-2 text-lg font-black tracking-tight text-slate-950">{formatDate(ticket.last_message_at)}</p>
                        </div>
                    </section>

                    <section className="grid gap-6 xl:grid-cols-[1.05fr_.95fr]">
                        <div className="space-y-6">
                            <Panel
                                title={ticket.subject}
                                description={ticket.description}
                            >
                                <dl className="rounded-2xl border border-slate-200 px-4">
                                    <DetailRow label="Requester" value={ticket.requester ? `${ticket.requester.name} (${ticket.requester.email})` : 'n/a'} />
                                    <DetailRow label="Supplier" value={ticket.supplier?.company_name || 'n/a'} />
                                    <DetailRow label="Order" value={ticket.order?.order_number || 'n/a'} />
                                    <DetailRow label="Customer" value={ticket.customer ? `${ticket.customer.contact_name} (${ticket.customer.company_name || ticket.customer.email})` : 'n/a'} />
                                    <DetailRow label="Assignee" value={ticket.assignee ? `${ticket.assignee.name} (${ticket.assignee.email})` : 'Unassigned'} />
                                    <DetailRow label="Channel" value={formatLabel(ticket.channel)} />
                                    <DetailRow label="Created" value={formatDate(ticket.created_at)} />
                                    <DetailRow label="Resolved" value={formatDate(ticket.resolved_at)} />
                                </dl>
                            </Panel>

                            <Panel
                                title="Conversation"
                                description="Message history stays attached to the ticket so support and automation have a clear audit trail."
                            >
                                <div className="space-y-4">
                                    {(ticket.messages || []).length > 0 ? (
                                        ticket.messages.map((message) => (
                                            <article key={message.id} className="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                                <div className="flex flex-wrap items-center justify-between gap-2">
                                                    <div className="flex flex-wrap items-center gap-2">
                                                        <Pill value={message.sender_type} />
                                                        <span className="text-xs font-semibold text-slate-500">
                                                            {message.sender ? `${message.sender.name} (${message.sender.email})` : 'Automation'}
                                                        </span>
                                                    </div>
                                                    <span className="text-xs font-semibold text-slate-500">{formatDate(message.created_at)}</span>
                                                </div>
                                                <p className="mt-3 whitespace-pre-line text-sm leading-6 text-slate-700">
                                                    {message.message}
                                                </p>
                                            </article>
                                        ))
                                    ) : (
                                        <div className="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-6 text-sm text-slate-500">
                                            No messages have been added yet.
                                        </div>
                                    )}
                                </div>
                            </Panel>
                        </div>

                        <div className="space-y-6">
                            <Panel
                                title="Reply"
                                description="Send a message back to the customer, supplier, or internal support team."
                            >
                                <FlashBanner message={flash?.success} />
                                <FlashBanner message={flash?.error} type="error" className="mt-4" />
                                <FlashBanner message={validationMessage} type="error" className="mt-4" />

                                <form onSubmit={sendReply} className="mt-5 space-y-4">
                                    <textarea
                                        value={replyForm.data.message}
                                        onChange={(event) => replyForm.setData('message', event.target.value)}
                                        className="input min-h-[160px] resize-y"
                                        placeholder="Write your reply..."
                                        required
                                    />
                                    <button
                                        type="submit"
                                        disabled={replyForm.processing}
                                        className="inline-flex w-full items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        {replyForm.processing ? 'Sending...' : 'Send reply'}
                                    </button>
                                </form>
                            </Panel>

                            {can_manage_status ? (
                                <>
                                    <Panel
                                        title="Status"
                                        description="Move the ticket through the operational support lifecycle."
                                    >
                                        <form onSubmit={updateStatus} className="space-y-4">
                                            <select
                                                value={statusForm.data.status}
                                                onChange={(event) => statusForm.setData('status', event.target.value)}
                                                className="input"
                                            >
                                                {statuses.map((status) => (
                                                    <option key={status} value={status}>
                                                        {formatLabel(status)}
                                                    </option>
                                                ))}
                                            </select>
                                            <button
                                                type="submit"
                                                disabled={statusForm.processing}
                                                className="inline-flex w-full items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:border-blue-200 hover:text-blue-800 disabled:cursor-not-allowed disabled:opacity-50"
                                            >
                                                {statusForm.processing ? 'Updating...' : 'Update status'}
                                            </button>
                                        </form>
                                    </Panel>

                                </>
                            ) : null}

                            {can_assign ? (
                                <Panel
                                    title="Assignment"
                                    description="Keep ownership visible for the support team."
                                >
                                    <form onSubmit={updateAssignment} className="space-y-4">
                                        <select
                                            value={assignForm.data.assigned_to}
                                            onChange={(event) => assignForm.setData('assigned_to', event.target.value)}
                                            className="input"
                                        >
                                            <option value="">Unassigned</option>
                                            {assignees.map((assignee) => (
                                                <option key={assignee.id} value={assignee.id}>
                                                    {assignee.name} ({assignee.email})
                                                </option>
                                            ))}
                                        </select>
                                        <button
                                            type="submit"
                                            disabled={assignForm.processing}
                                            className="inline-flex w-full items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:border-blue-200 hover:text-blue-800 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            {assignForm.processing ? 'Saving...' : 'Assign ticket'}
                                        </button>
                                    </form>
                                </Panel>
                            ) : null}
                        </div>
                    </section>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
