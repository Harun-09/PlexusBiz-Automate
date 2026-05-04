import { Head, Link } from '@inertiajs/react';
import FrontendLayout from '@/Layouts/FrontendLayout';
import FlashBanner from '@/Components/FlashBanner';

const fallbackImage = '/images/landing/deal-imac.jpg';

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

function CheckoutLineItem({ item, currency }) {
    const product = item.product || {};

    return (
        <div className="flex items-center gap-3 rounded-2xl bg-slate-50 p-3">
            <img
                src={product.primary_image_url || fallbackImage}
                alt={product.name}
                className="h-16 w-16 rounded-2xl object-cover"
                onError={(event) => {
                    event.currentTarget.src = fallbackImage;
                }}
            />
            <div className="min-w-0 flex-1">
                <p className="truncate text-sm font-black text-slate-950">{product.name}</p>
                <p className="text-xs text-slate-500">
                    {product.supplier?.company_name || 'PlexusBiz supplier'} · Qty {item.quantity}
                </p>
            </div>
            <div className="text-right">
                <p className="text-sm font-black text-slate-950">{formatMoney(item.line_total, currency)}</p>
                <p className="text-[11px] text-slate-500">{formatMoney(item.unit_price, currency)} each</p>
            </div>
        </div>
    );
}

function GatewayCard({ gateway, defaultGateway }) {
    const checked = gateway.key === defaultGateway;

    return (
        <label
            className={`flex cursor-pointer items-start gap-3 rounded-[24px] border p-4 transition ${
                checked ? 'border-[#ff8a00] bg-[#fff8ef] shadow-sm' : 'border-slate-200 bg-white hover:border-[#bfd0f0]'
            }`}
        >
            <input
                type="radio"
                name="gateway"
                value={gateway.key}
                defaultChecked={checked}
                className="mt-1 h-4 w-4 border-slate-300 text-[#0b2e71] focus:ring-[#0b2e71]"
                required
            />
            <div className="min-w-0 flex-1">
                <div className="flex items-center justify-between gap-3">
                    <p className="font-black text-slate-950">{gateway.label}</p>
                    <span
                        className={`rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] ${
                            gateway.accent === 'amber'
                                ? 'bg-[#fff0c9] text-[#9c5b00]'
                                : 'bg-[#e8f0ff] text-[#0b2e71]'
                        }`}
                    >
                        {gateway.accent}
                    </span>
                </div>
                <p className="mt-1 text-sm leading-6 text-slate-600">{gateway.description}</p>
            </div>
        </label>
    );
}

