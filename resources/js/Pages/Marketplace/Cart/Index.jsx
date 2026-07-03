import { Head, Link, router } from '@inertiajs/react';
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

function LineItem({ item, currency }) {
    const product = item.product || {};
    const currentQuantity = Number(item.quantity || 1);
    const moq = Number(product.moq || 1);
    const availableStock = Number(product.available_stock || 0);
    const maxQuantity = Math.max(moq, availableStock);
    const canIncrease = availableStock === 0 ? false : currentQuantity < maxQuantity;
    const canDecrease = currentQuantity > moq;
    const tierActive = Number(item.unit_price || 0) < Number(product.base_price || item.unit_price || 0);

    const updateQuantity = (nextQuantity) => {
        router.post(
            route('cart.update'),
            {
                item_id: item.id,
                quantity: nextQuantity,
            },
            { preserveScroll: true },
        );
    };

    return (
        <article className="overflow-hidden rounded-[28px] border border-[#d7e3f4] bg-white shadow-[0_12px_34px_-24px_rgba(15,23,42,0.55)]">
            <div className="grid gap-0 md:grid-cols-[170px_1fr]">
                <Link href={route('products.show', product.slug)} className="block bg-[#f2f6ff]">
                    <img
                        src={product.primary_image_url || fallbackImage}
                        alt={product.name}
                        className="h-full w-full object-cover md:h-full"
                        onError={(event) => {
                            event.currentTarget.src = fallbackImage;
                        }}
                    />
                </Link>

                <div className="space-y-4 p-5">
                    <div className="flex flex-wrap items-start justify-between gap-3">
                        <div className="space-y-1">
                            <p className="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">
                                {product.supplier?.company_name || 'PlexusBiz supplier'}
                            </p>
                            <h3 className="text-xl font-black tracking-[-0.04em] text-slate-950">
                                <Link href={route('products.show', product.slug)} className="transition hover:text-[#0b2e71]">
                                    {product.name}
                                </Link>
                            </h3>
                            <p className="text-sm text-slate-500">
                                SKU {product.sku || 'N/A'} · MOQ {moq} · Stock {availableStock}
                            </p>
                        </div>
                        <div className="text-right">
                            <p className="text-xs font-black uppercase tracking-[0.18em] text-slate-400">
                                Unit price
                            </p>
                            <p className="text-2xl font-black tracking-[-0.04em] text-[#0b2e71]">
                                {formatMoney(item.unit_price, currency)}
                            </p>
                            {tierActive && (
                                <p className="text-xs font-semibold text-[#d75d00]">Tier price applied</p>
                            )}
                        </div>
                    </div>

                    <div className="flex flex-wrap items-center justify-between gap-4">
                        <div className="rounded-2xl bg-slate-50 px-4 py-3">
                            <p className="text-[10px] font-black uppercase tracking-[0.18em] text-slate-400">
                                Quantity
                            </p>
                            <div className="mt-2 flex items-center gap-2">
                                <button
                                    type="button"
                                    disabled={!canDecrease}
                                    onClick={() => updateQuantity(currentQuantity - 1)}
                                    className="grid h-10 w-10 place-items-center rounded-full border border-slate-200 bg-white text-lg font-black text-slate-700 transition hover:border-[#ffb16d] hover:text-[#d75d00] disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-300"
                                >
                                    -
                                </button>
                                <span className="min-w-14 text-center text-lg font-black text-slate-950">
                                    {currentQuantity}
                                </span>
                                <button
                                    type="button"
                                    disabled={!canIncrease}
                                    onClick={() => updateQuantity(currentQuantity + 1)}
                                    className="grid h-10 w-10 place-items-center rounded-full border border-slate-200 bg-white text-lg font-black text-slate-700 transition hover:border-[#ffb16d] hover:text-[#d75d00] disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-300"
                                >
                                    +
                                </button>
                            </div>
                            {currentQuantity > availableStock && (
                                <p className="mt-2 text-xs font-bold text-rose-600">
                                    Only {availableStock} units left in stock.
                                </p>
                            )}
                        </div>

                        <div className="flex items-end gap-3">
                            <div className="text-right">
                                <p className="text-xs font-black uppercase tracking-[0.18em] text-slate-400">
                                    Line total
                                </p>
                                <p className="text-2xl font-black tracking-[-0.04em] text-slate-950">
                                    {formatMoney(item.line_total, currency)}
                                </p>
                            </div>

                            <button
                                type="button"
                                onClick={() => {
                                    router.post(
                                        route('cart.remove'),
                                        { item_id: item.id },
                                        { preserveScroll: true },
                                    );
                                }}
                                className="rounded-full border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-black uppercase tracking-[0.14em] text-rose-700 transition hover:bg-rose-100"
                            >
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    );
}

