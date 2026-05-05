import { Head, Link, router } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import FrontendLayout from '@/Layouts/FrontendLayout';
import FlashBanner from '@/Components/FlashBanner';
import { selectionPillClasses } from '@/Utils/pillStyles';

const fallbackImage = '/images/landing/deal-imac.jpg';

const catalogModes = {
    catalog: {
        routeName: 'products.index',
        headTitle: 'Marketplace',
        badge: 'Live marketplace',
        kicker: 'Products, pricing tiers, and cart flow',
        title: 'A tighter product experience with B2B stock, MOQ, and fast checkout.',
        description: 'Browse active products, compare bulk prices, and jump straight into cart or checkout without leaving the store rhythm.',
        cartTitle: 'Cart snapshot',
        cartCopy: 'Items already inside the current buyer cart.',
        featuredTitle: 'Featured picks',
        featuredCopy: 'Quick access to products that already fit the active catalog flow.',
        sectionLabel: 'Product catalog',
        sectionTitle: 'Active products available for purchase',
        sectionCopy: (visible, total) => (visible === total
            ? `Showing ${total} items from the active catalog.`
            : `Showing ${visible} of ${total} items from the active catalog.`),
        noteTitle: 'Store notes',
        notes: [
            'Fast cart actions are wired to the current buyer account.',
            'Bulk pricing is calculated from quantity tiers and MOQ rules.',
            'Checkout uses the existing payment gateway flow already present in the app.',
        ],
    },
    bulk: {
        routeName: 'products.bulk',
        headTitle: 'Bulk Orders',
        badge: 'Bulk order workspace',
        kicker: 'Volume buying and tier pricing',
        title: 'Build bigger carts with clearer volume pricing and fewer clicks.',
        description: 'Bulk-ready products show MOQ, stock, and pricing tiers together so your team can move from shortlist to checkout quickly.',
        cartTitle: 'Bulk cart snapshot',
        cartCopy: 'Current items in the order builder, including any quantity-heavy picks.',
        featuredTitle: 'Tier ladder',
        featuredCopy: 'A quick look at products where quantity bands change the unit price.',
        sectionLabel: 'Bulk order catalog',
        sectionTitle: 'Products that reward larger order sizes',
        sectionCopy: (visible, total) => (visible === total
            ? `Showing ${total} bulk-eligible items from the active catalog.`
            : `Showing ${visible} of ${total} bulk-eligible items from the active catalog.`),
        noteTitle: 'Bulk order notes',
        notes: [
            'Every card keeps MOQ and stock visible before add-to-cart.',
            'Tier pricing is calculated from quantity bands already stored in the catalog.',
            'Checkout keeps the selected quantity intact so the final total stays aligned with the order intent.',
        ],
    },
    moq: {
        routeName: 'products.moq',
        headTitle: 'MOQ Pricing',
        badge: 'MOQ pricing workspace',
        kicker: 'Minimum order visibility',
        title: 'See the threshold first, then buy with confidence.',
        description: 'This view keeps MOQ-sensitive items in focus so the buyer knows the minimum order rule before adding anything to the cart.',
        cartTitle: 'MOQ snapshot',
        cartCopy: 'How many items are already waiting in the cart.',
        featuredTitle: 'MOQ focus',
        featuredCopy: 'Products with clear minimum quantities and stock context.',
        sectionLabel: 'MOQ catalog',
        sectionTitle: 'Products filtered around minimum order rules',
        sectionCopy: (visible, total) => (visible === total
            ? `Showing ${total} MOQ-aware items from the active catalog.`
            : `Showing ${visible} of ${total} MOQ-aware items from the active catalog.`),
        noteTitle: 'MOQ rules',
        notes: [
            'MOQ is shown prominently on every product card.',
            'Bulk pricing still applies, but the page stays centered on the threshold you need to reach.',
            'The cart defaults to the MOQ quantity so checkout starts from a valid order size.',
        ],
    },
};

const quickCatalogLinks = [
    { label: 'Bulk Orders', key: 'bulk', routeName: 'products.bulk' },
    { label: 'MOQ Pricing', key: 'moq', routeName: 'products.moq' },
];

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

