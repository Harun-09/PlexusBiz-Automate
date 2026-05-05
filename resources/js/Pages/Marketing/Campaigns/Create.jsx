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

export default function Create({ auth, flash, errors, campaignTypes = ['email'], statuses = [] }) {
    const { data, setData, post, processing, reset } = useForm({
        name: '',
        type: campaignTypes[0] || 'email',
        status: statuses.includes('draft') ? 'draft' : statuses[0] || 'draft',
        segment_tags: '',
        scheduled_at: '',
    });

    const submit = (event) => {
        event.preventDefault();

        post(route('marketing.campaigns.store'), {
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
                    title="Create campaign"
                    description="Build an email campaign that can be scheduled now and edited later from the UI."
                    actions={
                        <Link
                            href={route('marketing.campaigns.index')}
                            className={`inline-flex items-center justify-center rounded-full border px-4 py-2 text-sm font-bold transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('neutral')}`}
                        >
                            Back to email campaigns
                        </Link>
                    }
                />
            }
        >
            <Head title="Create Campaign" />

            <div className="py-8">
                <div className="w-full">
                    <section className="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                        <div className="border-b border-slate-100 bg-slate-50 px-5 py-5 sm:px-8">
                            <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">
                                Email-only marketing
                            </p>
                            <h2 className="mt-1 text-2xl font-black tracking-tight text-slate-950">
                                Create a new campaign
                            </h2>
                            <p className="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                                Campaigns are configured for email delivery in this module, with status, scheduling, and segment tags editable from the UI.
                            </p>
                        </div>

                        <div className="px-5 py-6 sm:px-8">
                            <FlashBanner message={flash?.success} />
                            <FlashBanner message={flash?.error} type="error" className="mt-4" />
                            <FlashBanner message={validationMessage} type="error" className="mt-4" />

                            <form onSubmit={submit} className="mt-6 space-y-5">
                                <div className="grid gap-5">
                                    <Field label="Campaign Name" error={errors.name} hint="Use a clear label such as Eid Offer or Priority Wholesale Welcome.">
                                        <input
                                            value={data.name}
                                            onChange={(event) => setData('name', event.target.value)}
                                            className="input"
                                            placeholder="Eid Special Offer"
                                            required
                                        />
                                    </Field>

                                    <div className="grid gap-5 sm:grid-cols-2">
                                        <Field label="Type" error={errors.type} hint="This module currently uses email-only campaigns.">
                                            <select
                                                value={data.type}
                                                onChange={(event) => setData('type', event.target.value)}
                                                className="input"
                                            >
                                                {campaignTypes.map((type) => (
                                                    <option key={type} value={type}>
                                                        {type.toUpperCase()}
                                                    </option>
                                                ))}
                                            </select>
                                        </Field>

                                        <Field label="Status" error={errors.status} hint="Draft campaigns can be reviewed before scheduling.">
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

                                    <Field label="Target Audience Tags" error={errors.segment_tags} hint="Comma-separated tags such as wholesale, wholesale-repeat, eid-buyers.">
                                        <input
                                            value={data.segment_tags}
                                            onChange={(event) => setData('segment_tags', event.target.value)}
                                            className="input"
                                            placeholder="wholesale, eid-buyers"
                                        />
                                    </Field>

                                    <Field label="Scheduled At" error={errors.scheduled_at} hint="Use Bangladesh local time. Leave blank to keep it as a draft; scheduled campaigns without a time are picked up on the next scheduler pass.">
                                        <input
                                            type="datetime-local"
                                            value={data.scheduled_at}
                                            onChange={(event) => setData('scheduled_at', event.target.value)}
                                            className="input"
                                        />
                                    </Field>
                                </div>

                                <div className="flex flex-wrap items-center gap-3">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        {processing ? 'Saving...' : 'Create campaign'}
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
