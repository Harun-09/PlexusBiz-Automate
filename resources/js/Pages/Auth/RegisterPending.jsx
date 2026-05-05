import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link } from '@inertiajs/react';

const accountLabels = {
    buyer: 'Buyer',
    supplier: 'Supplier',
    marketing_manager: 'Marketing Manager',
    workflow_manager: 'Workflow Manager',
};

export default function RegisterPending({ accountType = '', accountTypes = [] }) {
    const label =
        accountLabels[String(accountType || '').toLowerCase()] ||
        accountTypes.find((option) => option.value === accountType)?.label ||
        'requested account';

    return (
        <GuestLayout
            variant="register"
            registerHero={{
                eyebrow: 'Application submitted',
                title: 'Your account request is under review.',
                lead: 'Admin approval is required before the account becomes active and can sign in.',
            }}
        >
            <Head title="Application submitted" />

            <div className="auth-form auth-form--register">
                <div className="auth-card-brand auth-card-brand--visible">
                    <span className="auth-card-brand__mark">
                        <img
                            src="/images/project-logo.png"
                            alt="PlexusBiz Automate"
                            className="auth-card-brand__logo"
                        />
                    </span>
                    <span className="auth-card-brand__copy">
                        <strong>PlexusBiz Automate</strong>
                        <span>Application queue</span>
                    </span>
                </div>

                <div className="auth-form__header auth-form__header--register">
                    <span className="auth-eyebrow">Pending approval</span>
                    <h2>We received your {label.toLowerCase()} request.</h2>
                </div>

                <div className="space-y-4">
                    <div className="rounded-2xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm leading-6 text-blue-900">
                        An admin will review the submitted details, approve or reject the application, and activate the account only after review.
                    </div>

                    <div className="grid gap-3 sm:grid-cols-2">
                        <div className="rounded-2xl border border-slate-200 bg-white px-4 py-4">
                            <p className="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Requested type</p>
                            <p className="mt-2 text-base font-bold text-slate-950">{label}</p>
                        </div>
                        <div className="rounded-2xl border border-slate-200 bg-white px-4 py-4">
                            <p className="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Next step</p>
                            <p className="mt-2 text-base font-bold text-slate-950">Wait for admin review</p>
                        </div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm leading-6 text-slate-600">
                        You will receive access after the admin approves the submission. Use the same email address to sign in once the account becomes active.
                    </div>

                    <div className="flex flex-col gap-3 sm:flex-row">
                        <Link
                            href="/login"
                            className="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-slate-950 to-blue-700 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-700/20 transition hover:-translate-y-0.5 hover:shadow-blue-700/30"
                        >
                            Go to login
                        </Link>
                        <Link
                            href="/"
                            className="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50"
                        >
                            Back to homepage
                        </Link>
                    </div>
                </div>
            </div>
        </GuestLayout>
    );
}