function ProductCard({ product, currency }) {
    const inStock = Number(product.available_stock ?? 0) > 0;
    const hasPriceTier = Array.isArray(product.pricing_tiers) && product.pricing_tiers.length > 0;

    return (
        <article className="group overflow-hidden rounded-[28px] border border-[#d9e5f5] bg-white shadow-[0_12px_34px_-24px_rgba(15,23,42,0.55)] transition hover:-translate-y-1 hover:shadow-[0_18px_44px_-26px_rgba(15,23,42,0.75)]">
            <Link href={route('products.show', product.slug)} className="block">
                <div className="relative aspect-[4/3] overflow-hidden bg-[#f2f6ff]">
                    <img
                        src={product.primary_image_url || fallbackImage}
                        alt={product.name}
                        className="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                        onError={(event) => {
                            event.currentTarget.src = fallbackImage;
                        }}
                    />
                    <div className="absolute left-3 top-3 flex flex-wrap gap-2">
                        <span className="rounded-full bg-gradient-to-r from-[#4f7fe0] via-[#3f70d4] to-[#2953b1] px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-white">
                            {product.category?.name || 'Product'}
                        </span>
                        <span className={`rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] ${inStock ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'}`}>
                            {inStock ? 'In stock' : 'Out of stock'}
                        </span>
                    </div>
                </div>
            </Link>

            <div className="space-y-4 p-5">
                <div className="space-y-1">
                    <p className="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">
                        {product.supplier?.company_name || 'PlexusBiz supplier'}
                    </p>
                    <h3 className="text-lg font-black leading-6 tracking-[-0.04em] text-slate-950">
                        <Link href={route('products.show', product.slug)} className="transition hover:text-[#0b2e71]">
                            {product.name}
                        </Link>
                    </h3>
                    <p className="max-h-[3.4rem] overflow-hidden text-sm leading-6 text-slate-600">
                        {product.description || 'Wholesale product details are available on the product page.'}
                    </p>
                </div>

                <div className="grid grid-cols-3 gap-3 text-sm">
                    <div className="rounded-2xl bg-slate-50 px-3 py-2">
                        <p className="text-[10px] font-black uppercase tracking-[0.18em] text-slate-400">MOQ</p>
                        <p className="mt-1 font-bold text-slate-900">{Number(product.moq || 1)}</p>
                    </div>
                    <div className="rounded-2xl bg-slate-50 px-3 py-2">
                        <p className="text-[10px] font-black uppercase tracking-[0.18em] text-slate-400">Stock</p>
                        <p className="mt-1 font-bold text-slate-900">{Number(product.available_stock || 0)}</p>
                    </div>
                    <div className="rounded-2xl bg-slate-50 px-3 py-2">
                        <p className="text-[10px] font-black uppercase tracking-[0.18em] text-slate-400">SKU</p>
                        <p className="mt-1 truncate font-bold text-slate-900">{product.sku || 'N/A'}</p>
                    </div>
                </div>

                <div className="flex items-end justify-between gap-4">
                    <div>
                        <p className="text-[11px] font-black uppercase tracking-[0.18em] text-slate-400">
                            Starting at
                        </p>
                        <p className="text-2xl font-black tracking-[-0.05em] text-[#0b2e71]">
                            {formatMoney(product.base_price, currency)}
                        </p>
                        {hasPriceTier && (
                            <p className="mt-1 text-xs font-semibold text-slate-500">
                                Tier pricing available on bulk quantities.
                            </p>
                        )}
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
                        className="inline-flex items-center justify-center rounded-full bg-[#ff8a00] px-4 py-2.5 text-sm font-black uppercase tracking-[0.14em] text-white transition hover:bg-[#ef7400] disabled:cursor-not-allowed disabled:bg-slate-300"
                    >
                        Add to cart
                    </button>
                </div>
            </div>
        </article>
    );
}

