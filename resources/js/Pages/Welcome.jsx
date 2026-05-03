import { Head, Link } from '@inertiajs/react';

const topTools = ['Shell Shocker', 'PC Builder', 'Business', 'Help Center', 'Returns & Orders'];

const categories = [
    'Components & Storage',
    'Computer Systems',
    'Computer Peripherals',
    'Networking',
    'Gaming & VR',
    'Smart Home & Security',
    'Office Solutions',
    'Automotive & Tools',
];

const spotlightCards = [
    {
        title: 'Dual-monitor workstation bundles',
        copy: 'Save up to 28% on creator-ready desk builds.',
        image: '/images/landing/deal-two-monitor.jpg',
        tag: 'Hot combo',
    },
    {
        title: 'Workspace essentials',
        copy: 'Fast shipping on keyboards, docks, and displays.',
        image: '/images/landing/deal-imac.jpg',
        tag: 'Today only',
    },
];

const flashDeals = [
    {
        title: 'Designer two-screen setup kit',
        category: 'Workstation',
        price: '$799.99',
        oldPrice: '$999.99',
        image: '/images/landing/deal-two-monitor.jpg',
    },
    {
        title: 'Minimal laptop desk starter pack',
        category: 'Office + Laptop',
        price: '$429.00',
        oldPrice: '$549.00',
        image: '/images/landing/hero-desk.jpg',
    },
    {
        title: 'Creator desk accessories set',
        category: 'Creator tools',
        price: '$189.99',
        oldPrice: '$239.99',
        image: '/images/landing/deal-photographer-desk.jpg',
    },
    {
        title: 'Elegant iMac productivity bundle',
        category: 'Desktop setup',
        price: '$999.00',
        oldPrice: '$1,149.00',
        image: '/images/landing/deal-imac.jpg',
    },
];

const quickSignals = [
    {
        title: 'Fast Dispatch',
        copy: 'Same-day processing for selected products.',
    },
    {
        title: 'Verified Sellers',
        copy: 'Curated vendors with performance tracking.',
    },
    {
        title: 'Price Alerts',
        copy: 'Track drops and receive instant deal updates.',
    },
];