function SuggestionCard({ product, currency }) {
    const inStock = Number(product.available_stock ?? 0) > 0;

    return (
        <article className="overflow-hidden rounded-[24px] border border-[#d9e5f5] bg-white shadow-[0_12px_34px_-24px_rgba(15,23,42,0.55)]">
            <Link href={route('products.show', product.slug)} className="block">
                <div className="aspect-[4/3] overflow-hidden bg-[#f2f6ff]">
                    <img
                        src={product.primary_image_url || fallbackImage}
                        alt={product.name}
                        className="h-full w-full object-cover"
                        onError={(event) => {
                            event.currentTarget.src = fallbackImage;
                        }}
                    />
                </div>
            </Link>
            <div className="space-y-3 p-4">
                <div>
                    <p className="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">
                        {product.supplier?.company_name || 'PlexusBiz supplier'}
                    </p>
                    <h3 className="mt-1 text-sm font-black leading-5 text-slate-950">
                        <Link href={route('products.show', product.slug)} className="transition hover:text-[#0b2e71]">
                            {product.name}
                        </Link>
                    </h3>
                </div>
                <div className="flex items-end justify-between gap-3">
                    <div>
                        <p className="text-xs font-semibold text-slate-500">from</p>
                        <p className="text-lg font-black tracking-[-0.04em] text-[#0b2e71]">
                            {formatMoney(product.base_price, currency)}
                        </p>
                    </div>
                    <button
                        type="button"
                        disabled={!inStock}
                        onClick={() => {
                            if (!inStock) {
                                return;
                            }

                            router.post(
                                route('cart.add'),
                                {
                                    product_id: product.id,
                                    quantity: Number(product.moq || 1),
                                },
                                { preserveScroll: true },
                            );
                        }}
                        className="rounded-full bg-[#ff8a00] px-4 py-2 text-xs font-black uppercase tracking-[0.14em] text-white transition hover:bg-[#ef7400] disabled:cursor-not-allowed disabled:bg-slate-300"
                    >
                        Add
                    </button>
                </div>
            </div>
        </article>
    );
}

