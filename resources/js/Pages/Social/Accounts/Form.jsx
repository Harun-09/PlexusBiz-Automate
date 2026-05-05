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

const labelValue = (value) => String(value || '')
    .replace(/[_-]/g, ' ')
    .split(/\s+/)
    .filter(Boolean)
    .map((part) => part.charAt(0).toUpperCase() + part.slice(1).toLowerCase())
    .join(' ');

export default function Form({
    auth,
    flash,
    errors,
    account = null,
    platforms = [],
    statuses = [],
    modes = [],
    submitUrl,
    submitMethod = 'post',
}) {
    const isEditing = Boolean(account);
    const { data, setData, post, put, processing, reset } = useForm({
        platform: account?.platform || platforms[0] || 'facebook',
        name: account?.name || '',
        handle: account?.handle || '',
        status: account?.status || statuses[0] || 'active',
        mode: account?.credentials_mode || modes[0] || 'mock',
        page_id: account?.page_id || '',
        access_token: '',
    });

    const submit = (event) => {
        event.preventDefault();

        const options = {
            preserveScroll: true,
        };

        if (!isEditing) {
            options.onSuccess = () => reset();
        }

        if (submitMethod === 'put') {
            put(submitUrl, options);
            return;
        }

        post(submitUrl, options);
    };

    const validationMessage = Object.values(errors || {}).find(Boolean);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <PageHeader
                    eyebrow="Social Media Automation"
                    title={isEditing ? 'Edit social account' : 'Add social account'}
                    description="Register the publishing identity used by the social adapters. Store the Facebook Page ID and access token for live publishing, or keep mock mode for demo-only accounts."
                    actions={
                        <Link
                            href={route('social.accounts.index')}
                            className={`inline-flex items-center justify-center rounded-full border px-4 py-2 text-sm font-bold transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('neutral')}`}
                        >
                            Back to accounts
                        </Link>
                    }
                />
            }
        >
            <Head title={isEditing ? 'Edit Social Account' : 'Add Social Account'} />

            <div className="py-8">
                <div className="w-full">
                    <section className="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                        <div className="border-b border-slate-100 bg-slate-50 px-5 py-5 sm:px-8">
                            <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Registry metadata only</p>
                            <h2 className="mt-1 text-2xl font-black tracking-tight text-slate-950">
                                {isEditing ? 'Update an existing account entry' : 'Create a new account entry'}
                            </h2>
                            <p className="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                                Use this form to keep the platform, handle, Facebook Page ID, access token, status, and demo connection mode attached to each publishing identity.
                            </p>
                        </div>

                        <div className="px-5 py-6 sm:px-8">
                            <FlashBanner message={flash?.success} />
                            <FlashBanner message={flash?.error} type="error" className="mt-4" />
                            <FlashBanner message={validationMessage} type="error" className="mt-4" />

                            <form onSubmit={submit} className="mt-6 space-y-5">
                                <div className="grid gap-5 sm:grid-cols-2">
                                    <Field label="Platform" error={errors.platform} hint="Choose the network this account belongs to.">
                                        <select value={data.platform} onChange={(event) => setData('platform', event.target.value)} className="input">
                                            {platforms.map((platform) => (
                                                <option key={platform} value={platform}>
                                                    {labelValue(platform)}
                                                </option>
                                            ))}
                                        </select>
                                    </Field>

                                    <Field label="Status" error={errors.status} hint="Inactive accounts stay in the registry but are filtered out of active workflows.">
                                        <select value={data.status} onChange={(event) => setData('status', event.target.value)} className="input">
                                            {statuses.map((status) => (
                                                <option key={status} value={status}>
                                                    {labelValue(status)}
                                                </option>
                                            ))}
                                        </select>
                                    </Field>
                                </div>

                                <div className="grid gap-5 sm:grid-cols-2">
                                    <Field label="Account Name" error={errors.name} hint="Use a human-friendly label such as PlexusBiz Facebook.">
                                        <input
                                            value={data.name}
                                            onChange={(event) => setData('name', event.target.value)}
                                            className="input"
                                            placeholder="PlexusBiz Facebook"
                                            required
                                        />
                                    </Field>

                                    <Field label="Handle" error={errors.handle} hint="Optional, but useful for matching the visible profile handle.">
                                        <input
                                            value={data.handle}
                                            onChange={(event) => setData('handle', event.target.value)}
                                            className="input"
                                            placeholder="@plexusbiz"
                                        />
                                    </Field>
                                </div>

                                <div className="grid gap-5 sm:grid-cols-2">
                                    <Field label="Facebook Page ID" error={errors.page_id} hint="Needed for live Facebook publishing. Leave blank to keep the current value while editing.">
                                        <input
                                            value={data.page_id}
                                            onChange={(event) => setData('page_id', event.target.value)}
                                            className="input"
                                            placeholder="123456789012345"
                                        />
                                    </Field>

                                    <Field
                                        label="Access Token"
                                        error={errors.access_token}
                                        hint={isEditing && account?.has_access_token
                                            ? 'Token is stored encrypted. Leave blank to keep the current token.'
                                            : 'Store a page or app access token here for live publishing.'}
                                    >
                                        <input
                                            type="password"
                                            value={data.access_token}
                                            onChange={(event) => setData('access_token', event.target.value)}
                                            className="input"
                                            placeholder={isEditing && account?.has_access_token ? 'stored token' : 'Page access token'}
                                            autoComplete="off"
                                        />
                                    </Field>
                                </div>

                                <Field label="Connection Mode" error={errors.mode} hint="Mock keeps the registry demo-only; live only marks the account as intended for a real provider later.">
                                    <select value={data.mode} onChange={(event) => setData('mode', event.target.value)} className="input">
                                        {modes.map((mode) => (
                                            <option key={mode} value={mode}>
                                                {labelValue(mode)}
                                            </option>
                                        ))}
                                    </select>
                                </Field>

                                <div className="flex flex-wrap items-center gap-3">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        {processing ? 'Saving...' : (isEditing ? 'Update account' : 'Create account')}
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