function Pagination({ links }) {
    const normalizedLinks = Array.isArray(links) ? links.filter((link) => link.label && link.url) : [];

    if (normalizedLinks.length === 0) {
        return null;
    }

    return (
        <div className="flex flex-wrap items-center justify-center gap-2">
            {normalizedLinks.map((link, index) => (
                <Link
                    key={`${link.label}-${index}`}
                    href={link.url}
                    preserveScroll
                    className={`min-w-10 rounded-full px-4 py-2 text-sm font-semibold transition ${
                        link.active
                            ? 'bg-gradient-to-r from-[#4f7fe0] via-[#3f70d4] to-[#2953b1] text-white shadow-sm'
                            : 'border border-[#dbe5f1] bg-white text-slate-700 hover:border-[#ffb16d] hover:text-[#d75d00]'
                    }`}
                    dangerouslySetInnerHTML={{ __html: link.label }}
                />
            ))}
        </div>
    );
}

export default function Index({
    auth,
    flash,
    errors,
    cartCount,
    categories,
    featuredProducts,
    products,
    filters,
    currency,
    mode = 'catalog',
}) {
    const [search, setSearch] = useState(filters?.search || '');
    const [category, setCategory] = useState(filters?.category || '');
    const validationMessage = Object.values(errors || {}).find(Boolean);

    const activeCategory = useMemo(() => category || '', [category]);
    const activeQuick = filters?.quick || (mode === 'bulk' ? 'bulk' : mode === 'moq' ? 'moq' : '');
    const activeMode = catalogModes[activeQuick || mode] ?? catalogModes.catalog;
    const catalogRouteName = activeMode.routeName;
    const showHeroSection = activeMode.routeName === 'products.index';
    const showHeroSidebar = showHeroSection;
    const visibleCount = Number(products?.data?.length ?? 0);
    const totalCount = Number(products?.meta?.total ?? visibleCount);
    const totalProducts = Number(products?.meta?.total ?? products?.data?.length ?? 0);

    const nextQuery = (nextCategory = activeCategory) => ({
        search: search.trim() || undefined,
        category: nextCategory || undefined,
    });

    const runSearch = (nextCategory = activeCategory) => {
        router.get(
            route(catalogRouteName),
            nextQuery(nextCategory),
            {
                preserveScroll: true,
                preserveState: false,
                replace: true,
            },
        );
    };

    return (
        <FrontendLayout auth={auth} canLogin={true} cartCount={cartCount}>
            <Head title={activeMode.headTitle} />

            <div
                className={`min-h-screen text-slate-900 ${
                    activeMode.routeName === 'products.moq'
                        ? 'bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.82),_transparent_35%),radial-gradient(circle_at_top_right,_rgba(245,214,167,0.55),_transparent_30%),linear-gradient(180deg,_#f6eddc_0%,_#fbf8f0_44%,_#ffffff_100%)]'
                        : activeMode.routeName === 'products.bulk'
                            ? 'bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.82),_transparent_35%),radial-gradient(circle_at_top_right,_rgba(245,214,167,0.55),_transparent_30%),linear-gradient(180deg,_#f6eddc_0%,_#fbf8f0_44%,_#ffffff_100%)]'
                            : 'bg-[radial-gradient(circle_at_top_left,_rgba(11,46,113,0.18),_transparent_35%),radial-gradient(circle_at_top_right,_rgba(255,138,0,0.18),_transparent_28%),linear-gradient(180deg,_#eef5ff_0%,_#f8fbff_46%,_#ffffff_100%)]'
                }`}
            >
                <main className="w-full px-4 py-6 sm:px-6 lg:px-8">
                    <section className={`${showHeroSection ? 'overflow-hidden rounded-[32px] border border-[#d7e3f4] bg-gradient-to-r from-[#4f7fe0] via-[#3f70d4] to-[#2953b1] shadow-[0_26px_80px_-40px_rgba(7,18,46,0.9)]' : 'hidden'}`}>
                        <div className={`grid gap-8 px-5 py-6 lg:px-8 lg:py-8 ${showHeroSidebar ? 'lg:grid-cols-[1.1fr_.9fr]' : ''}`}>
                            <div className="space-y-5 text-white">
                                <div className="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-[11px] font-black uppercase tracking-[0.22em] text-[#ffd59a]">
                                    {activeMode.badge}
                                    <span className="rounded-full bg-white/15 px-2 py-0.5 text-white">
                                        {totalProducts} products
                                    </span>
                                </div>

                                <div className="space-y-3">
                                    <p className="text-sm font-semibold uppercase tracking-[0.24em] text-blue-200">
                                        {activeMode.kicker}
                                    </p>
                                    <h1 className="max-w-2xl text-4xl font-black tracking-[-0.06em] sm:text-5xl">
                                        {activeMode.title}
                                    </h1>
                                    <p className="max-w-2xl text-base leading-7 text-blue-100">
                                        {activeMode.description}
                                    </p>
                                </div>

                                <form
                                    className="flex flex-col gap-3 rounded-[24px] border border-white/10 bg-white/10 p-4 backdrop-blur sm:flex-row"
                                    onSubmit={(event) => {
                                        event.preventDefault();
                                        runSearch();
                                    }}
                                >
                                    <input
                                        type="search"
                                        value={search}
                                        onChange={(event) => setSearch(event.target.value)}
                                        placeholder="Search SKU, product name, or supplier"
                                        className="h-12 flex-1 rounded-full border border-white/10 bg-white px-5 text-sm font-medium text-slate-900 outline-none placeholder:text-slate-400"
                                    />
                                    <button
                                        type="submit"
                                        className="h-12 rounded-full bg-[#ff8a00] px-6 text-sm font-black uppercase tracking-[0.16em] text-white transition hover:bg-[#ef7400]"
                                >
                                    Search
                                </button>
                            </form>

                                <div className="flex flex-wrap items-center gap-2">
                                    <span className="text-[11px] font-black uppercase tracking-[0.24em] text-blue-200">
                                        Quick picks
                                    </span>
                                    {quickCatalogLinks.map((item) => {
                                        const isActive = activeQuick === item.key;

                                        return (
                                            <Link
                                                key={item.key}
                                                href={route(item.routeName, nextQuery(activeCategory))}
                                                preserveScroll
                                                className={`rounded-full border px-4 py-2 text-xs font-black uppercase tracking-[0.16em] transition ${selectionPillClasses(isActive, {
                                                    active: 'border-white bg-white text-[#0b2e71]',
                                                    inactive: 'border-white/20 bg-white/10 text-white hover:bg-white/15',
                                                })}`}
                                            >
                                                {item.label}
                                            </Link>
                                        );
                                    })}
                                </div>

                                <div className="flex flex-wrap gap-2">
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setCategory('');
                                            runSearch('');
                                        }}
                                        className={`rounded-full border px-4 py-2 text-xs font-black uppercase tracking-[0.16em] transition ${selectionPillClasses(activeCategory === '', {
                                            active: 'border-white bg-white text-[#0b2e71]',
                                            inactive: 'border-white/20 bg-white/10 text-white hover:bg-white/15',
                                        })}`}
                                    >
                                        All
                                    </button>
                                    {categories.map((item) => (
                                        <button
                                            key={item.slug}
                                            type="button"
                                            onClick={() => {
                                                setCategory(item.slug);
                                                runSearch(item.slug);
                                            }}
                                            className={`rounded-full border px-4 py-2 text-xs font-black uppercase tracking-[0.16em] transition ${selectionPillClasses(activeCategory === item.slug, {
                                                active: 'border-[#ff8a00] bg-[#ff8a00] text-white',
                                                inactive: 'border-white/20 bg-white/10 text-white hover:bg-white/15',
                                            })}`}
                                        >
                                            {item.name} <span className="opacity-75">({item.active_products_count})</span>
                                        </button>
                                    ))}
                                </div>
                            </div>

                            {showHeroSidebar ? (
                                <div className="grid gap-4 sm:grid-cols-2">
                                    <div className="rounded-[28px] border border-white/10 bg-white/10 p-5 text-white backdrop-blur">
                                        <p className="text-[11px] font-black uppercase tracking-[0.24em] text-blue-100">
                                            {activeMode.cartTitle}
                                        </p>
                                        <p className="mt-3 text-4xl font-black tracking-[-0.06em]">{Number(cartCount || 0)}</p>
                                        <p className="mt-2 text-sm leading-6 text-blue-100">
                                            {activeMode.cartCopy}
                                        </p>
                                    </div>

                                    <div className="rounded-[28px] border border-white/10 bg-white p-5 shadow-[0_16px_42px_-28px_rgba(15,23,42,0.8)]">
                                        <p className="text-[11px] font-black uppercase tracking-[0.24em] text-[#0b2e71]">
                                            {activeMode.featuredTitle}
                                        </p>
                                        <p className="mt-2 text-sm leading-6 text-slate-500">
                                            {activeMode.featuredCopy}
                                        </p>
                                        <div className="mt-4 space-y-3">
                                            {featuredProducts.slice(0, 3).map((product) => (
                                                <Link
                                                    key={product.id}
                                                    href={route('products.show', product.slug)}
                                                    className="flex items-center gap-3 rounded-2xl border border-slate-100 p-3 transition hover:border-[#ffb16d] hover:bg-[#fff9f3]"
                                                >
                                                    <img
                                                        src={product.primary_image_url || fallbackImage}
                                                        alt={product.name}
                                                        className="h-14 w-14 rounded-2xl object-cover"
                                                        onError={(event) => {
                                                            event.currentTarget.src = fallbackImage;
                                                        }}
                                                    />
                                                    <div className="min-w-0 flex-1">
                                                        <p className="truncate text-sm font-black text-slate-950">{product.name}</p>
                                                        <p className="text-xs text-slate-500">
                                                            {formatMoney(product.base_price, currency)} · MOQ {product.moq}
                                                        </p>
                                                    </div>
                                                </Link>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            ) : null}
                        </div>
                    </section>

                    {!showHeroSection ? (
                        <section className="space-y-4">
                            <div className="flex flex-wrap items-center gap-2">
                                <span className="rounded-full border border-[#d7e3f4] bg-white px-4 py-2 text-[11px] font-black uppercase tracking-[0.22em] text-[#0b2e71]">
                                    {activeMode.badge}
                                </span>
                                <span className="rounded-full border border-[#d7e3f4] bg-white px-4 py-2 text-[11px] font-black uppercase tracking-[0.22em] text-slate-500">
                                    {totalProducts} products
                                </span>
                                <span className="rounded-full border border-[#d7e3f4] bg-white px-4 py-2 text-[11px] font-black uppercase tracking-[0.22em] text-slate-500">
                                    {activeMode.kicker}
                                </span>
                            </div>

                            <form
                                className="flex flex-col gap-3 lg:flex-row lg:items-center"
                                onSubmit={(event) => {
                                    event.preventDefault();
                                    runSearch();
                                }}
                            >
                                <input
                                    type="search"
                                    value={search}
                                    onChange={(event) => setSearch(event.target.value)}
                                    placeholder="Search SKU, product name, or supplier"
                                    className="h-12 w-full rounded-full border border-slate-200 bg-white px-5 text-sm font-medium text-slate-900 outline-none placeholder:text-slate-400 focus:border-[#4f7fe0] focus:bg-white lg:w-[360px] lg:flex-none"
                                />
                                <div className="flex flex-wrap items-center gap-2">
                                    {quickCatalogLinks.map((item) => {
                                        const isActive = activeQuick === item.key;

                                        return (
                                            <Link
                                                key={item.key}
                                                href={route(item.routeName, nextQuery(activeCategory))}
                                                preserveScroll
                                                className={`rounded-full border px-4 py-2 text-xs font-black uppercase tracking-[0.16em] transition ${selectionPillClasses(isActive, {
                                                    active: 'border-[#0b2e71] bg-[#0b2e71] text-white',
                                                    inactive: 'border-[#d7e3f4] bg-white text-[#0b2e71] hover:border-[#ffb16d] hover:text-[#d75d00]',
                                                })}`}
                                            >
                                                {item.label}
                                            </Link>
                                        );
                                    })}
                                    <button
                                        type="submit"
                                        className="h-12 rounded-full bg-[#ff8a00] px-6 text-sm font-black uppercase tracking-[0.16em] text-white transition hover:bg-[#ef7400]"
                                    >
                                        Search
                                    </button>
                                </div>
                            </form>

                            <div className="flex flex-wrap gap-2">
                                <button
                                    type="button"
                                    onClick={() => {
                                        setCategory('');
                                        runSearch('');
                                    }}
                                    className={`rounded-full border px-4 py-2 text-xs font-black uppercase tracking-[0.16em] transition ${selectionPillClasses(activeCategory === '', {
                                        active: 'border-[#0b2e71] bg-[#0b2e71] text-white',
                                        inactive: 'border-[#d7e3f4] bg-white text-slate-700 hover:border-[#4f7fe0] hover:text-[#0b2e71]',
                                    })}`}
                                >
                                    All
                                </button>
                                {categories.map((item) => (
                                    <button
                                        key={item.slug}
                                        type="button"
                                        onClick={() => {
                                            setCategory(item.slug);
                                            runSearch(item.slug);
                                        }}
                                        className={`rounded-full border px-4 py-2 text-xs font-black uppercase tracking-[0.16em] transition ${selectionPillClasses(activeCategory === item.slug, {
                                            active: 'border-[#ff8a00] bg-[#ff8a00] text-white',
                                            inactive: 'border-[#d7e3f4] bg-white text-slate-700 hover:border-[#ffb16d] hover:text-[#d75d00]',
                                        })}`}
                                    >
                                        {item.name} <span className="opacity-75">({item.active_products_count})</span>
                                    </button>
                                ))}
                            </div>
                        </section>
                    ) : null}

                    <FlashBanner message={flash?.success} className="mt-5" />
                    <FlashBanner message={flash?.error} type="error" className="mt-5" />
                    <FlashBanner message={validationMessage} type="error" className="mt-5" />

                    <section className="mt-6 grid gap-6 lg:grid-cols-[1fr_320px]">
                        <div className="space-y-5">
                            <div className="flex items-end justify-between gap-4">
                                <div>
                                    <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-500">
                                        {activeMode.sectionLabel}
                                    </p>
                                    <h2 className="mt-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                                        {activeMode.sectionTitle}
                                    </h2>
                                </div>
                                <p className="text-sm text-slate-600">
                                    {activeMode.sectionCopy(visibleCount, totalCount)}
                                </p>
                            </div>

                            <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                {(products?.data ?? []).map((product) => (
                                    <ProductCard key={product.id} product={product} currency={currency} />
                                ))}
                            </div>

                            <Pagination links={products?.links || []} />
                        </div>

                        <aside className="space-y-4">
                            <div className="rounded-[28px] border border-[#d7e3f4] bg-white p-5 shadow-sm">
                                <p className="text-[11px] font-black uppercase tracking-[0.24em] text-[#0b2e71]">{activeMode.noteTitle}</p>
                                <div className="mt-4 space-y-3 text-sm leading-6 text-slate-600">
                                    {activeMode.notes.map((note) => (
                                        <p key={note}>{note}</p>
                                    ))}
                                </div>
                            </div>

                            {showHeroSidebar ? (
                                <div className="rounded-[28px] border border-[#d7e3f4] bg-gradient-to-r from-[#4f7fe0] via-[#3f70d4] to-[#2953b1] p-5 text-white shadow-sm">
                                    <p className="text-[11px] font-black uppercase tracking-[0.24em] text-blue-200">{activeMode.cartTitle}</p>
                                    <p className="mt-3 text-3xl font-black tracking-[-0.05em]">{Number(cartCount || 0)}</p>
                                    <p className="mt-2 text-sm leading-6 text-blue-100">
                                        {activeMode.cartCopy}
                                    </p>
                                    <Link
                                        href={route('cart.index')}
                                        className="mt-4 inline-flex rounded-full bg-white px-4 py-2.5 text-sm font-black uppercase tracking-[0.14em] text-[#0b2e71] transition hover:bg-blue-50"
                                    >
                                        View cart
                                    </Link>
                                </div>
                            ) : null}
                        </aside>
                    </section>
                </main>
            </div>
        </FrontendLayout>
    );
}