export default function Index({ auth, flash, errors, cart, suggestions, currency }) {
    const summary = cart?.summary || {};
    const items = Array.isArray(cart?.items) ? cart.items : [];
    const validationMessage = Object.values(errors || {}).find(Boolean);
    const hasInsufficientStock = items.some(item => Number(item.quantity || 1) > Number(item.product?.available_stock || 0));

    return (
        <FrontendLayout auth={auth} canLogin={true} cartCount={cart?.summary?.items_count || 0}>
            <Head title="Cart" />

            <div className="min-h-screen bg-white"
                style={{
                    background: `
                        radial-gradient(circle at top left, rgba(37, 99, 235, 0.18), transparent 28%),
                        radial-gradient(circle at bottom right, rgba(59, 130, 246, 0.12), transparent 22%),
                        linear-gradient(180deg, #ffffff 0%, #f8fbff 46%, #ffffff 100%)
                    `
                }}
            >
                <main className="w-full px-4 py-6 sm:px-6 lg:px-8">
                    <section className="overflow-hidden rounded-[32px] border border-[#d7e3f4] bg-gradient-to-r from-[#4f7fe0] via-[#3f70d4] to-[#2953b1] shadow-[0_26px_80px_-40px_rgba(7,18,46,0.9)]">
                        <div className="grid gap-8 px-5 py-6 lg:grid-cols-[1.1fr_.9fr] lg:px-8 lg:py-8">
                            <div className="space-y-5 text-white">
                                <div className="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-[11px] font-black uppercase tracking-[0.22em] text-[#ffd59a]">
                                    Shopping cart
                                    <span className="rounded-full bg-white/15 px-2 py-0.5 text-white">
                                        {Number(summary.items_count || 0)} units
                                    </span>
                                </div>

                                <div className="space-y-3">
                                    <p className="text-sm font-semibold uppercase tracking-[0.24em] text-blue-200">
                                        Review quantities, tier prices, and move to checkout
                                    </p>
                                    <h1 className="max-w-2xl text-4xl font-black tracking-[-0.06em] sm:text-5xl">
                                        Keep the buying context tight from cart to payment.
                                    </h1>
                                    <p className="max-w-2xl text-base leading-7 text-blue-100">
                                        Every item stays tied to MOQ rules and unit-price tiers, so the checkout total mirrors the order logic that already exists in the backend.
                                    </p>
                                </div>

                                <div className="flex flex-wrap gap-3">
                                    <Link
                                        href={route('products.index')}
                                        className="inline-flex rounded-full bg-[#ff8a00] px-5 py-2.5 text-xs font-black uppercase tracking-[0.16em] text-white transition hover:bg-[#ef7400]"
                                    >
                                        Continue shopping
                                    </Link>
                                    <Link
                                        href={route('checkout.index')}
                                        className="inline-flex rounded-full border border-white/20 bg-white/10 px-5 py-2.5 text-xs font-black uppercase tracking-[0.16em] text-white transition hover:bg-white/20"
                                    >
                                        Checkout
                                    </Link>
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
                                        Before gateway fees and final confirmation.
                                    </p>
                                </div>

                                <div className="rounded-[28px] border border-white/10 bg-white p-5 shadow-[0_16px_42px_-28px_rgba(15,23,42,0.8)]">
                                    <p className="text-[11px] font-black uppercase tracking-[0.24em] text-[#0b2e71]">
                                        Suggested next step
                                    </p>
                                    <p className="mt-3 text-sm leading-6 text-slate-600">
                                        Proceed to checkout after checking quantity changes and removing any item that no longer fits the order.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <FlashBanner message={flash?.success} className="mt-5" />
                    <FlashBanner message={flash?.error} type="error" className="mt-5" />
                    <FlashBanner message={validationMessage} type="error" className="mt-5" />

                    {items.length > 0 ? (
                        <section className="mt-6 grid gap-6 lg:grid-cols-[1fr_360px]">
                            <div className="space-y-4">
                                <div className="flex items-end justify-between gap-4">
                                    <div>
                                        <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-500">
                                            Cart items
                                        </p>
                                        <h2 className="mt-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                                            Review items before checkout
                                        </h2>
                                    </div>
                                    <p className="text-sm text-slate-600">
                                        {items.length} line item{items.length > 1 ? 's' : ''}
                                    </p>
                                </div>

                                <div className="space-y-4">
                                    {items.map((item) => (
                                        <LineItem key={item.id} item={item} currency={currency} />
                                    ))}
                                </div>
                            </div>

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

                                    <div className="mt-5 rounded-[24px] bg-gradient-to-r from-[#4f7fe0] via-[#3f70d4] to-[#2953b1] p-4 text-white">
                                        <p className="text-[11px] font-black uppercase tracking-[0.24em] text-blue-100">
                                            Grand total
                                        </p>
                                        <p className="mt-2 text-4xl font-black tracking-[-0.06em]">
                                            {formatMoney(summary.grand_total || 0, currency)}
                                        </p>
                                        {hasInsufficientStock ? (
                                            <p className="mt-2 text-sm leading-6 text-rose-200 font-bold">
                                                Please adjust quantities. Some items exceed available stock.
                                            </p>
                                        ) : (
                                            <p className="mt-2 text-sm leading-6 text-blue-100">
                                                The checkout step will hand off to the payment gateway selected on the next screen.
                                            </p>
                                        )}
                                    </div>

                                    {hasInsufficientStock ? (
                                        <button
                                            type="button"
                                            disabled
                                            className="mt-5 inline-flex w-full items-center justify-center rounded-full bg-slate-300 px-5 py-3 text-sm font-black uppercase tracking-[0.14em] text-white cursor-not-allowed"
                                        >
                                            Adjust stock before checkout
                                        </button>
                                    ) : (
                                        <Link
                                            href={route('checkout.index')}
                                            className="mt-5 inline-flex w-full items-center justify-center rounded-full bg-[#ff8a00] px-5 py-3 text-sm font-black uppercase tracking-[0.14em] text-white transition hover:bg-[#ef7400]"
                                        >
                                            Proceed to checkout
                                        </Link>
                                    )}
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
                                        No items in the cart yet.
                                    </h2>
                                    <p className="max-w-xl text-base leading-7 text-slate-600">
                                        Pick a product, add the MOQ quantity, and the cart will come alive with tier pricing and a checkout-ready summary.
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
                                        Ready when you are
                                    </p>
                                    <p className="mt-4 text-2xl font-black tracking-[-0.05em]">
                                        The cart keeps buyer intent, MOQ rules, and gateway selection in one flow.
                                    </p>
                                </div>
                            </div>
                        </section>
                    )}

                    <section className="mt-6 rounded-[30px] border border-[#d7e3f4] bg-white p-5 shadow-sm">
                        <div className="flex items-center justify-between gap-4">
                            <div>
                                <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-500">
                                    Continue shopping
                                </p>
                                <h2 className="mt-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                                    Products that fit the same buying pattern
                                </h2>
                            </div>
                            <Link href={route('products.index')} className="text-sm font-semibold text-[#0b2e71] hover:text-[#d75d00]">
                                View catalog
                            </Link>
                        </div>

                        <div className="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                            {suggestions.slice(0, 4).map((product) => (
                                <SuggestionCard key={product.id} product={product} currency={currency} />
                            ))}
                        </div>
                    </section>
                </main>
            </div>
        </FrontendLayout>
    );
}
