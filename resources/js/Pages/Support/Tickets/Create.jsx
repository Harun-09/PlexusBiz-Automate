import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import FlashBanner from '@/Components/FlashBanner';
import PageHeader from '@/Components/PageHeader';
import { actionButtonClasses } from '@/Utils/pillStyles';
import { Head, Link, useForm } from '@inertiajs/react';

function Field({ label, error, children, hint = null }) {
    return (
        <label className="block">
            <span className="mb-2 block text-xs font-black uppercase tracking-[0.18em] text-slate-500">
                {label}
            </span>
            {children}
            {hint ? <span className="mt-1.5 block text-xs text-slate-500">{hint}</span> : null}
            {error ? <span className="mt-1.5 block text-sm text-rose-600">{error}</span> : null}
        </label>
    );
}

export default function Create({ auth, flash, errors, priorities = [] }) {
    const { data, setData, post, processing, reset } = useForm({
        subject: '',
        description: '',
        priority: priorities.includes('normal') ? 'normal' : priorities[0] || 'normal',
        supplier_id: null,
        order_id: null,
    });

    const submit = (event) => {
        event.preventDefault();

        post(route('support.tickets.store'), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    };

    const validationMessage = Object.values(errors || {}).find(Boolean);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <PageHeader
                    eyebrow="Support"
                    title="Create ticket"
                    description="Open a buyer or supplier support request and keep the issue inside the automation-ready ticket flow."
                    actions={
                        <Link
                            href={route('support.tickets.index')}
                            className={`inline-flex items-center justify-center rounded-full border px-4 py-2 text-sm font-bold transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('neutral')}`}
                        >
                            Back to tickets
                        </Link>
                    }
                />
            }
        >
            <Head title="Create Support Ticket" />

            <div className="py-8">
                <div className="w-full">
                    <section className="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                        <div className="border-b border-slate-100 bg-slate-50 px-5 py-5 sm:px-8">
                            <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">
                                Support intake
                            </p>
                            <h2 className="mt-1 text-2xl font-black tracking-tight text-slate-950">
                                Create a new support ticket
                            </h2>
                            <p className="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                                Use this form when you need help with an order, supplier issue, account problem, or product question.
                            </p>
                        </div>

                        <div className="px-5 py-6 sm:px-8">
                            <FlashBanner message={flash?.success} />
                            <FlashBanner message={flash?.error} type="error" className="mt-4" />
                            <FlashBanner message={validationMessage} type="error" className="mt-4" />

                            <form onSubmit={submit} className="mt-6 space-y-5">
                                <div className="grid gap-5">
                                    <Field label="Subject" error={errors.subject} hint="Short title describing the problem.">
                                        <input
                                            value={data.subject}
                                            onChange={(event) => setData('subject', event.target.value)}
                                            className="input"
                                            placeholder="Order delivery delay"
                                            required
                                        />
                                    </Field>

                                    <Field label="Priority" error={errors.priority}>
                                        <select
                                            value={data.priority}
                                            onChange={(event) => setData('priority', event.target.value)}
                                            className="input"
                                        >
                                            {priorities.map((priority) => (
                                                <option key={priority} value={priority}>
                                                    {priority.replace(/_/g, ' ')}
                                                </option>
                                            ))}
                                        </select>
                                    </Field>

                                    <div className="grid gap-5 sm:grid-cols-2">
                                        <Field label="Supplier ID" error={errors.supplier_id} hint="Optional. Use this if the issue is tied to a specific vendor.">
                                            <input
                                                type="number"
                                                min="1"
                                                value={data.supplier_id ?? ''}
                                                onChange={(event) => setData('supplier_id', event.target.value === '' ? null : event.target.value)}
                                                className="input"
                                                placeholder="Supplier database ID"
                                            />
                                        </Field>

                                        <Field label="Order ID" error={errors.order_id} hint="Optional. Use the database order ID when the ticket is order-specific.">
                                            <input
                                                type="number"
                                                min="1"
                                                value={data.order_id ?? ''}
                                                onChange={(event) => setData('order_id', event.target.value === '' ? null : event.target.value)}
                                                className="input"
                                                placeholder="Order database ID"
                                            />
                                        </Field>
                                    </div>

                                    <Field label="Description" error={errors.description} hint="Include the order number, supplier name, or any relevant context.">
                                        <textarea
                                            value={data.description}
                                            onChange={(event) => setData('description', event.target.value)}
                                            className="input min-h-[180px] resize-y"
                                            placeholder="Describe the issue in detail..."
                                            required
                                        />
                                    </Field>
                                </div>

                                <div className="flex flex-wrap items-center gap-3">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        {processing ? 'Submitting...' : 'Create ticket'}
                                    </button>

                                    <Link
                                        href="/support/faq"
                                        className="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:border-blue-200 hover:text-blue-800"
                                    >
                                        View FAQ
                                    </Link>
                                </div>
                            </form>
                        </div>
                    </section>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
