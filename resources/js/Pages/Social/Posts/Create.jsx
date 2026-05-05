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

export default function Create({ auth, flash, errors, platforms = [], statuses = [], accounts = [], campaigns = [] }) {
    const { data, setData, post, processing, reset } = useForm({
        campaign_id: '',
        social_account_id: '',
        platform: platforms[0] || 'facebook',
        content: '',
        media_url: '',
        scheduled_at: '',
        status: statuses.includes('scheduled') ? 'scheduled' : statuses[0] || 'draft',
        likes_count: 0,
        comments_count: 0,
        shares_count: 0,
    });

    const submit = (event) => {
        event.preventDefault();

        post(route('social.posts.store'), {
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
                    eyebrow="Social Media Automation"
                    title="Schedule post"
                    description="Create Facebook or Instagram content with schedule time, status, and engagement placeholders."
                    actions={
                        <Link href={route('social.posts.index')} className={`inline-flex items-center justify-center rounded-full border px-4 py-2 text-sm font-bold transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('neutral')}`}>
                            Back to posts
                        </Link>
                    }
                />
            }
        >
            <Head title="Schedule Social Post" />

            <div className="py-8">
                <div className="w-full">
                    <section className="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                        <div className="border-b border-slate-100 bg-slate-50 px-5 py-5 sm:px-8">
                            <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Mock publishing workflow</p>
                            <h2 className="mt-1 text-2xl font-black tracking-tight text-slate-950">Create scheduled content</h2>
                            <p className="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                                Due scheduled posts are picked up by Laravel scheduler and published through the mock Facebook or Instagram adapter.
                            </p>
                        </div>

                        <div className="px-5 py-6 sm:px-8">
                            <FlashBanner message={flash?.success} />
                            <FlashBanner message={flash?.error} type="error" className="mt-4" />
                            <FlashBanner message={validationMessage} type="error" className="mt-4" />

                            <form onSubmit={submit} className="mt-6 space-y-5">
                                <div className="grid gap-5 sm:grid-cols-2">
                                    <Field label="Platform" error={errors.platform}>
                                        <select value={data.platform} onChange={(event) => setData('platform', event.target.value)} className="input">
                                            {platforms.map((platform) => <option key={platform} value={platform}>{platform}</option>)}
                                        </select>
                                    </Field>

                                    <Field label="Status" error={errors.status}>
                                        <select value={data.status} onChange={(event) => setData('status', event.target.value)} className="input">
                                            {statuses.map((status) => <option key={status} value={status}>{status.replace(/_/g, ' ')}</option>)}
                                        </select>
                                    </Field>
                                </div>

                                <div className="grid gap-5 sm:grid-cols-2">
                                    <Field label="Social Account" error={errors.social_account_id}>
                                        <select value={data.social_account_id} onChange={(event) => setData('social_account_id', event.target.value)} className="input">
                                            <option value="">No account selected</option>
                                            {accounts.map((account) => <option key={account.id} value={account.id}>{account.label}</option>)}
                                        </select>
                                    </Field>

                                    <Field label="Campaign" error={errors.campaign_id}>
                                        <select value={data.campaign_id} onChange={(event) => setData('campaign_id', event.target.value)} className="input">
                                            <option value="">No campaign</option>
                                            {campaigns.map((campaign) => <option key={campaign.id} value={campaign.id}>{campaign.label}</option>)}
                                        </select>
                                    </Field>
                                </div>

                                <Field label="Content" error={errors.content}>
                                    <textarea value={data.content} onChange={(event) => setData('content', event.target.value)} className="input min-h-[160px]" placeholder="Eid wholesale offer: bulk buyers get special pricing this week." required />
                                </Field>

                                <div className="grid gap-5 sm:grid-cols-2">
                                    <Field label="Image URL Optional" error={errors.media_url} hint="Use a URL for demo media, or leave blank.">
                                        <input value={data.media_url} onChange={(event) => setData('media_url', event.target.value)} className="input" placeholder="https://example.com/image.jpg" />
                                    </Field>

                                    <Field label="Schedule Date/Time" error={errors.scheduled_at} hint="Use Bangladesh local time. Leave blank and scheduled posts will be picked up on the next scheduler pass.">
                                        <input type="datetime-local" value={data.scheduled_at} onChange={(event) => setData('scheduled_at', event.target.value)} className="input" />
                                    </Field>
                                </div>

                                <div className="grid gap-5 sm:grid-cols-3">
                                    <Field label="Likes" error={errors.likes_count}>
                                        <input type="number" min="0" value={data.likes_count} onChange={(event) => setData('likes_count', event.target.value)} className="input" />
                                    </Field>
                                    <Field label="Comments" error={errors.comments_count}>
                                        <input type="number" min="0" value={data.comments_count} onChange={(event) => setData('comments_count', event.target.value)} className="input" />
                                    </Field>
                                    <Field label="Shares" error={errors.shares_count}>
                                        <input type="number" min="0" value={data.shares_count} onChange={(event) => setData('shares_count', event.target.value)} className="input" />
                                    </Field>
                                </div>

                                <button type="submit" disabled={processing} className="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50">
                                    {processing ? 'Saving...' : 'Schedule post'}
                                </button>
                            </form>
                        </div>
                    </section>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
