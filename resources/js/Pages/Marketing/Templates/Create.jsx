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

export default function Create({ auth, flash, errors, campaigns = [], channels = ['email'], statuses = [] }) {
    const { data, setData, post, processing, reset } = useForm({
        campaign_id: '',
        template_key: '',
        channel: channels[0] || 'email',
        name: '',
        subject: '',
        body: '',
        variables: '',
        status: statuses.includes('active') ? 'active' : statuses[0] || 'active',
    });

    const submit = (event) => {
        event.preventDefault();

        post(route('marketing.templates.store'), {
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
                    eyebrow="Marketing Automation"
                    title="Create template"
                    description="Create an email template that can be reused by campaigns and trigger rules."
                    actions={
                        <Link
                            href={route('marketing.templates.index')}
                            className={`inline-flex items-center justify-center rounded-full border px-4 py-2 text-sm font-bold transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('neutral')}`}
                        >
                            Back to templates
                        </Link>
                    }
                />
            }
        >
            <Head title="Create Template" />

            <div className="py-8">
                <div className="w-full">
                    <section className="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                        <div className="border-b border-slate-100 bg-slate-50 px-5 py-5 sm:px-8">
                            <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">
                                Email-only templates
                            </p>
                            <h2 className="mt-1 text-2xl font-black tracking-tight text-slate-950">
                                Create a reusable email template
                            </h2>
                            <p className="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                                Templates stay editable from the UI so marketing can reuse subjects, bodies, and variables without touching code.
                            </p>
                        </div>

                        <div className="px-5 py-6 sm:px-8">
                            <FlashBanner message={flash?.success} />
                            <FlashBanner message={flash?.error} type="error" className="mt-4" />
                            <FlashBanner message={validationMessage} type="error" className="mt-4" />

                            <form onSubmit={submit} className="mt-6 space-y-5">
                                <div className="grid gap-5">
                                    <Field label="Template Name" error={errors.name} hint="Human-friendly template name.">
                                        <input
                                            value={data.name}
                                            onChange={(event) => setData('name', event.target.value)}
                                            className="input"
                                            placeholder="Welcome Email"
                                            required
                                        />
                                    </Field>

                                    <div className="grid gap-5 sm:grid-cols-2">
                                        <Field label="Template Key" error={errors.template_key} hint="Optional. Leave blank to auto-generate from the name.">
                                            <input
                                                value={data.template_key}
                                                onChange={(event) => setData('template_key', event.target.value)}
                                                className="input"
                                                placeholder="welcome_email"
                                            />
                                        </Field>

                                        <Field label="Campaign" error={errors.campaign_id} hint="Optional. Link the template to a specific campaign.">
                                            <select
                                                value={data.campaign_id}
                                                onChange={(event) => setData('campaign_id', event.target.value)}
                                                className="input"
                                            >
                                                <option value="">Standalone</option>
                                                {campaigns.map((campaign) => (
                                                    <option key={campaign.id} value={campaign.id}>
                                                        {campaign.label}
                                                    </option>
                                                ))}
                                            </select>
                                        </Field>
                                    </div>

                                    <div className="grid gap-5 sm:grid-cols-2">
                                        <Field label="Channel" error={errors.channel} hint="This module currently keeps templates email-only.">
                                            <select value={data.channel} className="input" disabled>
                                                {channels.map((channel) => (
                                                    <option key={channel} value={channel}>
                                                        {channel.toUpperCase()}
                                                    </option>
                                                ))}
                                            </select>
                                        </Field>

                                        <Field label="Status" error={errors.status}>
                                            <select
                                                value={data.status}
                                                onChange={(event) => setData('status', event.target.value)}
                                                className="input"
                                            >
                                                {statuses.map((status) => (
                                                    <option key={status} value={status}>
                                                        {status.replace(/_/g, ' ')}
                                                    </option>
                                                ))}
                                            </select>
                                        </Field>
                                    </div>

                                    <Field label="Subject" error={errors.subject} hint="Optional for plain body templates, but recommended for email campaigns.">
                                        <input
                                            value={data.subject}
                                            onChange={(event) => setData('subject', event.target.value)}
                                            className="input"
                                            placeholder="Welcome to PlexusBiz, {{ customer_name }}"
                                        />
                                    </Field>

                                    <Field label="Body" error={errors.body} hint="Use comma-separated variables below, such as customer_name, company_name.">
                                        <textarea
                                            value={data.body}
                                            onChange={(event) => setData('body', event.target.value)}
                                            className="input min-h-[180px] resize-y"
                                            placeholder="Hello {{ customer_name }}, welcome to PlexusBiz."
                                            required
                                        />
                                    </Field>

                                    <Field label="Variables" error={errors.variables} hint="Comma-separated placeholders used by the template.">
                                        <input
                                            value={data.variables}
                                            onChange={(event) => setData('variables', event.target.value)}
                                            className="input"
                                            placeholder="customer_name, company_name, order_number"
                                        />
                                    </Field>
                                </div>

                                <div className="flex flex-wrap items-center gap-3">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        {processing ? 'Saving...' : 'Create template'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