export default function Welcome({ auth, canLogin, canRegister }) {
    return (
        <>
            <Head title="PlexusBiz Automate" />

            <div className="min-h-screen bg-[#e9f1fb] text-slate-900">
                <header>
                    <div className="bg-gradient-to-r from-[#0b1e44] via-[#16346b] to-[#1f4d94] text-[#d9e7ff]">
                        <div className="mx-auto flex w-full max-w-[1240px] items-center justify-between px-4 py-2 text-[11px] uppercase tracking-[0.14em] sm:px-6 lg:px-8">
                            <div className="hidden gap-6 md:flex">
                                {topTools.map((tool) => (
                                    <span key={tool}>{tool}</span>
                                ))}
                            </div>
                            <span className="font-semibold">Limited tech drops every hour</span>
                        </div>
                    </div>

                    <div className="border-b border-slate-200 bg-white/95 backdrop-blur">
                        <div className="mx-auto flex w-full max-w-[1240px] flex-col gap-4 px-4 py-4 sm:px-6 lg:px-8 xl:flex-row xl:items-center xl:gap-6">
                            <Link href="/" className="flex items-center gap-3 self-start xl:self-auto">
                                <img
                                    src="/images/project-logo.png"
                                    alt="PlexusBiz Automate"
                                    className="h-11 w-11 rounded-xl border border-slate-200 bg-white p-2 shadow-sm"
                                />
                                <div>
                                    <p className="text-lg font-extrabold tracking-tight text-[#0b2450]">
                                        PlexusBiz Automate
                                    </p>
                                    <p className="text-xs font-medium uppercase tracking-[0.18em] text-slate-500">
                                        Smart Commerce Hub
                                    </p>
                                </div>
                            </Link>

                            <form className="relative flex-1">
                                <input
                                    type="search"
                                    placeholder="Search products, bundles, accessories..."
                                    className="w-full rounded-full border border-slate-200 bg-slate-50 px-5 py-3 text-sm outline-none transition focus:border-[#2362c9] focus:bg-white focus:ring-4 focus:ring-[#2362c9]/15"
                                />
                                <button
                                    type="button"
                                    className="absolute right-1.5 top-1.5 rounded-full bg-[#ff7b22] px-5 py-2 text-xs font-bold uppercase tracking-[0.14em] text-white transition hover:bg-[#ef6c12]"
                                >
                                    Search
                                </button>
                            </form>

                            <div className="flex items-center gap-2">
                                {auth.user ? (
                                    <Link
                                        href={route('dashboard')}
                                        className="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-[#2362c9] hover:text-[#2362c9]"
                                    >
                                        Dashboard
                                    </Link>
                                ) : (
                                    <>
                                        {canLogin && (
                                            <Link
                                                href={route('login')}
                                                className="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-[#2362c9] hover:text-[#2362c9]"
                                            >
                                                Sign in
                                            </Link>
                                        )}

                                        {canRegister && (
                                            <Link
                                                href={route('register')}
                                                className="rounded-full bg-[#0f3f9f] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#0b2f79]"
                                            >
                                                Register
                                            </Link>
                                        )}
                                    </>
                                )}

                                <button
                                    type="button"
                                    className="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-[#2362c9] hover:text-[#2362c9]"
                                >
                                    Cart (0)
                                </button>
                            </div>
                        </div>
                    </div>

                    <div className="border-b border-slate-200 bg-white">
                        <div className="mx-auto flex w-full max-w-[1240px] items-center gap-2 overflow-x-auto px-4 py-3 sm:px-6 lg:px-8">
                            {categories.map((category) => (
                                <button
                                    key={category}
                                    type="button"
                                    className="whitespace-nowrap rounded-full border border-slate-200 bg-slate-50 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.12em] text-slate-700 transition hover:border-[#2362c9] hover:bg-[#edf4ff] hover:text-[#1a56b5]"
                                >
                                    {category}
                                </button>
                            ))}
                        </div>
                    </div>
                </header>

                <main className="mx-auto w-full max-w-[1240px] space-y-8 px-4 py-8 sm:px-6 lg:px-8">
                    <section className="grid gap-6 lg:grid-cols-[280px_1fr_300px]">
                        <aside className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p className="text-xs font-extrabold uppercase tracking-[0.2em] text-slate-400">
                                Shop all categories
                            </p>
                            <ul className="mt-4 space-y-2">
                                {categories.map((category) => (
                                    <li key={category}>
                                        <button
                                            type="button"
                                            className="flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-sm font-semibold text-slate-700 transition hover:bg-[#eef4ff] hover:text-[#1c4dac]"
                                        >
                                            <span>{category}</span>
                                            <span className="text-slate-400">›</span>
                                        </button>
                                    </li>
                                ))}
                            </ul>
                        </aside>

                        <article className="relative overflow-hidden rounded-3xl border border-[#c7daf8] bg-gradient-to-r from-[#0b2e71] via-[#14499d] to-[#2a74d6] p-8 text-white shadow-[0_30px_60px_-35px_rgba(15,63,159,0.7)]">
                            <div className="absolute inset-0 opacity-35">
                                <img
                                    src="/images/landing/hero-desk.jpg"
                                    alt="Open source workspace image"
                                    className="h-full w-full object-cover"
                                />
                            </div>
                            <div className="absolute inset-0 bg-gradient-to-r from-[#082660]/95 via-[#0c377f]/82 to-[#2a74d6]/40" />

                            <div className="relative z-10 max-w-[30rem]">
                                <p className="inline-flex rounded-full border border-white/30 bg-white/10 px-4 py-1 text-[11px] font-bold uppercase tracking-[0.16em]">
                                    Deal zone
                                </p>
                                <h1 className="mt-5 text-4xl font-extrabold tracking-tight sm:text-5xl">
                                    Build your next workstation with cleaner bundles.
                                </h1>
                                <p className="mt-4 text-sm leading-7 text-blue-100">
                                    Newegg-inspired shopping flow with faster discovery, stronger pricing contrast,
                                    and action-first product cards.
                                </p>

                                <div className="mt-6 flex flex-wrap gap-3">
                                    <button
                                        type="button"
                                        className="rounded-full bg-[#ff7b22] px-5 py-2.5 text-xs font-bold uppercase tracking-[0.14em] text-white transition hover:bg-[#ef6c12]"
                                    >
                                        Shop today
                                    </button>
                                    <button
                                        type="button"
                                        className="rounded-full border border-white/35 bg-white/10 px-5 py-2.5 text-xs font-bold uppercase tracking-[0.14em] text-white transition hover:bg-white/20"
                                    >
                                        View bundles
                                    </button>
                                </div>
                            </div>
                        </article>

                        <aside className="space-y-4">
                            {spotlightCards.map((card) => (
                                <article
                                    key={card.title}
                                    className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                                >
                                    <img src={card.image} alt={card.title} className="h-36 w-full object-cover" />
                                    <div className="p-4">
                                        <p className="text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#1a56b5]">
                                            {card.tag}
                                        </p>
                                        <h3 className="mt-2 text-sm font-bold text-slate-900">{card.title}</h3>
                                        <p className="mt-1 text-xs leading-5 text-slate-600">{card.copy}</p>
                                    </div>
                                </article>
                            ))}
                        </aside>
                    </section>

                    <section className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                        <div className="flex flex-wrap items-end justify-between gap-4">
                            <div>
                                <p className="text-xs font-extrabold uppercase tracking-[0.18em] text-slate-400">
                                    Today&apos;s deals
                                </p>
                                <h2 className="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">
                                    Flash Savings On Tech Workspace Picks
                                </h2>
                            </div>
                            <span className="rounded-full bg-[#eef4ff] px-4 py-2 text-xs font-bold uppercase tracking-[0.14em] text-[#1a56b5]">
                                Ends in 06h : 13m : 09s
                            </span>
                        </div>

                        <div className="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            {flashDeals.map((deal) => (
                                <article
                                    key={deal.title}
                                    className="group overflow-hidden rounded-2xl border border-slate-200 bg-white transition hover:-translate-y-1 hover:border-[#a8c5f5] hover:shadow-md"
                                >
                                    <div className="h-44 overflow-hidden bg-slate-100">
                                        <img
                                            src={deal.image}
                                            alt={deal.title}
                                            className="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                        />
                                    </div>
                                    <div className="p-4">
                                        <p className="text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-400">
                                            {deal.category}
                                        </p>
                                        <h3 className="mt-2 line-clamp-2 text-sm font-bold leading-6 text-slate-900">
                                            {deal.title}
                                        </h3>
                                        <div className="mt-3 flex items-center gap-2">
                                            <span className="text-xl font-extrabold text-[#0f3f9f]">{deal.price}</span>
                                            <span className="text-xs font-semibold text-slate-400 line-through">
                                                {deal.oldPrice}
                                            </span>
                                        </div>
                                        <button
                                            type="button"
                                            className="mt-4 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold uppercase tracking-[0.14em] text-slate-700 transition hover:border-[#2362c9] hover:bg-[#eef4ff] hover:text-[#1a56b5]"
                                        >
                                            Add to cart
                                        </button>
                                    </div>
                                </article>
                            ))}
                        </div>
                    </section>

                    <section className="grid gap-4 rounded-3xl border border-[#bed5f8] bg-gradient-to-r from-[#dbe8fc] via-[#eaf2ff] to-[#f3f7ff] p-5 sm:grid-cols-3 sm:p-7">
                        {quickSignals.map((signal) => (
                            <article
                                key={signal.title}
                                className="rounded-2xl border border-white/70 bg-white/70 p-4 shadow-sm"
                            >
                                <h3 className="text-sm font-extrabold uppercase tracking-[0.12em] text-[#1a56b5]">
                                    {signal.title}
                                </h3>
                                <p className="mt-2 text-sm leading-6 text-slate-600">{signal.copy}</p>
                            </article>
                        ))}
                    </section>
                </main>

                <footer className="border-t border-slate-200 bg-white/90">
                    <div className="mx-auto flex w-full max-w-[1240px] flex-col gap-3 px-4 py-6 text-xs text-slate-500 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                        <p>Powered by PlexusBiz Automate storefront experience.</p>
                        <p>Layout logic inspired by large-format ecommerce homepages.</p>
                    </div>
                </footer>
            </div>
        </>
    );
}
