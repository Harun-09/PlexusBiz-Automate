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

function resolveTierPrice(quantity, tiers, fallbackPrice) {
    const sortedTiers = Array.isArray(tiers)
        ? [...tiers].sort((left, right) => Number(left.min_quantity) - Number(right.min_quantity))
        : [];

    const tier = sortedTiers.filter((entry) => Number(quantity) >= Number(entry.min_quantity)).pop();

    return {
        tier,
        price: Number(tier?.unit_price ?? fallbackPrice ?? 0),
    };
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

function TierTable({ tiers, quantity, currency, basePrice }) {
    const rows = Array.isArray(tiers) ? [...tiers].sort((left, right) => left.min_quantity - right.min_quantity) : [];
    const current = resolveTierPrice(quantity, rows, basePrice);

    if (rows.length === 0) {
        return (
            <div className="rounded-3xl border border-dashed border-slate-200 bg-slate-50 p-5 text-sm text-slate-600">
                No pricing tiers are configured for this product yet.
            </div>
        );
    }

    return (
        <div className="overflow-hidden rounded-[28px] border border-[#d7e3f4] bg-white shadow-sm">
            <div className="border-b border-slate-100 px-5 py-4">
                <p className="text-[11px] font-black uppercase tracking-[0.24em] text-[#0b2e71]">
                    Bulk pricing
                </p>
                <p className="mt-1 text-sm text-slate-600">
                    Quantity-based pricing updates as your order size grows.
                </p>
            </div>
            <div className="divide-y divide-slate-100">
                {rows.map((tier) => {
                    const active = current.tier?.id === tier.id;

                    return (
                        <div
                            key={tier.id}
                            className={`flex items-center justify-between gap-4 px-5 py-4 ${active ? 'bg-[#fff8ef]' : ''}`}
                        >
                            <div>
                                <p className="text-sm font-bold text-slate-950">
                                    {Number(tier.min_quantity)}+ units
                                </p>
                                <p className="text-xs text-slate-500">
                                    Starts at MOQ threshold
                                </p>
                            </div>
                            <div className="text-right">
                                <p className="text-base font-black tracking-[-0.03em] text-[#0b2e71]">
                                    {formatMoney(tier.unit_price, currency)}
                                </p>
                                {active && (
                                    <p className="text-xs font-semibold text-[#d75d00]">
                                        Current tier for {quantity} units
                                    </p>
                                )}
                            </div>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}

export default function Show({
    auth,
    flash,
    cartCount,
    currency,
    defaultQuantity,
    product,
    relatedProducts,
    supplierProducts,
    isPurchasable,
}) {
    const gallery = Array.isArray(product.gallery) && product.gallery.length > 0
        ? product.gallery
        : [{ url: product.primary_image_url || fallbackImage, alt: product.name, is_primary: true }];

    const [selectedImage, setSelectedImage] = useState(gallery[0]?.url || product.primary_image_url || fallbackImage);
    const [quantity, setQuantity] = useState(Math.max(1, Number(defaultQuantity || product.moq || 1)));

    const availableStock = Number(product.available_stock ?? 0);
    const minimumOrder = Number(product.moq ?? 1);
    const canPurchase = Boolean(isPurchasable) && availableStock >= minimumOrder;
    const safeQuantity = useMemo(() => {
        if (!canPurchase) {
            return Math.max(1, minimumOrder);
        }

        return Math.min(Math.max(1, quantity), Math.max(minimumOrder, availableStock));
    }, [availableStock, canPurchase, minimumOrder, quantity]);

    const pricing = useMemo(
        () => resolveTierPrice(safeQuantity, product.pricing_tiers || [], Number(product.base_price ?? 0)),
        [product.pricing_tiers, product.base_price, safeQuantity],
    );

    const unitPrice = pricing.price;
    const lineTotal = unitPrice * safeQuantity;
    const savings = Number(product.base_price ?? 0) - unitPrice;
    const stockBadge = !canPurchase
        ? 'Out of stock'
        : availableStock <= minimumOrder
            ? `Only ${availableStock} left`
            : `${availableStock} available`;

    const addToCart = () => {
        if (!canPurchase) {
            return;
        }

        router.post(
            route('cart.add'),
            {
                product_id: product.id,
                quantity: safeQuantity,
            },
            { preserveScroll: true },
        );
    };

    const buyNow = () => {
        if (!canPurchase) {
            return;
        }

        router.post(
            route('cart.add'),
            {
                product_id: product.id,
                quantity: safeQuantity,
            },
            {
                preserveScroll: true,
                onSuccess: () => router.visit(route('checkout.index')),
            },
        );
    };

    return (
        <FrontendLayout auth={auth} canLogin={true} cartCount={cartCount}>
            <Head title={product.name} />

            <div className="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(11,46,113,0.14),_transparent_30%),radial-gradient(circle_at_top_right,_rgba(255,138,0,0.16),_transparent_26%),linear-gradient(180deg,_#eef5ff_0%,_#f9fbff_46%,_#ffffff_100%)] text-slate-900">
                <main className="mx-auto w-full max-w-[1500px] px-4 py-6 sm:px-6 lg:px-8">
                    <div className="mb-5 flex flex-wrap items-center gap-2 text-sm text-slate-500">
                        <Link href={route('products.index')} className="font-semibold text-[#0b2e71] transition hover:text-[#d75d00]">
                            Marketplace
                        </Link>
                        <span>/</span>
                        <span>{product.category?.name || 'Products'}</span>
                        <span>/</span>
                        <span className="text-slate-700">{product.name}</span>
                    </div>

                    <FlashBanner message={flash?.success} />
                    <FlashBanner message={flash?.error} type="error" />

                    <section className="mt-5 grid gap-6 lg:grid-cols-[1.2fr_.8fr]">
                        <div className="space-y-6">
                            <div className="overflow-hidden rounded-[32px] border border-[#d7e3f4] bg-white shadow-[0_20px_70px_-38px_rgba(15,23,42,0.85)]">
                                <div className="grid gap-0 xl:grid-cols-[1fr_112px]">
                                    <div className="relative bg-[#f3f7ff] p-4 sm:p-6">
                                        <div className="absolute left-6 top-6 z-10 flex flex-wrap gap-2">
                                            <span className="rounded-full bg-[#0b2e71] px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-white">
                                                {product.supplier?.company_name || 'PlexusBiz supplier'}
                                            </span>
                                            <span className={`rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] ${canPurchase ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'}`}>
                                                {stockBadge}
                                            </span>
                                        </div>

                                        <div className="flex min-h-[420px] items-center justify-center">
                                            <img
                                                src={selectedImage || fallbackImage}
                                                alt={product.name}
                                                className="max-h-[560px] w-full rounded-[28px] object-contain"
                                                onError={(event) => {
                                                    event.currentTarget.src = fallbackImage;
                                                }}
                                            />
                                        </div>
                                    </div>

                                    <div className="border-t border-[#edf3fb] bg-white p-4 xl:border-l xl:border-t-0">
                                        <p className="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">
                                            Gallery
                                        </p>
                                        <div className="mt-4 grid grid-cols-2 gap-3 xl:grid-cols-1">
                                            {gallery.map((image, index) => (
                                                <button
                                                    key={`${image.url}-${index}`}
                                                    type="button"
                                                    onClick={() => setSelectedImage(image.url)}
                                                    className={`overflow-hidden rounded-2xl border transition ${
                                                        selectedImage === image.url ? 'border-[#ff8a00] ring-2 ring-[#ff8a00]/20' : 'border-slate-200 hover:border-[#bfd0f0]'
                                                    }`}
                                                >
                                                    <img
                                                        src={image.url || fallbackImage}
                                                        alt={image.alt || product.name}
                                                        className="aspect-square w-full object-cover"
                                                        onError={(event) => {
                                                            event.currentTarget.src = fallbackImage;
                                                        }}
                                                    />
                                                </button>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="grid gap-4 md:grid-cols-2">
                                <div className="rounded-[28px] border border-[#d7e3f4] bg-white p-5 shadow-sm">
                                    <p className="text-[11px] font-black uppercase tracking-[0.24em] text-[#0b2e71]">
                                        Product details
                                    </p>
                                    <h1 className="mt-2 text-3xl font-black tracking-[-0.05em] text-slate-950">
                                        {product.name}
                                    </h1>
                                    <p className="mt-3 text-base leading-7 text-slate-600">
                                        {product.description || 'Wholesale product detail page with MOQ, pricing tiers, and a direct checkout path.'}
                                    </p>
                                </div>

                                <div className="rounded-[28px] border border-[#d7e3f4] bg-gradient-to-br from-[#0b2e71] via-[#103a87] to-[#0f4fa8] p-5 text-white shadow-sm">
                                    <p className="text-[11px] font-black uppercase tracking-[0.24em] text-blue-200">
                                        Purchase flow
                                    </p>
                                    <div className="mt-4 grid grid-cols-2 gap-3 text-sm">
                                        <div className="rounded-2xl bg-white/10 p-3">
                                            <p className="text-[10px] font-black uppercase tracking-[0.18em] text-blue-100">MOQ</p>
                                            <p className="mt-1 text-lg font-black">{minimumOrder}</p>
                                        </div>
                                        <div className="rounded-2xl bg-white/10 p-3">
                                            <p className="text-[10px] font-black uppercase tracking-[0.18em] text-blue-100">Stock</p>
                                            <p className="mt-1 text-lg font-black">{availableStock}</p>
                                        </div>
                                        <div className="rounded-2xl bg-white/10 p-3">
                                            <p className="text-[10px] font-black uppercase tracking-[0.18em] text-blue-100">SKU</p>
                                            <p className="mt-1 truncate text-sm font-black">{product.sku || 'N/A'}</p>
                                        </div>
                                        <div className="rounded-2xl bg-white/10 p-3">
                                            <p className="text-[10px] font-black uppercase tracking-[0.18em] text-blue-100">Category</p>
                                            <p className="mt-1 truncate text-sm font-black">{product.category?.name || 'Uncategorized'}</p>
                                        </div>
                                    </div>
                                    <p className="mt-4 text-sm leading-6 text-blue-100">
                                        Start from the MOQ, then the tier table and checkout step keep the buying flow aligned with the inventory rules already in the backend.
                                    </p>
                                </div>
                            </div>

                            <div className="grid gap-4 xl:grid-cols-[.9fr_1.1fr]">
                                <div className="overflow-hidden rounded-[28px] border border-[#d7e3f4] bg-white shadow-sm">
                                    <div className="border-b border-slate-100 px-5 py-4">
                                        <p className="text-[11px] font-black uppercase tracking-[0.24em] text-[#0b2e71]">
                                            Quick specs
                                        </p>
                                    </div>
                                    <dl className="grid gap-0 divide-y divide-slate-100 px-5">
                                        {[
                                            ['Supplier', product.supplier?.company_name || 'N/A'],
                                            ['Category', product.category?.name || 'N/A'],
                                            ['SKU', product.sku || 'N/A'],
                                            ['MOQ', `${minimumOrder}`],
                                            ['Available stock', `${availableStock}`],
                                        ].map(([label, value]) => (
                                            <div key={label} className="flex items-center justify-between gap-4 py-3 text-sm">
                                                <dt className="font-semibold text-slate-500">{label}</dt>
                                                <dd className="text-right font-black text-slate-950">{value}</dd>
                                            </div>
                                        ))}
                                    </dl>
                                </div>

                                <TierTable
                                    tiers={product.pricing_tiers || []}
                                    quantity={safeQuantity}
                                    currency={currency}
                                    basePrice={product.base_price}
                                />
                            </div>
                        </div>

                        <aside className="space-y-4">
                            <div className="sticky top-24 space-y-4 rounded-[32px] border border-[#d7e3f4] bg-white p-5 shadow-[0_18px_60px_-38px_rgba(15,23,42,0.85)]">
                                <div>
                                    <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">
                                        Current price
                                    </p>
                                    <div className="mt-2 flex items-end gap-3">
                                        <p className="text-4xl font-black tracking-[-0.06em] text-[#0b2e71]">
                                            {formatMoney(unitPrice, currency)}
                                        </p>
                                        {savings > 0 && (
                                            <span className="rounded-full bg-[#fff3e7] px-3 py-1 text-xs font-black uppercase tracking-[0.14em] text-[#d75d00]">
                                                Save {formatMoney(savings, currency)}
                                            </span>
                                        )}
                                    </div>
                                    <p className="mt-2 text-sm leading-6 text-slate-600">
                                        {pricing.tier ? `Tier price unlocked at ${pricing.tier.min_quantity}+ units.` : 'Base price applies until the first tier threshold is reached.'}
                                    </p>
                                </div>

                                <div className="rounded-[28px] bg-slate-50 p-4">
                                    <p className="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">
                                        Quantity
                                    </p>
                                    <div className="mt-3 flex items-center gap-3">
                                        <button
                                            type="button"
                                            disabled={!canPurchase || safeQuantity <= minimumOrder}
                                            onClick={() => setQuantity((current) => Math.max(minimumOrder, current - 1))}
                                            className="grid h-11 w-11 place-items-center rounded-full border border-slate-200 bg-white text-lg font-black text-slate-700 transition hover:border-[#ffb16d] hover:text-[#d75d00] disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-300"
                                        >
                                            -
                                        </button>
                                        <input
                                            type="number"
                                            min={minimumOrder}
                                            max={Math.max(minimumOrder, availableStock)}
                                            value={safeQuantity}
                                            disabled={!canPurchase}
                                            onChange={(event) => {
                                                const nextValue = Number(event.target.value || minimumOrder);
                                                const maxQuantity = Math.max(minimumOrder, availableStock);
                                                setQuantity(Math.min(Math.max(minimumOrder, nextValue), maxQuantity));
                                            }}
                                            className="h-11 w-full rounded-2xl border border-slate-200 bg-white text-center text-base font-black text-slate-950 outline-none focus:border-[#ff8a00]"
                                        />
                                        <button
                                            type="button"
                                            disabled={!canPurchase || safeQuantity >= Math.max(minimumOrder, availableStock)}
                                            onClick={() => setQuantity((current) => {
                                                const maxQuantity = Math.max(minimumOrder, availableStock);
                                                return Math.min(maxQuantity, current + 1);
                                            })}
                                            className="grid h-11 w-11 place-items-center rounded-full border border-slate-200 bg-white text-lg font-black text-slate-700 transition hover:border-[#ffb16d] hover:text-[#d75d00] disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-300"
                                        >
                                            +
                                        </button>
                                    </div>
                                    <p className="mt-2 text-xs font-semibold text-slate-500">
                                        Order total: {formatMoney(lineTotal, currency)}
                                    </p>
                                </div>

                                <div className="grid gap-3">
                                    {auth?.user ? (
                                        <>
                                            <button
                                                type="button"
                                                disabled={!canPurchase}
                                                onClick={addToCart}
                                                className="inline-flex items-center justify-center rounded-full bg-[#0b2e71] px-5 py-3 text-sm font-black uppercase tracking-[0.14em] text-white transition hover:bg-[#09255b] disabled:cursor-not-allowed disabled:bg-slate-300"
                                            >
                                                Add to cart
                                            </button>
                                            <button
                                                type="button"
                                                disabled={!canPurchase}
                                                onClick={buyNow}
                                                className="inline-flex items-center justify-center rounded-full bg-[#ff8a00] px-5 py-3 text-sm font-black uppercase tracking-[0.14em] text-white transition hover:bg-[#ef7400] disabled:cursor-not-allowed disabled:bg-slate-300"
                                            >
                                                Buy now
                                            </button>
                                        </>
                                    ) : (
                                        <Link
                                            href={route('login')}
                                            className="inline-flex items-center justify-center rounded-full bg-[#ff8a00] px-5 py-3 text-sm font-black uppercase tracking-[0.14em] text-white transition hover:bg-[#ef7400]"
                                        >
                                            Sign in to buy
                                        </Link>
                                    )}
                                </div>

                                <div className="rounded-[24px] border border-[#e8eef8] bg-[#f8fbff] p-4 text-sm leading-6 text-slate-600">
                                    <p className="font-bold text-slate-900">What happens next</p>
                                    <ul className="mt-2 space-y-1">
                                        <li>1. Add the selected quantity to your cart.</li>
                                        <li>2. Review the cart summary.</li>
                                        <li>3. Continue to checkout and choose the payment gateway.</li>
                                    </ul>
                                </div>
                            </div>
                        </aside>
                    </section>

                    <section className="mt-6 grid gap-6 lg:grid-cols-2">
                        <div className="rounded-[30px] border border-[#d7e3f4] bg-white p-5 shadow-sm">
                            <div className="flex items-center justify-between gap-4">
                                <div>
                                    <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-500">
                                        Related products
                                    </p>
                                    <h2 className="mt-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                                        Similar items that fit the same buying context
                                    </h2>
                                </div>
                                <Link href={route('products.index')} className="text-sm font-semibold text-[#0b2e71] hover:text-[#d75d00]">
                                    View all
                                </Link>
                            </div>
                            <div className="mt-5 grid gap-4 md:grid-cols-2">
                                {relatedProducts.slice(0, 4).map((item) => (
                                    <SuggestionCard key={item.id} product={item} currency={currency} />
                                ))}
                            </div>
                        </div>

                        <div className="rounded-[30px] border border-[#d7e3f4] bg-white p-5 shadow-sm">
                            <div className="flex items-center justify-between gap-4">
                                <div>
                                    <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-500">
                                        More from this supplier
                                    </p>
                                    <h2 className="mt-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                                        Keep the same vendor context
                                    </h2>
                                </div>
                                <span className="rounded-full bg-slate-100 px-3 py-1 text-xs font-black uppercase tracking-[0.14em] text-slate-600">
                                    {supplierProducts.length} items
                                </span>
                            </div>
                            <div className="mt-5 grid gap-4 md:grid-cols-2">
                                {supplierProducts.slice(0, 4).map((item) => (
                                    <SuggestionCard key={item.id} product={item} currency={currency} />
                                ))}
                            </div>
                        </div>
                    </section>
                </main>
            </div>
        </FrontendLayout>
    );
}
