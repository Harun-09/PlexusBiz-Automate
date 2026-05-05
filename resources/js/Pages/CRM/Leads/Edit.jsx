import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import FlashBanner from '@/Components/FlashBanner';
import PageHeader from '@/Components/PageHeader';
import { actionButtonClasses } from '@/Utils/pillStyles';
import { Head, Link, useForm } from '@inertiajs/react';

function Field({ label, error, children, hint = null }) {
    return (
        <label className="block">
            <span className="mb-2 block text-xs font-black uppercase tracking-[0.18em] text-slate-500">{label}</span>
            {children}
            {hint ? <span className="mt-1.5 block text-xs text-slate-500">{hint}</span> : null}
            {error ? <span className="mt-1.5 block text-sm text-rose-600">{error}</span> : null}
        </label>
    );
}

export default function Edit({ auth, flash, errors, lead, statuses = [], customers = [], assignees = [] }) {
    const { data, setData, put, processing } = useForm({
        customer_id: lead.customer_id || '',
        assigned_user_id: lead.assigned_user_id || '',
        source: lead.source || '',
        status: lead.status || statuses[0] || 'new',
        company_name: lead.company_name || '',
        contact_name: lead.contact_name || '',
        email: lead.email || '',
        phone: lead.phone || '',
        value: lead.value || '',
        notes: lead.notes || '',
        next_follow_up_at: lead.next_follow_up_at || '',
    });

    const submit = (event) => {
        event.preventDefault();
        put(route('crm.leads.update', lead.id), { preserveScroll: true });
    };

    const validationMessage = Object.values(errors || {}).find(Boolean);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <PageHeader
                    eyebrow="CRM"
                    title={`Edit lead: ${lead.contact_name}`}
                    description="Update the lead status, owner, follow-up date, and qualification notes."
                    actions={
                        <Link href={route('crm.leads.index')} className={`inline-flex items-center justify-center rounded-full border px-4 py-2 text-sm font-bold transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('neutral')}`}>
                            Back to leads
                        </Link>
                    }
                />
            }
        >
            <Head title={`Edit Lead - ${lead.contact_name}`} />

            <div className="py-8">
                <div className="w-full">
                    <section className="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                        <div className="border-b border-slate-100 bg-slate-50 px-5 py-5 sm:px-8">
                            <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Lead editor</p>
                            <h2 className="mt-1 text-2xl font-black tracking-tight text-slate-950">Update lead details</h2>
                        </div>

                        <div className="px-5 py-6 sm:px-8">
                            <FlashBanner message={flash?.success} />
                            <FlashBanner message={flash?.error} type="error" className="mt-4" />
                            <FlashBanner message={validationMessage} type="error" className="mt-4" />

                            <form onSubmit={submit} className="mt-6 space-y-5">
                                <div className="grid gap-5 sm:grid-cols-2">
                                    <Field label="Contact Name" error={errors.contact_name}>
                                        <input value={data.contact_name} onChange={(event) => setData('contact_name', event.target.value)} className="input" required />
                                    </Field>
                                    <Field label="Company Name" error={errors.company_name}>
                                        <input value={data.company_name} onChange={(event) => setData('company_name', event.target.value)} className="input" />
                                    </Field>
                                </div>

                                <div className="grid gap-5 sm:grid-cols-2">
                                    <Field label="Email" error={errors.email}>
                                        <input type="email" value={data.email} onChange={(event) => setData('email', event.target.value)} className="input" />
                                    </Field>
                                    <Field label="Phone" error={errors.phone}>
                                        <input value={data.phone} onChange={(event) => setData('phone', event.target.value)} className="input" />
                                    </Field>
                                </div>

                                <div className="grid gap-5 sm:grid-cols-3">
                                    <Field label="Status" error={errors.status}>
                                        <select value={data.status} onChange={(event) => setData('status', event.target.value)} className="input">
                                            {statuses.map((status) => <option key={status} value={status}>{status.replace(/_/g, ' ')}</option>)}
                                        </select>
                                    </Field>
                                    <Field label="Source" error={errors.source}>
                                        <input value={data.source} onChange={(event) => setData('source', event.target.value)} className="input" />
                                    </Field>
                                    <Field label="Pipeline Value" error={errors.value}>
                                        <input type="number" min="0" step="0.01" value={data.value} onChange={(event) => setData('value', event.target.value)} className="input" />
                                    </Field>
                                </div>

                                <div className="grid gap-5 sm:grid-cols-2">
                                    <Field label="Linked Customer" error={errors.customer_id}>
                                        <select value={data.customer_id} onChange={(event) => setData('customer_id', event.target.value)} className="input">
                                            <option value="">No customer link</option>
                                            {customers.map((customer) => <option key={customer.id} value={customer.id}>{customer.label}</option>)}
                                        </select>
                                    </Field>
                                    <Field label="Assigned Owner" error={errors.assigned_user_id}>
                                        <select value={data.assigned_user_id} onChange={(event) => setData('assigned_user_id', event.target.value)} className="input">
                                            <option value="">Unassigned</option>
                                            {assignees.map((assignee) => <option key={assignee.id} value={assignee.id}>{assignee.label}</option>)}
                                        </select>
                                    </Field>
                                </div>

                                <Field label="Next Follow Up" error={errors.next_follow_up_at}>
                                    <input type="datetime-local" value={data.next_follow_up_at} onChange={(event) => setData('next_follow_up_at', event.target.value)} className="input" />
                                </Field>

                                <Field label="Notes" error={errors.notes}>
                                    <textarea value={data.notes} onChange={(event) => setData('notes', event.target.value)} className="input min-h-[140px]" />
                                </Field>

                                <button type="submit" disabled={processing} className="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50">
                                    {processing ? 'Updating...' : 'Update lead'}
                                </button>
                            </form>
                        </div>
                    </section>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