export default function Index({ auth, flash, errors, cart, buyer, csrfToken, currency, defaultGateway, gateways }) {
    const summary = cart?.summary || {};
    const items = Array.isArray(cart?.items) ? cart.items : [];
    const validationMessage = Object.values(errors || {}).find(Boolean);

    return (
        <FrontendLayout auth={auth} canLogin={true} cartCount={cart?.summary?.items_count || 0}>
            <Head title="Checkout" />

            <div className="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(11,46,113,0.14),_transparent_30%),radial-gradient(circle_at_top_right,_rgba(255,138,0,0.16),_transparent_26%),linear-gradient(180deg,_#eef5ff_0%,_#f9fbff_46%,_#ffffff_100%)] text-slate-900">
                <main className="mx-auto w-full max-w-[1500px] px-4 py-6 sm:px-6 lg:px-8">
                    <section className="overflow-hidden rounded-[32px] border border-[#d7e3f4] bg-[#0b2e71] shadow-[0_26px_80px_-40px_rgba(7,18,46,0.9)]">
                        <div className="grid gap-8 px-5 py-6 lg:grid-cols-[1.1fr_.9fr] lg:px-8 lg:py-8">
                            <div className="space-y-5 text-white">
                                <div className="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-[11px] font-black uppercase tracking-[0.22em] text-[#ffd59a]">
                                    Checkout
                                    <span className="rounded-full bg-white/15 px-2 py-0.5 text-white">
                                        {Number(summary.items_count || 0)} units
                                    </span>
                                </div>

                                <div className="space-y-3">
                                    <p className="text-sm font-semibold uppercase tracking-[0.24em] text-blue-200">
                                        Select a gateway and hand off to the existing payment flow
                                    </p>
                                    <h1 className="max-w-2xl text-4xl font-black tracking-[-0.06em] sm:text-5xl">
                                        A clean gateway step that keeps the cart context intact.
                                    </h1>
                                    <p className="max-w-2xl text-base leading-7 text-blue-100">
                                        The form below creates the order, records the payment intent, and then redirects to Stripe or SSLCOMMERZ exactly as the backend already expects.
                                    </p>
                                </div>

                                <div className="grid gap-3 sm:grid-cols-2">
                                    <div className="rounded-[24px] border border-white/10 bg-white/10 p-4 backdrop-blur">
                                        <p className="text-[11px] font-black uppercase tracking-[0.24em] text-blue-100">
                                            Buyer
                                        </p>
                                        <p className="mt-2 text-lg font-black">{buyer?.name || 'Buyer'}</p>
                                        <p className="text-sm text-blue-100">{buyer?.email || 'buyer@example.com'}</p>
                                    </div>

                                    <div className="rounded-[24px] border border-white/10 bg-white/10 p-4 backdrop-blur">
                                        <p className="text-[11px] font-black uppercase tracking-[0.24em] text-blue-100">
                                            Payment gateway
                                        </p>
                                        <p className="mt-2 text-lg font-black capitalize">{defaultGateway || 'stripe'}</p>
                                        <p className="text-sm text-blue-100">Default gateway from commerce settings.</p>
                                    </div>
                                </div>
                            </div>

                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="rounded-[28px] border border-white/10 bg-white/10 p-5 text-white backdrop-blur">
                                    <p className="text-[11px] font-black uppercase tracking-[0.24em] text-blue-100">
                                        Cart value
                                    </p>
                                    <p className="mt-3 text-4xl font-black tracking-[-0.06em]">
                                        {formatMoney(summary.grand_total || summary.subtotal || 0, currency)}
                                    </p>
                                    <p className="mt-2 text-sm leading-6 text-blue-100">
                                        The order total is pulled from the current active cart.
                                    </p>
                                </div>

                                <div className="rounded-[28px] border border-white/10 bg-white p-5 shadow-[0_16px_42px_-28px_rgba(15,23,42,0.8)]">
                                    <p className="text-[11px] font-black uppercase tracking-[0.24em] text-[#0b2e71]">
                                        Checkout ready
                                    </p>
                                    <p className="mt-3 text-sm leading-6 text-slate-600">
                                        Clicking the button below submits a standard form so the external gateway redirect can complete normally.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <FlashBanner message={flash?.success} className="mt-5" />
                    <FlashBanner message={flash?.error} type="error" className="mt-5" />
                    <FlashBanner message={validationMessage} type="error" className="mt-5" />

                    {items.length > 0 ? (
                        <section className="mt-6 grid gap-6 lg:grid-cols-[1fr_380px]">
                            <form
                                method="post"
                                action={route('checkout.process')}
                                className="space-y-6"
                            >
                                <input type="hidden" name="_token" value={csrfToken} />

                                <div className="rounded-[30px] border border-[#d7e3f4] bg-white p-5 shadow-sm">
                                    <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-500">
                                        Payment method
                                    </p>
                                    <h2 className="mt-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                                        Choose where the order should go
                                    </h2>
                                    <div className="mt-5 grid gap-3">
                                        {gateways.map((gateway) => (
                                            <GatewayCard
                                                key={gateway.key}
                                                gateway={gateway}
                                                defaultGateway={defaultGateway}
                                            />
                                        ))}
                                    </div>
                                </div>

                                <div className="rounded-[30px] border border-[#d7e3f4] bg-white p-5 shadow-sm">
                                    <div className="flex items-center justify-between gap-4">
                                        <div>
                                            <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-500">
                                                Review
                                            </p>
                                            <h2 className="mt-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                                                Cart items included in this order
                                            </h2>
                                        </div>
                                        <Link href={route('cart.index')} className="text-sm font-semibold text-[#0b2e71] hover:text-[#d75d00]">
                                            Back to cart
                                        </Link>
                                    </div>

                                    <div className="mt-5 space-y-3">
                                        {items.map((item) => (
                                            <CheckoutLineItem key={item.id} item={item} currency={currency} />
                                        ))}
                                    </div>
                                </div>

                                <button
                                    type="submit"
                                    className="inline-flex w-full items-center justify-center rounded-full bg-[#ff8a00] px-5 py-4 text-sm font-black uppercase tracking-[0.14em] text-white transition hover:bg-[#ef7400]"
                                >
                                    Place order and continue
                                </button>
                            </form>

                            <aside className="space-y-4">
                                <div className="sticky top-24 rounded-[32px] border border-[#d7e3f4] bg-white p-5 shadow-[0_18px_60px_-38px_rgba(15,23,42,0.85)]">
                                    <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">
                                        Order summary
                                    </p>

                                    <dl className="mt-4 space-y-3 text-sm">
                                        <div className="flex items-center justify-between gap-4 rounded-2xl bg-slate-50 px-4 py-3">
                                            <dt className="text-slate-500">Subtotal</dt>
                                            <dd className="font-black text-slate-950">
                                                {formatMoney(summary.subtotal || 0, currency)}
                                            </dd>
                                        </div>
                                        <div className="flex items-center justify-between gap-4 rounded-2xl bg-slate-50 px-4 py-3">
                                            <dt className="text-slate-500">Shipping</dt>
                                            <dd className="font-black text-slate-950">
                                                {formatMoney(summary.shipping_total || 0, currency)}
                                            </dd>
                                        </div>
                                        <div className="flex items-center justify-between gap-4 rounded-2xl bg-slate-50 px-4 py-3">
                                            <dt className="text-slate-500">Tax</dt>
                                            <dd className="font-black text-slate-950">
                                                {formatMoney(summary.tax_total || 0, currency)}
                                            </dd>
                                        </div>
                                        <div className="flex items-center justify-between gap-4 rounded-2xl bg-slate-50 px-4 py-3">
                                            <dt className="text-slate-500">Discount</dt>
                                            <dd className="font-black text-slate-950">
                                                {formatMoney(summary.discount_total || 0, currency)}
                                            </dd>
                                        </div>
                                    </dl>

                                    <div className="mt-5 rounded-[24px] bg-[#0b2e71] p-4 text-white">
                                        <p className="text-[11px] font-black uppercase tracking-[0.24em] text-blue-100">
                                            Grand total
                                        </p>
                                        <p className="mt-2 text-4xl font-black tracking-[-0.06em]">
                                            {formatMoney(summary.grand_total || 0, currency)}
                                        </p>
                                        <p className="mt-2 text-sm leading-6 text-blue-100">
                                            The next redirect is handled by the payment controller, so the external gateway opens directly after this submit.
                                        </p>
                                    </div>

                                    <div className="mt-5 rounded-[24px] border border-[#e8eef8] bg-[#f8fbff] p-4 text-sm leading-6 text-slate-600">
                                        <p className="font-bold text-slate-900">Why a plain form</p>
                                        <p className="mt-2">
                                            The gateway redirect is external, so this step stays outside of the XHR flow and behaves like a normal checkout.
                                        </p>
                                    </div>
                                </div>
                            </aside>
                        </section>
                    ) : (
                        <section className="mt-6 overflow-hidden rounded-[32px] border border-[#d7e3f4] bg-white p-8 shadow-sm">
                            <div className="grid gap-8 lg:grid-cols-[1fr_.8fr] lg:items-center">
                                <div className="space-y-4">
                                    <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-500">
                                        Empty cart
                                    </p>
                                    <h2 className="text-3xl font-black tracking-[-0.05em] text-slate-950">
                                        Nothing to check out yet.
                                    </h2>
                                    <p className="max-w-xl text-base leading-7 text-slate-600">
                                        Go back to the product catalog, add items to the cart, and return here when the order is ready.
                                    </p>
                                    <Link
                                        href={route('products.index')}
                                        className="inline-flex rounded-full bg-[#0b2e71] px-5 py-3 text-sm font-black uppercase tracking-[0.14em] text-white transition hover:bg-[#09255b]"
                                    >
                                        Browse products
                                    </Link>
                                </div>

                                <div className="rounded-[28px] bg-[linear-gradient(145deg,_#0b2e71,_#103a87,_#ff8a00)] p-6 text-white shadow-[0_16px_60px_-30px_rgba(15,23,42,0.8)]">
                                    <p className="text-[11px] font-black uppercase tracking-[0.24em] text-blue-100">
                                        Next step
                                    </p>
                                    <p className="mt-4 text-2xl font-black tracking-[-0.05em]">
                                        Once the cart has items, this checkout screen will hand off to Stripe or SSLCOMMERZ in one click.
                                    </p>
                                </div>
                            </div>
                        </section>
                    )}
                </main>
            </div>
        </FrontendLayout>
    );
}
