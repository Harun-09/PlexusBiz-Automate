import FlashBanner from '@/Components/FlashBanner';
import PageHeader from '@/Components/PageHeader';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { actionButtonClasses } from '@/Utils/pillStyles';
import { Head, Link, useForm } from '@inertiajs/react';

const countryOptions = ['Bangladesh', 'India', 'Singapore', 'Malaysia', 'United Arab Emirates', 'United States'];

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

const joinTags = (tags) => (Array.isArray(tags) ? tags.join(', ') : '');

export default function Edit({ auth, flash, errors, customer, summary, statuses = [], stages = [], countries = countryOptions }) {
    const { data, setData, transform, put, processing } = useForm({
        contact_name: customer.contact_name || '',
        company_name: customer.company_name || '',
        email: customer.email || '',
        phone: customer.phone || '',
        business_type: customer.business_type || '',
        status: customer.status || statuses[0] || 'active',
        lifecycle_stage: customer.lifecycle_stage || stages[0] || 'customer',
        tags: joinTags(customer.tags),
        notes: customer.notes || '',
        address_line1: customer.address?.line_1 || '',
        address_line2: customer.address?.line_2 || '',
        city: customer.address?.city || '',
        state: customer.address?.state || '',
        postal_code: customer.address?.postal_code || '',
        country: customer.address?.country || countries[0] || 'Bangladesh',
    });

    const submit = (event) => {
        event.preventDefault();

        transform((payload) => ({
            ...payload,
            tags: payload.tags,
        })).put(route('crm.customers.update', customer.id), {
            preserveScroll: true,
        });
    };

    const validationMessage = Object.values(errors || {}).find(Boolean);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <PageHeader
                    eyebrow="CRM"
                    title={`Edit customer: ${customer.contact_name}`}
                    description="Fill in missing buyer profile data or correct the CRM record from the admin console."
                    actions={
                        <>
                            <Link
                                href={route('crm.customers.show', customer.id)}
                                className={`inline-flex items-center justify-center rounded-full border px-4 py-2 text-sm font-bold transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('neutral')}`}
                            >
                                Back to profile
                            </Link>
                            <Link
                                href={route('crm.interactions.index')}
                                className="inline-flex items-center justify-center rounded-full bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800"
                            >
                                View interactions
                            </Link>
                        </>
                    }
                />
            }
        >
            <Head title={`Edit Customer - ${customer.contact_name}`} />

            <div className="py-8">
                <div className="space-y-6">
                    <section className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <div className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p className="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">Orders</p>
                            <p className="mt-2 text-2xl font-black tracking-tight text-slate-950">{summary.orders_count}</p>
                        </div>
                        <div className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p className="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">Total spent</p>
                            <p className="mt-2 text-2xl font-black tracking-tight text-slate-950">{summary.total_spent}</p>
                        </div>
                        <div className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p className="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">Last order</p>
                            <p className="mt-2 text-lg font-black tracking-tight text-slate-950">{summary.last_order_at}</p>
                        </div>
                        <div className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p className="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">Last activity</p>
                            <p className="mt-2 text-lg font-black tracking-tight text-slate-950">{formatDate(customer.last_activity_at)}</p>
                        </div>
                    </section>

                    <section className="grid gap-6 xl:grid-cols-[1.05fr_.95fr]">
                        <div className="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                            <div className="border-b border-slate-100 bg-slate-50 px-5 py-5 sm:px-8">
                                <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Customer editor</p>
                                <h2 className="mt-1 text-2xl font-black tracking-tight text-slate-950">Update buyer profile data</h2>
                                <p className="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                                    Use this form to backfill missing contact details, business data, tags, notes, and address fields.
                                </p>
                            </div>

                            <div className="px-5 py-6 sm:px-8">
                                <FlashBanner message={flash?.success} />
                                <FlashBanner message={flash?.error} type="error" className="mt-4" />
                                <FlashBanner message={validationMessage} type="error" className="mt-4" />

                                <form onSubmit={submit} className="mt-6 space-y-5">
                                    <div className="grid gap-5 sm:grid-cols-2">
                                        <Field label="Contact Name" error={errors.contact_name}>
                                            <input
                                                value={data.contact_name}
                                                onChange={(event) => setData('contact_name', event.target.value)}
                                                className="input"
                                                required
                                            />
                                        </Field>

                                        <Field label="Company Name" error={errors.company_name}>
                                            <input
                                                value={data.company_name}
                                                onChange={(event) => setData('company_name', event.target.value)}
                                                className="input"
                                            />
                                        </Field>
                                    </div>

                                    <div className="grid gap-5 sm:grid-cols-2">
                                        <Field label="Email" error={errors.email}>
                                            <input
                                                type="email"
                                                value={data.email}
                                                onChange={(event) => setData('email', event.target.value)}
                                                className="input"
                                                required
                                            />
                                        </Field>

                                        <Field label="Phone" error={errors.phone}>
                                            <input
                                                value={data.phone}
                                                onChange={(event) => setData('phone', event.target.value)}
                                                className="input"
                                            />
                                        </Field>
                                    </div>

                                    <div className="grid gap-5 sm:grid-cols-2">
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

                                        <Field label="Lifecycle Stage" error={errors.lifecycle_stage}>
                                            <select
                                                value={data.lifecycle_stage}
                                                onChange={(event) => setData('lifecycle_stage', event.target.value)}
                                                className="input"
                                            >
                                                {stages.map((stage) => (
                                                    <option key={stage} value={stage}>
                                                        {stage.replace(/_/g, ' ')}
                                                    </option>
                                                ))}
                                            </select>
                                        </Field>
                                    </div>

                                    <div className="grid gap-5 sm:grid-cols-2">
                                        <Field label="Business Type" error={errors.business_type}>
                                            <input
                                                value={data.business_type}
                                                onChange={(event) => setData('business_type', event.target.value)}
                                                className="input"
                                                placeholder="Wholesale distributor, retailer, manufacturer..."
                                            />
                                        </Field>

                                        <Field label="Tags" error={errors.tags} hint="Comma-separated tags for CRM segmentation.">
                                            <input
                                                value={data.tags}
                                                onChange={(event) => setData('tags', event.target.value)}
                                                className="input"
                                                placeholder="priority, wholesale, key-account"
                                            />
                                        </Field>
                                    </div>

                                    <div className="grid gap-5 sm:grid-cols-2">
                                        <Field label="Address line 1" error={errors.address_line1}>
                                            <input
                                                value={data.address_line1}
                                                onChange={(event) => setData('address_line1', event.target.value)}
                                                className="input"
                                            />
                                        </Field>

                                        <Field label="Address line 2" error={errors.address_line2}>
                                            <input
                                                value={data.address_line2}
                                                onChange={(event) => setData('address_line2', event.target.value)}
                                                className="input"
                                            />
                                        </Field>
                                    </div>

                                    <div className="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                                        <Field label="City" error={errors.city}>
                                            <input
                                                value={data.city}
                                                onChange={(event) => setData('city', event.target.value)}
                                                className="input"
                                            />
                                        </Field>

                                        <Field label="State / Region" error={errors.state}>
                                            <input
                                                value={data.state}
                                                onChange={(event) => setData('state', event.target.value)}
                                                className="input"
                                            />
                                        </Field>

                                        <Field label="Postal Code" error={errors.postal_code}>
                                            <input
                                                value={data.postal_code}
                                                onChange={(event) => setData('postal_code', event.target.value)}
                                                className="input"
                                            />
                                        </Field>

                                        <Field label="Country" error={errors.country}>
                                            <select
                                                value={data.country}
                                                onChange={(event) => setData('country', event.target.value)}
                                                className="input"
                                            >
                                                {countries.map((country) => (
                                                    <option key={country} value={country}>
                                                        {country}
                                                    </option>
                                                ))}
                                            </select>
                                        </Field>
                                    </div>

                                    <Field label="Notes" error={errors.notes} hint="Internal CRM notes and context.">
                                        <textarea
                                            value={data.notes}
                                            onChange={(event) => setData('notes', event.target.value)}
                                            className="input min-h-[160px] resize-y"
                                            placeholder="Missing phone number, procurement contact, preferred shipping window..."
                                        />
                                    </Field>

                                    <div className="flex flex-wrap items-center gap-3">
                                        <button
                                            type="submit"
                                            disabled={processing}
                                            className="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            {processing ? 'Saving...' : 'Save customer'}
                                        </button>

                                        <Link
                                            href={route('crm.customers.show', customer.id)}
                                            className="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:border-blue-200 hover:text-blue-800"
                                        >
                                            Cancel
                                        </Link>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div className="space-y-6">
                            <section className="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                                <div className="border-b border-slate-100 px-5 py-4">
                                    <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Current record</p>
                                    <h3 className="mt-1 text-lg font-black tracking-tight text-slate-950">Customer snapshot</h3>
                                </div>
                                <div className="px-5 py-5">
                                    <dl className="rounded-2xl border border-slate-200 px-4">
                                        <DetailRow label="Contact" value={customer.contact_name} />
                                        <DetailRow label="Company" value={customer.company_name || 'n/a'} />
                                        <DetailRow label="Email" value={customer.email} />
                                        <DetailRow label="Phone" value={customer.phone || 'n/a'} />
                                        <DetailRow label="Business type" value={customer.business_type || 'n/a'} />
                                        <DetailRow label="Status" value={customer.status} />
                                        <DetailRow label="Lifecycle" value={customer.lifecycle_stage} />
                                        <DetailRow label="Linked user" value={customer.user ? `${customer.user.name} (${customer.user.email})` : 'n/a'} />
                                        <DetailRow label="Address" value={formatAddress(customer.address)} />
                                        <DetailRow label="Notes" value={customer.notes || 'n/a'} />
                                    </dl>
                                </div>
                            </section>

                            <section className="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                                <div className="border-b border-slate-100 px-5 py-4">
                                    <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Guidance</p>
                                    <h3 className="mt-1 text-lg font-black tracking-tight text-slate-950">How this is used</h3>
                                </div>
                                <div className="space-y-3 px-5 py-5 text-sm leading-6 text-slate-600">
                                    <p>
                                        Buyer self-edit is already available from <code>/profile</code>. This CRM form is the admin fallback for missing or incorrect customer data.
                                    </p>
                                    <p>
                                        If the customer is linked to a user account, changing the email here also keeps the user email in sync.
                                    </p>
                                    <p>
                                        Core CRM fields here feed customer lists, interaction history, segmentation, and order context.
                                    </p>
                                </div>
                            </section>
                        </div>
                    </section>
                </div>
            </div>
        </AuthenticatedLayout>
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
