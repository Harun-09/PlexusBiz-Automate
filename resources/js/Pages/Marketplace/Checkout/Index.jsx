import { Head, Link, router } from '@inertiajs/react';
import { useMemo, useState } from 'react';
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
        <div className="flex items-center gap-3 rounded-[22px] border border-slate-200 bg-slate-50 p-3.5">
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
                    {product.supplier?.company_name || 'PlexusBiz supplier'} - Qty {item.quantity}
                </p>
            </div>
            <div className="text-right">
                <p className="text-sm font-black text-slate-950">{formatMoney(item.line_total, currency)}</p>
                <p className="text-[11px] text-slate-500">{formatMoney(item.unit_price, currency)} each</p>
            </div>
        </div>
    );
}

function GatewayCard({ gateway, selectedGateway, onSelect }) {
    const checked = gateway.key === selectedGateway;

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
                checked={checked}
                onChange={() => onSelect(gateway.key)}
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

function SummaryRow({ label, value, emphasized = false }) {
    return (
        <div className={`flex items-center justify-between gap-4 rounded-2xl px-4 py-3 ${emphasized ? 'bg-gradient-to-r from-[#4f7fe0] via-[#3f70d4] to-[#2953b1] text-white' : 'bg-slate-50 text-slate-700'}`}>
            <span className={emphasized ? 'text-blue-100' : 'text-slate-500'}>{label}</span>
            <span className={`font-black ${emphasized ? 'text-white' : 'text-slate-950'}`}>{value}</span>
        </div>
    );
}

export default function Index({ auth, flash, errors, cart, buyer, isB2C, csrfToken, currency, defaultGateway, gateways }) {
    const summary = cart?.summary || {};
    const items = Array.isArray(cart?.items) ? cart.items : [];
    const validationMessage = Object.values(errors || {}).find(Boolean);
    const [selectedGateway, setSelectedGateway] = useState(defaultGateway || 'stripe');
    const [paymentTerm, setPaymentTerm] = useState('cash');
    const [shippingMethod, setShippingMethod] = useState(cart?.shipping_method || 'weight_based');

    const selectedGatewayInfo = useMemo(
        () => gateways.find((gateway) => gateway.key === selectedGateway) || gateways[0] || null,
        [gateways, selectedGateway],
    );

    const handleShippingChange = (method) => {
        setShippingMethod(method);
        router.post(route('cart.update'), { shipping_method: method }, { preserveScroll: true, preserveState: true });
    };

    return (
        <FrontendLayout auth={auth} canLogin={true} cartCount={cart?.summary?.items_count || 0}>
            <Head title="Checkout" />

            <div className="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(11,46,113,0.14),_transparent_30%),radial-gradient(circle_at_top_right,_rgba(255,138,0,0.16),_transparent_26%),linear-gradient(180deg,_#eef5ff_0%,_#f9fbff_46%,_#ffffff_100%)] text-slate-900">
                <main className="w-full px-4 py-6 sm:px-6 lg:px-8">
                    <section className="overflow-hidden rounded-[32px] border border-[#d7e3f4] bg-gradient-to-r from-[#4f7fe0] via-[#3f70d4] to-[#2953b1] shadow-[0_26px_80px_-40px_rgba(7,18,46,0.9)]">
                        <div className="grid gap-8 px-5 py-6 lg:grid-cols-[1.08fr_.92fr] lg:px-8 lg:py-8">
                            <div className="space-y-5 text-white">
                                <div className="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-[11px] font-black uppercase tracking-[0.22em] text-[#ffd59a]">
                                    Checkout
                                    <span className="rounded-full bg-white/15 px-2 py-0.5 text-white">
                                        {Number(summary.items_count || 0)} units
                                    </span>
                                </div>

                                <div className="space-y-3">
                                    <p className="text-sm font-semibold uppercase tracking-[0.24em] text-blue-200">
                                        Review the cart and choose a payment gateway
                                    </p>
                                    <h1 className="max-w-2xl text-4xl font-black tracking-[-0.06em] sm:text-5xl">
                                        A cleaner checkout that stays aligned with the existing gateway flow.
                                    </h1>
                                    <p className="max-w-2xl text-base leading-7 text-blue-100">
                                        The order is created from the cart, the buyer-facing order is confirmed, and the selected payment gateway takes over the handoff exactly as the backend expects.
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
                                            Gateway
                                        </p>
                                        <p className="mt-2 text-lg font-black">{selectedGatewayInfo?.label || 'Stripe'}</p>
                                        <p className="text-sm text-blue-100">
                                            {selectedGatewayInfo?.description || 'Default gateway from commerce settings.'}
                                        </p>
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
                                        The total is calculated from the current cart and MOQ-aware pricing.
                                    </p>
                                </div>

                                <div className="rounded-[28px] border border-white/10 bg-white p-5 shadow-[0_16px_42px_-28px_rgba(15,23,42,0.8)]">
                                    <p className="text-[11px] font-black uppercase tracking-[0.24em] text-[#0b2e71]">
                                        Checkout ready
                                    </p>
                                    <p className="mt-3 text-sm leading-6 text-slate-600">
                                        Submit the form to continue into Stripe or SSLCOMMERZ. No extra step is needed for the buyer dashboard flow.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <FlashBanner message={flash?.success} className="mt-5" />
                    <FlashBanner message={flash?.error} type="error" className="mt-5" />
                    <FlashBanner message={validationMessage} type="error" className="mt-5" />

                    {items.length > 0 ? (
                        <section className="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_390px]">
                            <form method="post" action={route('checkout.process')} className="space-y-6">
                                <input type="hidden" name="_token" value={csrfToken} />
                                <input type="hidden" name="shipping_method" value={isB2C ? 'standard' : shippingMethod} />

                                <div className="rounded-[30px] border border-[#d7e3f4] bg-white p-5 shadow-sm">
                                    <div className="flex items-start justify-between gap-4">
                                        <div>
                                            <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-500">
                                                Shipping Method
                                            </p>
                                            <h2 className="mt-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                                                How would you like to receive your order?
                                            </h2>
                                        </div>
                                    </div>

                                    {isB2C ? (
                                        <div className="mt-5 rounded-[24px] border border-[#0b2e71] bg-[#e8f0ff] p-4 text-[#0b2e71] shadow-sm">
                                            <p className="font-black">Standard Shipping</p>
                                            <p className="mt-1 text-sm text-[#0b2e71]/80">Fixed shipping rate for retail orders. {formatMoney(5.00, currency)}</p>
                                        </div>
                                    ) : (
                                        <div className="mt-5 grid gap-3 sm:grid-cols-2">
                                            <label
                                                className={`flex cursor-pointer flex-col items-start gap-2 rounded-[24px] border p-4 transition ${
                                                    shippingMethod === 'weight_based' ? 'border-[#0b2e71] bg-[#e8f0ff] shadow-sm text-[#0b2e71]' : 'border-slate-200 bg-white hover:border-[#bfd0f0]'
                                                }`}
                                            >
                                                <input
                                                    type="radio"
                                                    name="b2b_shipping"
                                                    value="weight_based"
                                                    checked={shippingMethod === 'weight_based'}
                                                    onChange={(e) => handleShippingChange(e.target.value)}
                                                    className="sr-only"
                                                />
                                                <span className="font-black">Weight-based Shipping</span>
                                                <span className="text-xs text-slate-500">
                                                    $2.00 per kg (Total Weight: {summary.total_weight || 0} kg)
                                                </span>
                                            </label>
                                            <label
                                                className={`flex cursor-pointer flex-col items-start gap-2 rounded-[24px] border p-4 transition ${
                                                    shippingMethod === 'own_logistics' ? 'border-[#0b2e71] bg-[#e8f0ff] shadow-sm text-[#0b2e71]' : 'border-slate-200 bg-white hover:border-[#bfd0f0]'
                                                }`}
                                            >
                                                <input
                                                    type="radio"
                                                    name="b2b_shipping"
                                                    value="own_logistics"
                                                    checked={shippingMethod === 'own_logistics'}
                                                    onChange={(e) => handleShippingChange(e.target.value)}
                                                    className="sr-only"
                                                />
                                                <span className="font-black">Own Logistics</span>
                                                <span className="text-xs text-slate-500">Pick up or arrange your own shipping. Free.</span>
                                            </label>
                                        </div>
                                    )}
                                </div>

                                <div className="rounded-[30px] border border-[#d7e3f4] bg-white p-5 shadow-sm">
                                    <div className="flex items-start justify-between gap-4">
                                        <div>
                                            <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-500">
                                                Terms & Payment
                                            </p>
                                            <h2 className="mt-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                                                Select Payment Term
                                            </h2>
                                        </div>
                                    </div>

                                    <div className="mt-5 grid gap-3 sm:grid-cols-3">
                                        {['cash', 'net30', 'net60'].map((term) => (
                                            <label
                                                key={term}
                                                className={`flex cursor-pointer flex-col items-center gap-2 rounded-[24px] border p-4 text-center transition ${
                                                    paymentTerm === term ? 'border-[#0b2e71] bg-[#e8f0ff] shadow-sm text-[#0b2e71]' : 'border-slate-200 bg-white hover:border-[#bfd0f0]'
                                                }`}
                                            >
                                                <input
                                                    type="radio"
                                                    name="payment_term"
                                                    value={term}
                                                    checked={paymentTerm === term}
                                                    onChange={(e) => setPaymentTerm(e.target.value)}
                                                    className="sr-only"
                                                    required
                                                />
                                                <span className="font-black capitalize">{term.replace('net', 'Net ')}</span>
                                                {term !== 'cash' && (
                                                    <span className="text-[10px] text-slate-500">Requires Credit Line</span>
                                                )}
                                            </label>
                                        ))}
                                    </div>
                                </div>

                                {paymentTerm === 'cash' && (
                                    <div className="rounded-[30px] border border-[#d7e3f4] bg-white p-5 shadow-sm">
                                        <div className="flex items-start justify-between gap-4">
                                            <div>
                                                <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-500">
                                                    Payment Gateway
                                                </p>
                                                <h2 className="mt-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                                                    Choose where the order should go
                                                </h2>
                                            </div>
                                            <span className="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-bold text-slate-600">
                                                {gateways.length} options
                                            </span>
                                        </div>

                                        <div className="mt-5 grid gap-3">
                                            {gateways.map((gateway) => (
                                                <GatewayCard
                                                    key={gateway.key}
                                                    gateway={gateway}
                                                    selectedGateway={selectedGateway}
                                                    onSelect={setSelectedGateway}
                                                />
                                            ))}
                                        </div>
                                    </div>
                                )}

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
                                        <SummaryRow label="Subtotal" value={formatMoney(summary.subtotal || 0, currency)} />
                                        <SummaryRow label="Shipping" value={formatMoney(summary.shipping_total || 0, currency)} />
                                        <SummaryRow label="Tax" value={formatMoney(summary.tax_total || 0, currency)} />
                                        <SummaryRow label="Discount" value={formatMoney(summary.discount_total || 0, currency)} />
                                    </dl>

                                    <div className="mt-5 rounded-[24px] bg-gradient-to-r from-[#4f7fe0] via-[#3f70d4] to-[#2953b1] p-4 text-white">
                                        <p className="text-[11px] font-black uppercase tracking-[0.24em] text-blue-100">
                                            Grand total
                                        </p>
                                        <p className="mt-2 text-4xl font-black tracking-[-0.06em]">
                                            {formatMoney(summary.grand_total || 0, currency)}
                                        </p>
                                        <p className="mt-2 text-sm leading-6 text-blue-100">
                                            After submit, the payment controller redirects to the selected gateway and the checkout status stays synchronized.
                                        </p>
                                    </div>

                                    <div className="mt-5 rounded-[24px] border border-[#e8eef8] bg-[#f8fbff] p-4 text-sm leading-6 text-slate-600">
                                        <p className="font-bold text-slate-900">What happens next</p>
                                        <p className="mt-2">
                                            Buyer orders are confirmed at checkout, invoice creation happens automatically, and supplier fulfillment rows are generated behind the scenes.
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
                                        className="inline-flex rounded-full bg-gradient-to-r from-[#4f7fe0] via-[#3f70d4] to-[#2953b1] px-5 py-3 text-sm font-black uppercase tracking-[0.14em] text-white transition hover:brightness-105"
                                    >
                                        Browse products
                                    </Link>
                                </div>

                                <div className="rounded-[28px] bg-gradient-to-r from-[#4f7fe0] via-[#3f70d4] to-[#2953b1] p-6 text-white shadow-[0_16px_60px_-30px_rgba(15,23,42,0.8)]">
                                    <p className="text-[11px] font-black uppercase tracking-[0.24em] text-blue-100">
                                        Next step
                                    </p>
                                    <p className="mt-4 text-2xl font-black tracking-[-0.05em]">
                                        Once the cart has items, the checkout screen will hand off to the selected gateway in one click.
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
