import FlashBanner from '@/Components/FlashBanner';
import FrontendLayout from '@/Layouts/FrontendLayout';
import { Head, Link, useForm } from '@inertiajs/react';

function formatMoney(amount, currency = 'BDT') {
    const numericAmount = Number(amount ?? 0);

    try {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: currency || 'BDT',
            maximumFractionDigits: 2,
        }).format(numericAmount);
    } catch {
        return `${currency || 'BDT'} ${numericAmount.toFixed(2)}`;
    }
}

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

export default function RfqRequest({ auth, flash, errors, product = null }) {
    const { data, setData, post, processing, reset } = useForm({
        contact_name: auth?.user?.name || '',
        company_name: '',
        email: auth?.user?.email || '',
        phone: '',
        business_type: '',
        product_id: product?.id || '',
        product_name: product?.name || '',
        quantity: product?.default_quantity || 1,
        target_price: '',
        needed_by: '',
        message: product ? `Please send a quotation for ${product.name} (${product.sku}).` : '',
    });

    const submit = (event) => {
        event.preventDefault();

        post(route('rfq.store'), {
            preserveScroll: true,
            onSuccess: () => reset('message', 'target_price'),
        });
    };

    const validationMessage = Object.values(errors || {}).find(Boolean);

    return (
        <FrontendLayout auth={auth} canLogin={true}>
            <Head title="Request a Quote" />

            <div className="bg-[radial-gradient(circle_at_top_left,_rgba(11,46,113,0.16),_transparent_28%),radial-gradient(circle_at_top_right,_rgba(255,138,0,0.16),_transparent_26%),linear-gradient(180deg,_#eef5ff_0%,_#f9fbff_46%,_#ffffff_100%)] text-slate-900">
                <main className="w-full px-4 py-10 sm:px-6 lg:px-8">
                    <section className="overflow-hidden rounded-[32px] border border-[#d7e3f4] bg-[#0b2e71] shadow-[0_26px_80px_-40px_rgba(7,18,46,0.9)]">
                        <div className="grid gap-8 px-5 py-6 lg:grid-cols-[1.08fr_.92fr] lg:px-8 lg:py-8">
                            <div className="space-y-5 text-white">
                                <span className="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-2 text-[11px] font-black uppercase tracking-[0.22em] text-[#ffd59a]">
                                    RFQ intake
                                </span>

                                <div className="space-y-3">
                                    <p className="text-sm font-semibold uppercase tracking-[0.24em] text-blue-200">
                                        Lead capture
                                    </p>
                                    <h1 className="max-w-2xl text-4xl font-black tracking-[-0.06em] sm:text-5xl">
                                        Request bulk pricing or a custom quotation from the marketplace team.
                                    </h1>
                                    <p className="max-w-2xl text-base leading-7 text-blue-100">
                                        Submit a public RFQ and the request becomes a lead record for CRM follow-up. Logged-in buyers also get a proper RFQ record and interaction history.
                                    </p>
                                </div>

                                <div className="grid gap-3 sm:grid-cols-3">
                                    {[
                                        ['Lead ready', 'Every request is stored in CRM.'],
                                        ['Buyer profile', 'Logged-in buyers stay linked to their account.'],
                                        ['Workflow trigger', 'RFQ events can drive automation rules.'],
                                    ].map(([title, copy]) => (
                                        <div key={title} className="rounded-2xl border border-white/12 bg-white/10 p-4 backdrop-blur">
                                            <p className="text-sm font-black uppercase tracking-[0.18em] text-[#ffd59a]">{title}</p>
                                            <p className="mt-2 text-sm leading-6 text-blue-50/90">{copy}</p>
                                        </div>
                                    ))}
                                </div>

                                {product ? (
                                    <div className="rounded-3xl border border-white/12 bg-white/10 p-5">
                                        <p className="text-sm font-semibold text-blue-50/90">Selected product</p>
                                        <div className="mt-3 flex gap-4">
                                            <img
                                                src={product.primary_image_url}
                                                alt={product.name}
                                                className="h-24 w-24 rounded-2xl object-cover"
                                            />
                                            <div className="min-w-0">
                                                <p className="text-[11px] font-black uppercase tracking-[0.18em] text-[#ffd59a]">
                                                    {product.supplier?.company_name || 'Marketplace supplier'}
                                                </p>
                                                <h2 className="mt-1 text-2xl font-black tracking-tight text-white">{product.name}</h2>
                                                <p className="mt-2 text-sm leading-6 text-blue-100/90">
                                                    SKU {product.sku} · MOQ {Number(product.moq || 1)} · Stock {Number(product.available_stock || 0)}
                                                </p>
                                                <p className="mt-2 text-lg font-black text-white">
                                                    {formatMoney(product.base_price)}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                ) : null}
                            </div>

                            <div className="rounded-[32px] border border-[#d7e3f4] bg-white p-6 shadow-[0_20px_70px_rgba(15,23,42,0.08)] sm:p-8">
                                <div>
                                    <h2 className="text-2xl font-black tracking-tight text-slate-950">Quotation request</h2>
                                    <p className="mt-2 text-sm leading-6 text-slate-600">
                                        Fill in the details below and the request will be routed to CRM as a lead.
                                    </p>
                                </div>

                                <FlashBanner message={flash?.success} className="mt-6" />
                                <FlashBanner message={flash?.error} type="error" className="mt-6" />
                                <FlashBanner message={validationMessage} type="error" className="mt-6" />

                                <form onSubmit={submit} className="mt-6 space-y-5">
                                    <div className="grid gap-5 sm:grid-cols-2">
                                        <Field label="Contact name" error={errors.contact_name}>
                                            <input
                                                value={data.contact_name}
                                                onChange={(event) => setData('contact_name', event.target.value)}
                                                className="input"
                                                placeholder="Ayesha Rahman"
                                                required
                                            />
                                        </Field>

                                        <Field label="Company name" error={errors.company_name}>
                                            <input
                                                value={data.company_name}
                                                onChange={(event) => setData('company_name', event.target.value)}
                                                className="input"
                                                placeholder="Plexus Industrial Supply"
                                                required
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
                                                placeholder="buyer@company.com"
                                                required
                                            />
                                        </Field>

                                        <Field label="Phone" error={errors.phone}>
                                            <input
                                                value={data.phone}
                                                onChange={(event) => setData('phone', event.target.value)}
                                                className="input"
                                                placeholder="+880 1XXXXXXXXX"
                                            />
                                        </Field>
                                    </div>

                                    <Field label="Business type" error={errors.business_type}>
                                        <input
                                            value={data.business_type}
                                            onChange={(event) => setData('business_type', event.target.value)}
                                            className="input"
                                            placeholder="Wholesale distributor, retailer, manufacturer..."
                                        />
                                    </Field>

                                    <div className="grid gap-5 sm:grid-cols-2">
                                        <Field label="Product name" error={errors.product_name}>
                                            <input
                                                value={data.product_name}
                                                onChange={(event) => setData('product_name', event.target.value)}
                                                className="input"
                                                placeholder="Product or request title"
                                                required
                                            />
                                        </Field>

                                        <Field label="Quantity" error={errors.quantity} hint="MOQ is handled separately by the sales team if needed.">
                                            <input
                                                type="number"
                                                min="1"
                                                value={data.quantity}
                                                onChange={(event) => setData('quantity', event.target.value)}
                                                className="input"
                                                required
                                            />
                                        </Field>
                                    </div>

                                    <div className="grid gap-5 sm:grid-cols-2">
                                        <Field label="Target price" error={errors.target_price} hint="Optional per-unit target price if you have one.">
                                            <input
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                value={data.target_price}
                                                onChange={(event) => setData('target_price', event.target.value)}
                                                className="input"
                                                placeholder="0.00"
                                            />
                                        </Field>

                                        <Field label="Needed by" error={errors.needed_by}>
                                            <input
                                                type="date"
                                                value={data.needed_by}
                                                onChange={(event) => setData('needed_by', event.target.value)}
                                                className="input"
                                            />
                                        </Field>
                                    </div>

                                    <Field label="Message" error={errors.message}>
                                        <textarea
                                            value={data.message}
                                            onChange={(event) => setData('message', event.target.value)}
                                            className="input min-h-[140px] resize-y"
                                            placeholder="Tell us quantity range, delivery timing, color/spec preference, and anything else the buyer team should know."
                                            required
                                        />
                                    </Field>

                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="inline-flex w-full items-center justify-center rounded-full bg-gradient-to-r from-slate-950 to-blue-700 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-700/20 transition hover:-translate-y-0.5 hover:shadow-blue-700/30 disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        {processing ? 'Submitting...' : 'Submit RFQ'}
                                    </button>

                                    <div className="text-center text-sm text-slate-500">
                                        <Link href={route('products.index')} className="font-bold text-blue-700 hover:text-blue-900">
                                            Back to marketplace
                                        </Link>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </main>
            </div>
        </FrontendLayout>
    );
}
