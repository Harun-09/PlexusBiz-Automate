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

const label = (value) => String(value || '').replace(/[._-]/g, ' ');

export default function Edit({ auth, flash, errors, rule, triggers = [], actions = [], statuses = [], operators = [] }) {
    const { data, setData, put, processing } = useForm({
        name: rule.name || '',
        trigger_event: rule.trigger_event || triggers[0] || 'order.placed',
        status: rule.status || statuses[0] || 'active',
        priority: rule.priority || 100,
        run_async: Boolean(rule.run_async),
        condition_field: rule.condition_field || '',
        condition_operator: rule.condition_operator || operators[0] || 'equals',
        condition_value: rule.condition_value || '',
        action_types: rule.action_types || [],
        subject: rule.subject || '',
        message: rule.message || '',
    });

    const toggleAction = (action) => {
        const next = data.action_types.includes(action)
            ? data.action_types.filter((item) => item !== action)
            : [...data.action_types, action];
        setData('action_types', next);
    };

    const submit = (event) => {
        event.preventDefault();
        put(route('workflow.rules.update', rule.id), { preserveScroll: true });
    };

    const validationMessage = Object.values(errors || {}).find(Boolean);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <PageHeader
                    eyebrow="Workflow Automation"
                    title={`Edit rule: ${rule.name}`}
                    description="Update IF event, optional condition, THEN actions, priority, and runtime mode."
                    actions={
                        <Link href={route('workflow.rules.index')} className={`inline-flex items-center justify-center rounded-full border px-4 py-2 text-sm font-bold transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('neutral')}`}>
                            Back to rules
                        </Link>
                    }
                />
            }
        >
            <Head title={`Edit Rule - ${rule.name}`} />

            <div className="py-8">
                <div className="w-full">
                    <section className="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                        <div className="border-b border-slate-100 bg-slate-50 px-5 py-5 sm:px-8">
                            <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Rule editor</p>
                            <h2 className="mt-1 text-2xl font-black tracking-tight text-slate-950">Update automation logic</h2>
                        </div>

                        <div className="px-5 py-6 sm:px-8">
                            <FlashBanner message={flash?.success} />
                            <FlashBanner message={flash?.error} type="error" className="mt-4" />
                            <FlashBanner message={validationMessage} type="error" className="mt-4" />

                            <form onSubmit={submit} className="mt-6 space-y-5">
                                <Field label="Rule Name" error={errors.name}>
                                    <input value={data.name} onChange={(event) => setData('name', event.target.value)} className="input" required />
                                </Field>

                                <div className="space-y-5">
                                    <Field label="IF Event" error={errors.trigger_event}>
                                        <select value={data.trigger_event} onChange={(event) => setData('trigger_event', event.target.value)} className="input">
                                            {triggers.map((trigger) => <option key={trigger} value={trigger}>{label(trigger)}</option>)}
                                        </select>
                                    </Field>
                                    <Field label="Status" error={errors.status}>
                                        <select value={data.status} onChange={(event) => setData('status', event.target.value)} className="input">
                                            {statuses.map((status) => <option key={status} value={status}>{label(status)}</option>)}
                                        </select>
                                    </Field>
                                    <Field label="Priority" error={errors.priority}>
                                        <input type="number" min="1" value={data.priority} onChange={(event) => setData('priority', event.target.value)} className="input" />
                                    </Field>
                                </div>

                                <div className="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <p className="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Optional IF condition</p>
                                    <div className="mt-4 grid gap-4 sm:grid-cols-3">
                                        <Field label="Payload Field" error={errors.condition_field}>
                                            <input value={data.condition_field} onChange={(event) => setData('condition_field', event.target.value)} className="input bg-white" />
                                        </Field>
                                        <Field label="Operator" error={errors.condition_operator}>
                                            <select value={data.condition_operator} onChange={(event) => setData('condition_operator', event.target.value)} className="input bg-white">
                                                {operators.map((operator) => <option key={operator} value={operator}>{label(operator)}</option>)}
                                            </select>
                                        </Field>
                                        <Field label="Value" error={errors.condition_value}>
                                            <input value={data.condition_value} onChange={(event) => setData('condition_value', event.target.value)} className="input bg-white" />
                                        </Field>
                                    </div>
                                </div>

                                <div>
                                    <p className="mb-2 block text-xs font-black uppercase tracking-[0.18em] text-slate-500">THEN Actions</p>
                                    <div className="grid gap-3 sm:grid-cols-2">
                                        {actions.map((action) => (
                                            <label key={action} className="flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4">
                                                <input type="checkbox" checked={data.action_types.includes(action)} onChange={() => toggleAction(action)} className="h-4 w-4 rounded border-slate-300 text-teal-700 focus:ring-teal-600" />
                                                <span className="text-sm font-bold capitalize text-slate-800">{label(action)}</span>
                                            </label>
                                        ))}
                                    </div>
                                    {errors.action_types ? <span className="mt-1.5 block text-sm text-rose-600">{errors.action_types}</span> : null}
                                </div>

                                <div className="grid gap-5 sm:grid-cols-2">
                                    <Field label="Subject" error={errors.subject}>
                                        <input value={data.subject} onChange={(event) => setData('subject', event.target.value)} className="input" />
                                    </Field>
                                    <label className="flex items-center gap-3 self-end rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                        <input type="checkbox" checked={data.run_async} onChange={(event) => setData('run_async', event.target.checked)} className="h-4 w-4 rounded border-slate-300 text-teal-700 focus:ring-teal-600" />
                                        <span className="text-sm font-bold text-slate-700">Run async through queue</span>
                                    </label>
                                </div>

                                <Field label="Message Body" error={errors.message}>
                                    <textarea value={data.message} onChange={(event) => setData('message', event.target.value)} className="input min-h-[130px]" />
                                </Field>

                                <button type="submit" disabled={processing} className="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50">
                                    {processing ? 'Updating...' : 'Update rule'}
                                </button>
                            </form>
                        </div>
                    </section>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
