import { Head, Link } from '@inertiajs/react';
import FrontendLayout from '@/Layouts/FrontendLayout';
import LandingPromoStrip from '@/Components/LandingPromoStrip';
import PromoArtwork from '@/Components/PromoArtwork';

const storeAsset = (path) => `/images/store/${path}`;
const ecommerceAsset = (path) => `/images/ecommerce/${path}`;
const productImage = (name) => storeAsset(`products/${name}.png`);
const bannerImage = (name) => storeAsset(`banners/${name}.jpg`);
const brandImage = (name) => storeAsset(`brands/${name}.png`);
const ecommerceProductImage = (name) => ecommerceAsset(`products/${name}.jpg`);
const ecommerceBannerImage = (name) => ecommerceAsset(`banners/${name}.jpg`);
const ecommerceCardImage = (name) => ecommerceAsset(`cards/${name}.jpg`);

const heroStats = [
    {
        value: 'Live',
        label: 'rotating promos and deal modules',
    },
    {
        value: 'Fast',
        label: 'search-first shopping flow',
    },
    {
        value: 'Clear',
        label: 'price contrast and dense product cards',
    },
];

const promoCards = [
    {
        eyebrow: 'Shell Shocker',
        title: 'Flash pricing on desktop parts and build kits.',
        copy: 'Limited quantities, strong contrast, and a visible call to action for rapid scanning.',
        artVariant: 'hero',
        pill: 'Ends soon',
        cta: 'See the drop',
        href: '#deals',
        tone: 'border-[#1d4b9d] bg-[#0b2e71] text-white',
        copyClass: 'text-blue-100',
        buttonClass: 'border-white/15 bg-white/10 text-white hover:bg-white/20',
    },
    {
        eyebrow: 'AI Ready',
        title: 'Monitors and peripherals that stay competitive.',
        copy: 'Work, play, and quick comparison features in a clean, product-driven panel.',
        artVariant: 'banner',
        pill: 'New arrivals',
        cta: 'Compare picks',
        href: '#deals',
        tone: 'border-[#bfd0f0] bg-white text-slate-900',
        copyClass: 'text-slate-600',
        buttonClass: 'border-[#d7e3f4] bg-[#f4f8ff] text-[#1a56b5] hover:border-[#ffb16d] hover:bg-[#fff3e8] hover:text-[#d75d00]',
    },
    {
        eyebrow: 'Build Tools',
        title: 'Plan the rig before you buy the first part.',
        copy: 'Keep the shopping rhythm focused on components, compatibility, and upgrades.',
        pill: 'PC Builder',
        cta: 'Open build tools',
        href: '#build',
        tone: 'border-[#ffd2a8] bg-gradient-to-br from-[#fff8f0] via-[#ffe8c8] to-[#ffd49f] text-slate-900',
        copyClass: 'text-slate-600',
        buttonClass: 'border-[#ffd2a8] bg-white/80 text-[#d75d00] hover:border-[#ffb16d] hover:bg-white',
        points: ['CPU', 'GPU', 'Board', 'RAM', 'PSU', 'Storage'],
    },
];

const dealItems = [
    {
        title: 'ABS Cyclone Aqua Gaming PC',
        category: 'Desktop PC',
        price: '$1,049.99',
        compare: '$1,399.99',
        save: 'Save 25%',
        badge: 'AI Ready',
        rating: '4.6',
        reviews: '209',
        image: productImage('pc-cyclone-aqua'),
        short: 'Windows 11 Home, Intel Core i5-14400F, RTX 5060, 32GB DDR4, 1TB NVMe SSD.',
    },
    {
        title: 'Samsung 24" S3 120Hz IPS Monitor',
        category: 'Displays',
        price: '$104.99',
        compare: '$139.99',
        save: 'Save 25%',
        badge: 'Top rated',
        rating: '4.7',
        reviews: '3',
        image: productImage('monitor-s3-120hz'),
        short: 'Full HD, 120Hz refresh, eye saver mode, and dual HDMI for work or gaming.',
    },
    {
        title: 'Logitech G915 X LIGHTSPEED Keyboard',
        category: 'Input Devices',
        price: '$194.99',
        compare: '$259.99',
        save: 'Save 25%',
        badge: 'Fast typing',
        rating: '4.5',
        reviews: '33',
        image: productImage('keyboard-g915'),
        short: 'Wireless full-size mechanical board with GL linear switches and RGB lighting.',
    },
    {
        title: 'Fractal Design Scape Wireless Headset',
        category: 'Audio PC',
        price: '$199.99',
        compare: '$219.99',
        save: 'Free gift',
        badge: 'Comfort fit',
        rating: '4.3',
        reviews: '21',
        image: productImage('headset-scape'),
        short: 'Immersive audio, Bluetooth 5.3, charging stand, and a long battery life.',
    },
    {
        title: 'UGREEN NASync DXP2800 2-Bay NAS',
        category: 'Storage',
        price: '$369.99',
        compare: '$399.99',
        save: 'P-code offer',
        badge: 'Storage',
        rating: '4.9',
        reviews: '7',
        image: productImage('nas-dxp2800'),
        short: 'Intel N100 quad-core, 8GB DDR5, two SATA bays, two M.2 slots, and 2.5GbE.',
    },
    {
        title: 'WD Red Plus 10TB NAS HDD',
        category: 'Hard Drives',
        price: '$299.99',
        compare: '$349.99',
        save: 'Save 14%',
        badge: 'PlexusBiz Select',
        rating: '3.9',
        reviews: '1711',
        image: productImage('hdd-red-plus'),
        short: '7200 RPM class, CMR, 512MB cache, and a 3-year limited warranty.',
    },
];

const featuredBrands = [
    'AMD',
    'ASUS',
    'Intel',
    'MSI',
    'NVIDIA',
    'Samsung',
    'Seagate',
    'Logitech',
    'Corsair',
    'UGREEN',
];

const featureTiles = [
    {
        eyebrow: 'Components & Storage',
        title: 'Build the core first.',
        copy: 'CPU, boards, SSDs, memory, cooling, and power supplies in one clear flow.',
        action: 'Browse parts',
        href: '#deals',
        tone: 'border-[#1d4b9d] bg-gradient-to-br from-[#0b3d91] via-[#1559b8] to-[#2d79da] text-white',
        copyClass: 'text-blue-100',
        actionClass: 'border-white/15 bg-white/10 text-white hover:bg-white/20',
    },
    {
        eyebrow: 'Gaming Setup',
        title: 'Frames, latency, and comfort.',
        copy: 'Displays, mice, headsets, and controllers tuned around a better play loop.',
        action: 'See gaming picks',
        href: '#brands',
        tone: 'border-[#bfd0f0] bg-white text-slate-900',
        copyClass: 'text-slate-600',
        actionClass: 'border-[#d7e3f4] bg-[#f4f8ff] text-[#1a56b5] hover:border-[#ffb16d] hover:bg-[#fff3e8] hover:text-[#d75d00]',
    },
    {
        eyebrow: 'Workspace Essentials',
        title: 'A cleaner desk, a sharper workflow.',
        copy: 'Docks, printers, storage, and small accessories that remove daily friction.',
        action: 'Explore workspace',
        href: '#footer',
        tone: 'border-[#ffd2a8] bg-gradient-to-br from-[#fff8f0] via-[#ffe9cf] to-[#ffd7a8] text-slate-900',
        copyClass: 'text-slate-600',
        actionClass: 'border-[#ffd2a8] bg-white/80 text-[#d75d00] hover:border-[#ffb16d] hover:bg-white',
    },
];

const footerSections = [
    {
        title: 'Shop',
        links: [
            { label: "Today's deals", href: '#deals' },
            { label: 'Featured brands', href: '#brands' },
            { label: 'PC Builder', href: '#build' },
            { label: 'Browse categories', href: '#hero' },
        ],
    },
    {
        title: 'Explore',
        links: [
            { label: 'Flash deals', href: '#deals' },
            { label: 'Workspace picks', href: '#featured' },
            { label: 'Price alerts', href: '#hero' },
            { label: 'Product compare', href: '#build' },
        ],
    },
    {
        title: 'Support',
        links: [
            { label: 'Help center', href: '#footer' },
            { label: 'Shipping info', href: '#deals' },
            { label: 'Return policy', href: '#footer' },
            { label: 'Privacy choices', href: '#footer' },
        ],
    },
    {
        title: 'About',
        links: [
            { label: 'PlexusBiz story', href: '#footer' },
            { label: 'Partner with us', href: '#footer' },
            { label: 'Careers', href: '#footer' },
            { label: 'Contact', href: '#footer' },
        ],
    },
];

const heroBackground = ecommerceBannerImage('banner_04');

const heroImageCards = [
    { image: ecommerceCardImage('card_01') },
    { image: ecommerceCardImage('card_04') },
    { image: ecommerceCardImage('card_11') },
];

const lowerBannerImages = [
    'banner_01',
    'banner_06',
].map(ecommerceBannerImage);

function ProductImageFrame({ src, className = 'aspect-[4/3]', imageClassName = 'p-3' }) {
    return (
        <div className={`relative overflow-hidden bg-white ${className}`}>
            <img
                src={src}
                alt=""
                className={`absolute inset-0 h-full w-full object-contain ${imageClassName}`}
                loading="lazy"
            />
        </div>
    );
}

function ImageOnlyPromoCard({ image }) {
    return (
        <article className="h-full min-h-[160px] overflow-hidden rounded-[22px] border border-[#d7e3f4] bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <img src={image} alt="" className="h-full w-full object-cover" loading="lazy" />
        </article>
    );
}

function BannerImageGrid({ banners }) {
    return (
        <section className="grid gap-[15px] md:grid-cols-2">
            {banners.map((banner) => (
                <article key={banner} className="overflow-hidden rounded-[12px] bg-white shadow-sm">
                    <img src={banner} alt="" className="h-[210px] w-full object-cover sm:h-[240px]" loading="lazy" />
                </article>
            ))}
        </section>
    );
}

function HeroStat({ value, label }) {
    return (
        <div className="rounded-[22px] border border-white/15 bg-white/10 p-4 backdrop-blur">
            <strong className="block text-xl font-black tracking-[-0.04em] text-white">{value}</strong>
            <span className="mt-1 block text-xs leading-5 text-blue-100">{label}</span>
        </div>
    );
}

function PromoCard({ card }) {
    return (
        <article className={`overflow-hidden rounded-[28px] border p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md ${card.tone}`}>
            <div className="flex items-start justify-between gap-3">
                <div>
                    <p className="text-[10px] font-extrabold uppercase tracking-[0.18em] text-[#ffb16d]">
                        {card.eyebrow}
                    </p>
                    <h3 className={`mt-2 text-lg font-bold leading-6 tracking-[-0.03em] ${card.copyClass === 'text-blue-100' ? 'text-white' : 'text-slate-900'}`}>
                        {card.title}
                    </h3>
                </div>
                <span
                    className={`rounded-full px-3 py-1 text-[10px] font-extrabold uppercase tracking-[0.16em] ${
                        card.copyClass === 'text-blue-100'
                            ? 'bg-white/15 text-white'
                            : 'bg-[#eef4ff] text-[#0b3d91]'
                    }`}
                >
                    {card.pill}
                </span>
            </div>

            <p className={`mt-3 text-sm leading-6 ${card.copyClass}`}>{card.copy}</p>

            {card.artVariant ? (
                <div className="mt-4 overflow-hidden rounded-[22px]">
                    <PromoArtwork variant={card.artVariant} className="aspect-[16/10]" framed={false} />
                </div>
            ) : (
                <div className="mt-4 grid grid-cols-2 gap-2">
                    {card.points.map((point) => (
                        <div key={point} className="rounded-2xl bg-white/70 p-3 text-xs font-bold text-slate-700">
                            {point}
                        </div>
                    ))}
                </div>
            )}

            <a
                href={card.href}
                className={`mt-4 inline-flex w-full items-center justify-center rounded-2xl border px-4 py-2 text-xs font-extrabold uppercase tracking-[0.16em] transition ${card.buttonClass}`}
            >
                {card.cta}
            </a>
        </article>
    );
}

function DealCard({ item }) {
    return (
        <article className="group flex h-full flex-col overflow-hidden rounded-[28px] border border-[#d7e3f4] bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-[0_20px_50px_-24px_rgba(10,35,84,0.35)]">
            <div className="relative p-4">
                <div className="overflow-hidden rounded-[22px] border border-[#eef3fb] bg-slate-50">
                    <ProductImageFrame
                        src={item.image}
                        className="aspect-[4/3] transition duration-500 group-hover:scale-[1.03]"
                    />
                </div>
                <span className="absolute left-6 top-6 rounded-full bg-[#0b3d91] px-3 py-1 text-[10px] font-extrabold uppercase tracking-[0.16em] text-white shadow-sm">
                    {item.badge}
                </span>
                <span className="absolute right-6 top-6 rounded-full bg-white/95 px-3 py-1 text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#ff7b22] shadow-sm">
                    {item.save}
                </span>
            </div>

            <div className="flex flex-1 flex-col px-4 pb-4">
                <p className="text-[10px] font-extrabold uppercase tracking-[0.18em] text-slate-400">{item.category}</p>
                <h3 className="mt-2 min-h-[3.25rem] text-sm font-bold leading-6 text-slate-900">
                    {item.title}
                </h3>
                <p className="mt-2 text-xs leading-5 text-slate-600">{item.short}</p>

                <div className="mt-3 flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <span className="rounded-full bg-[#eef4ff] px-2.5 py-1 font-bold text-[#1a56b5]">
                        {item.rating}
                    </span>
                    <span>{item.reviews} reviews</span>
                </div>

                <div className="mt-4 flex items-end gap-2">
                    <span className="text-2xl font-extrabold tracking-tight text-[#0d3a91]">
                        {item.price}
                    </span>
                    <span className="pb-0.5 text-xs font-semibold text-slate-400 line-through">
                        {item.compare}
                    </span>
                </div>

                <button
                    type="button"
                    className="mt-4 rounded-2xl border border-[#d7e3f4] bg-[#f4f8ff] px-4 py-2 text-xs font-extrabold uppercase tracking-[0.16em] text-[#1a56b5] transition hover:border-[#ffb16d] hover:bg-[#fff3e8] hover:text-[#d75d00]"
                >
                    Add to cart
                </button>
            </div>
        </article>
    );
}

function BrandPill({ brand }) {
    return (
        <div className="rounded-2xl border border-[#d4e0f3] bg-white px-4 py-3 text-center text-sm font-extrabold tracking-[0.12em] text-slate-700 shadow-sm">
            {brand}
        </div>
    );
}

function FeatureTile({ tile }) {
    return (
        <article className={`overflow-hidden rounded-[28px] border p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md ${tile.tone}`}>
            <p className={`text-[10px] font-extrabold uppercase tracking-[0.18em] ${tile.copyClass === 'text-blue-100' ? 'text-[#ffd6af]' : 'text-[#1a56b5]'}`}>
                {tile.eyebrow}
            </p>
            <h3 className={`mt-2 text-2xl font-black tracking-[-0.04em] ${tile.copyClass === 'text-blue-100' ? 'text-white' : 'text-slate-900'}`}>
                {tile.title}
            </h3>
            <p className={`mt-3 max-w-md text-sm leading-7 ${tile.copyClass}`}>{tile.copy}</p>
            <a
                href={tile.href}
                className={`mt-5 inline-flex items-center justify-center rounded-full border px-5 py-2.5 text-xs font-extrabold uppercase tracking-[0.16em] transition ${tile.actionClass}`}
            >
                {tile.action}
            </a>
        </article>
    );
}

function FooterColumn({ section }) {
    return (
        <div>
            <h3 className="text-xs font-extrabold uppercase tracking-[0.2em] text-white">
                {section.title}
            </h3>
            <ul className="mt-4 space-y-3">
                {section.links.map((link) => (
                    <li key={link.label}>
                        <a href={link.href} className="text-sm text-slate-400 transition hover:text-white">
                            {link.label}
                        </a>
                    </li>
                ))}
            </ul>
        </div>
    );
}

function ProductsIcon({ className = '' }) {
    return (
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" className={className}>
            <path
                d="M4.5 8.5 12 4l7.5 4.5L12 13 4.5 8.5Z"
                stroke="currentColor"
                strokeWidth="1.8"
                strokeLinejoin="round"
            />
            <path
                d="M4.5 8.5V16L12 20.5 19.5 16V8.5"
                stroke="currentColor"
                strokeWidth="1.8"
                strokeLinejoin="round"
            />
            <path d="M12 13v7.5" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" />
        </svg>
    );
}

function OrdersIcon({ className = '' }) {
    return (
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" className={className}>
            <rect x="6.5" y="4.5" width="11" height="15" rx="2.25" stroke="currentColor" strokeWidth="1.8" />
            <path d="M9 4.5V3.25h6V4.5" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" />
            <path d="M9.2 10h5.6M9.2 13h5.6M9.2 16h3.4" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" />
            <path d="m8.6 10.2.8.8 1.4-1.5" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" />
        </svg>
    );
}

function InvoicesIcon({ className = '' }) {
    return (
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" className={className}>
            <path
                d="M7 3.75h7.75L18 7v13.25a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-16a1 1 0 0 1 1-1Z"
                stroke="currentColor"
                strokeWidth="1.8"
                strokeLinejoin="round"
            />
            <path d="M14.75 3.75V7H18" stroke="currentColor" strokeWidth="1.8" strokeLinejoin="round" />
            <path d="M9 11h6M9 14h6M9 17h4" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" />
        </svg>
    );
}

function SupportIcon({ className = '' }) {
    return (
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" className={className}>
            <path d="M6.5 12a5.5 5.5 0 0 1 11 0v2.5" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" />
            <path
                d="M5.5 13.5v2.25a2.25 2.25 0 0 0 2.25 2.25H9v-6H7.75a2.25 2.25 0 0 0-2.25 2.25Z"
                stroke="currentColor"
                strokeWidth="1.8"
                strokeLinejoin="round"
            />
            <path
                d="M18.5 13.5v2.25A2.25 2.25 0 0 1 16.25 18H15v-6h1.25a2.25 2.25 0 0 1 2.25 2.25Z"
                stroke="currentColor"
                strokeWidth="1.8"
                strokeLinejoin="round"
            />
            <path d="M11 18.7h2" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" />
        </svg>
    );
}

const sidebarCategoryTitles = [
    'Components & Storage',
    'Computer Systems',
    'Computer Peripherals',
    'Server & Components',
    'Appliances',
    'Electronics',
    'Gaming & VR',
    'Networking',
    'Smart Home & Security',
    'Office Solutions',
    'Software & Services',
    'Automotive & Tools',
    'Home & Outdoors',
    'Health & Sports',
    'Toys, Drones & Maker',
];

const sidebarCategoryIcons = [ProductsIcon, OrdersIcon, InvoicesIcon, SupportIcon];

const marketplaceShortcutItems = sidebarCategoryTitles.map((title, index) => ({
    icon: sidebarCategoryIcons[index % sidebarCategoryIcons.length],
    title,
    href: '#deals',
}));

const comboBundlesPrimary = [
    {
        brand: 'Intel',
        savings: 'Combo up savings $40.00',
        price: '$379.97',
        compare: '$419.97',
        cta: 'Build with it',
        accent: 'from-[#e8f1ff] to-[#cfe0ff]',
        items: [
            {
                title: 'Intel Core i5-12600KF - Core i5 12th Gen Alder Lake 10-Core (6P+4E) 3.7 GHz LGA 1700 125W Desktop Processor - BX8071512600KF',
                image: productImage('cpu-intel-i5'),
            },
            {
                title: 'ASUS B760M-AYW WIFI D4 II Intel B760 (LGA 1700) microATX mATX motherboard, PCIe 5.0 x16 Support, two M.2 slots, DDR4, Realtek 2.5Gb Ethernet, Wi-Fi 6, HDMI, SATA 6 Gbps, front USB 5Gbps, Aura Sync',
                image: productImage('board-asus-b760'),
            },
            {
                title: 'CORSAIR Vengeance LPX 16GB (2 x 8GB) 288-Pin PC RAM DDR4 3200 (PC4 25600) Desktop Memory Model CMK16GX4M2E3200C16',
                image: productImage('ram-vengeance-lpx'),
            },
        ],
    },
    {
        brand: 'Desktop Computers',
        savings: 'Combo up savings $70.00',
        price: '$1,279.97',
        compare: '$1,349.97',
        cta: 'Add to cart',
        accent: 'from-[#dfe9ff] to-[#c8dbff]',
        items: [
            {
                title: 'ABS Cyclone Aqua Gaming PC - Windows 11 - Intel Core i5-14400F - Nvidia GeForce RTX 5060 8GB - DLSS 4 - 32GB DDR4 3200 - 1TB M.2 NVMe SSD - CA14400F50605',
                image: productImage('pc-cyclone-bundle'),
            },
            {
                title: 'Samsung 24" S3 (S32GF) Full HD 120Hz IPS Flicker Free, Eye Saver Mode 2x HDMI ports LCD Computer Monitor for both Work and Gaming LS24F320GANXZA',
                image: productImage('monitor-s3-120hz'),
            },
            {
                title: 'Logitech G915 X LIGHTSPEED Full-size Wireless Mechanical Gaming Keyboard - GL Red Linear Switches, RGB Backlighting - Black',
                image: productImage('keyboard-g915'),
            },
        ],
    },
    {
        brand: 'Shell Shocker',
        savings: 'Flash pricing and limited drops',
        price: '$1,399.99',
        compare: '$1,799.99',
        cta: 'See all',
        accent: 'from-[#0b2e71] to-[#114b9f]',
        tone: 'dark',
        note: '+ $20 off w/ promo code SSF6252, limited offer',
        items: [
            {
                title: 'ABS Cyclone Aqua Gaming PC - Windows 11 - Intel Core i7-14700F - Nvidia GeForce RTX 5060 - DLSS 4 - 32GB DDR5 6000 - 1TB M.2 SSD',
                image: productImage('pc-shell-shocker'),
            },
        ],
    },
];

const comboBundlesSecondary = [
    {
        brand: 'AMD',
        savings: 'Combo up savings $78.00',
        price: '$297.98',
        compare: '$375.98',
        cta: 'Build with it',
        accent: 'from-[#eef3ff] to-[#dce7ff]',
        items: [
            {
                title: 'AMD Ryzen 5 5500 - Ryzen 5 5000 Series Cezanne (Zen 3) 6-Core 3.6 GHz Socket AM4 65W No Integrated Graphics Desktop CPU Processor - 100-100000457BOX',
                image: productImage('cpu-ryzen-5500'),
            },
            {
                title: 'Asus ROG Strix B550-F Gaming WiFi II AMD AM4 (3rd Gen Ryzen) ATX Gaming Motherboard (PCIe 4.0,WiFi 6E, 2.5Gb LAN, BIOS Flashback, HDMI 2.1, Addressable Gen 2 RGB Header and Aura Sync)',
                image: productImage('board-rog-b550'),
            },
            {
                title: 'CORSAIR Vengeance LPX 16GB (2 x 8GB) 288-Pin PC RAM DDR4 3200 (PC4 25600) Desktop Memory Model CMK16GX4M2E3200C16',
                image: productImage('ram-ddr4-blue'),
            },
        ],
    },
    {
        brand: 'NAS',
        savings: 'Combo up savings $45.00',
        price: '$924.97',
        compare: '$969.97',
        cta: 'Add to cart',
        accent: 'from-[#edf6ff] to-[#dbe9ff]',
        items: [
            {
                title: 'UGREEN NASync DXP2800, 2-Bay NAS with Intel N100 Quad-Core CPU (Up to 3.4GHz) 8GB DDR5, 2x M.2 PCIe Slots, 2.5GbE Port (Diskless)',
                image: productImage('nas-dxp-storage'),
            },
            {
                title: 'WD Red Plus 10TB NAS Hard Disk Drive - 7200 RPM Class SATA 6Gb/s, CMR, 512MB Cache, 3.5 Inch - WD100EFGX',
                image: productImage('hdd-red-plus-single'),
            },
            {
                title: 'WD Red Plus 10TB NAS Hard Disk Drive - 7200 RPM Class SATA 6Gb/s, CMR, 512MB Cache, 3.5 Inch - WD100EFGX',
                image: productImage('hdd-red-plus-duo'),
            },
        ],
    },
];

const promoShowcaseSlides = [
    {
        eyebrow: 'Memory Finder',
        title: 'Select the Right RAM',
        copy: 'Compare speed, latency, and capacity in a polished slider made for quick scanning.',
        cta: 'Shop now',
        href: '#deals',
        artVariant: 'memory',
        imageUrl: bannerImage('memory-finder'),
    },
    {
        eyebrow: 'Massive Mega Sale',
        title: 'Huge Savings on Notebooks & Gaming Laptops',
        copy: 'Keep the visual strong, dark, and product-driven so the call to action stays clear.',
        cta: 'Shop now',
        href: '#deals',
        artVariant: 'banner',
        imageUrl: bannerImage('laptop-sale'),
    },
    {
        eyebrow: 'Best Sellers',
        title: 'Our most popular products, updated frequently.',
        copy: 'A third slide keeps the carousel feeling alive without crowding the page.',
        cta: 'See all',
        href: '#deals',
        artVariant: 'hero',
        imageUrl: bannerImage('top-products'),
    },
];

const bestDealItems = [
    {
        title: 'ABS Cyclone Aqua Gaming PC - Windows 11 - Intel Core 5 120 - Arc A580 8GB - 16GB DDR4 3200 - 1TB M.2 SSD',
        category: 'Desktop PC',
        price: '$899.99',
        compare: '$1,149.99',
        save: '21% off',
        badge: 'PlexusBiz Select',
        rating: '4.5',
        reviews: '22',
        image: productImage('pc-arc-a580'),
        short: 'A clean desktop tower bundle with game-ready graphics and fast storage.',
    },
    {
        title: 'CORSAIR Vengeance RGB Pro 32GB (2 x 16GB) 288-Pin PC RAM DDR4 3200 (PC4 25600) Desktop Memory Model CMW32GX4M2E3200C16',
        category: 'Memory (RAM)',
        price: '$289.99',
        compare: '',
        save: '',
        badge: 'PlexusBiz Select',
        rating: '4.7',
        reviews: '135',
        image: productImage('ram-rgb-pro'),
        short: 'RGB desktop memory with a proven 3200 MHz profile and 32GB capacity.',
    },
    {
        title: 'SAPPHIRE NITRO+ Radeon RX 9070 XT Graphics Card 11348-01-20G',
        category: 'Graphics Cards',
        price: '$799.99',
        compare: '$949.99',
        save: '15% off',
        badge: 'Top rated',
        rating: '4.5',
        reviews: '365',
        image: productImage('gpu-sapphire-nitro'),
        short: 'High-end Radeon card with strong cooling and a premium factory design.',
    },
    {
        title: 'MSI Vector - LCD 16" QHD+ GeForce RTX 5080 Laptop GPU - Intel Core Ultra 9 275HX - 16GB Memory - 1 TB SSD - Windows 11 Home Gaming Laptop - 240 Hz',
        category: 'Laptops',
        price: '$2,199.00',
        compare: '$2,499.00',
        save: '12% off',
        badge: 'AI Ready',
        rating: '4.8',
        reviews: '92',
        image: productImage('laptop-msi-vector'),
        short: 'A desktop-replacement gaming laptop with a fast QHD+ panel and RTX 5080.',
    },
    {
        title: 'LIAN LI LANCOOL 216RX Black Steel / Tempered Glass ATX Mid Tower Computer Case ,2x 16 cm ARGB Fans Included ----LANCOOL 216RX',
        category: 'Cases',
        price: '$99.99',
        compare: '',
        save: 'Free Gift',
        badge: '',
        rating: '4.6',
        reviews: '308',
        image: productImage('case-lancool'),
        short: 'A roomy ATX mid tower with airflow-focused front design and RGB fans.',
    },
];

const officeDealItems = [
    {
        title: 'Lenovo V15 Gen 4 15.6" Laptop AMD Ryzen 7 7730U 16GB RAM 1TB SSD Windows 11 Home (83CR003UUS)',
        category: 'Laptops',
        price: '$599.99',
        compare: '$799.99',
        save: 'Save 25%',
        badge: 'PlexusBiz Select',
        rating: '4.6',
        reviews: '3',
        image: productImage('laptop-lenovo-v15'),
        short: 'A value-focused productivity laptop with a Ryzen 7 CPU and 1TB SSD.',
    },
    {
        title: 'MSI Venture 16 AI Touchscreen Laptop Intel Core Ultra 9 285H 32GB RAM 1TB SSD (A2HMTG-015US)',
        category: 'AI PC',
        price: '$1,099.00',
        compare: '$1,199.99',
        save: 'Save 8%',
        badge: 'AI Ready',
        rating: '4.7',
        reviews: '6',
        image: productImage('laptop-msi-venture'),
        short: 'A touchscreen AI-ready notebook with plenty of memory for multitasking.',
    },
    {
        title: 'Acer Aspire Go 15, 15.6" Full HD IPS Display, AMD Ryzen 7 7730U Octa-Core Processor, AMD Radeon Graphics, 32GB DDR4 Memory, 1TB PCIe Gen4 SSD, Windows 11 Home AG15-42P-R3GM',
        category: 'Laptops',
        price: '$699.99',
        compare: '$899.99',
        save: 'Save 22%',
        badge: 'AI Ready',
        rating: '4.4',
        reviews: '92',
        image: productImage('laptop-acer-aspire'),
        short: 'A lightweight all-day laptop with a sharp FHD panel and a big SSD.',
    },
];

const landingPhotos = {
    heroDesk: bannerImage('gaming-setup'),
    dealImac: productImage('kitchen-appliance'),
    dealTwoMonitor: productImage('mesh-router'),
    dealPhotographerDesk: bannerImage('workspace-gift'),
};

const categoryPanels = [
    {
        title: 'Components & Storage',
        tiles: [
            {
                label: 'CPU',
                imageUrl: productImage('cpu-category'),
                imageFit: 'contain',
            },
            {
                label: 'Graphics Card',
                imageUrl: productImage('gpu-category'),
                imageFit: 'contain',
            },
            {
                label: 'SSD',
                imageUrl: productImage('ssd-category'),
                imageFit: 'contain',
            },
            {
                label: 'Hard Drive',
                imageUrl: productImage('hdd-category'),
                imageFit: 'contain',
            },
        ],
        tileGridClass: 'mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2',
        tileAspectClass: 'aspect-[4/3]',
    },
    {
        title: 'Gamer Paradise',
        bannerArtVariant: 'gamingBanner',
        bannerImageUrl: landingPhotos.heroDesk,
        bannerImageFit: 'cover',
        bannerLabel: 'Shop Deals',
        bannerCopy: 'Power. Precision. Performance.',
        bannerClassName: 'min-h-[240px] sm:min-h-[280px]',
    },
    {
        title: 'Entertainment & More',
        tiles: [
            {
                label: 'TV & Video',
                imageUrl: productImage('tv-video'),
                imageFit: 'contain',
            },
            {
                label: 'Gift Cards',
                imageUrl: landingPhotos.dealPhotographerDesk,
                imageFit: 'cover',
            },
        ],
        tileGridClass: 'mt-4 grid grid-cols-1 gap-3',
        tileAspectClass: 'aspect-[16/9]',
    },
    {
        title: 'Home Tech Essentials',
        tiles: [
            {
                label: 'Home Audio',
                imageUrl: productImage('home-audio'),
                imageFit: 'contain',
            },
            {
                label: 'Kitchen Appliances',
                imageUrl: landingPhotos.dealImac,
                imageFit: 'cover',
            },
            {
                label: 'Security Cameras',
                imageUrl: landingPhotos.dealPhotographerDesk,
                imageFit: 'cover',
            },
            {
                label: 'Wireless Networking',
                imageUrl: landingPhotos.dealTwoMonitor,
                imageFit: 'cover',
            },
        ],
        tileGridClass: 'mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2',
        tileAspectClass: 'aspect-[4/3]',
    },
];

const desktopPcItems = [
    {
        title: 'Acer Aspire Desktop Computer Intel Core i5-14400, Intel UHD Graphics 730, 8GB DDR5, 512 GB PCIe SSD, Windows 11 Home 64-bit - TC-1775-UR11',
        category: 'Desktop PC',
        price: '$549.99',
        compare: '$599.99',
        save: 'Save 8%',
        badge: 'PlexusBiz Select',
        rating: '4.5',
        reviews: '19',
        image: productImage('desktop-acer'),
        short: 'A compact office-ready tower with Intel Core i5 and integrated graphics.',
    },
    {
        title: 'Dell Pro QCS1250 Slim PC - Intel Core i5 14th Gen i5-14500 - 16 GB - 512 GB SSD - Windows 11 Pro - Intel DDR5 SDRAM - 180W M1XXF',
        category: 'Desktop PC',
        price: '$829.99',
        compare: '$999.99',
        save: 'Save 17%',
        badge: '',
        rating: '4.4',
        reviews: '17',
        image: productImage('desktop-dell-slim'),
        short: 'Slim business tower for desks that need a lot of performance in little space.',
    },
    {
        title: 'HIGOLEPC Mini PC Windows 11 Pro Intel Celeron N4100 4GB/64GB, Micro Computer Stick PC USB PD3.0 and HDMI 4K Gigabit Ethernet, WiFi 5.0, BT 4.2',
        category: 'Mini PC',
        price: '$199.00',
        compare: '',
        save: 'See price in cart',
        badge: '',
        rating: '4.2',
        reviews: '10',
        image: productImage('mini-pc-stick'),
        short: 'A tiny stick PC for basic office tasks and 4K media playback.',
    },
    {
        title: 'HP Desktop PC OmniDesk M02-0127c AMD Ryzen 7 8700G 32GB RAM 1TB PCIe SSD AMD Radeon 780M Windows 11 Home',
        category: 'Desktop PC',
        price: '$639.99',
        compare: '$749.99',
        save: 'Save 14%',
        badge: '',
        rating: '4.7',
        reviews: '1',
        image: productImage('desktop-hp-omni'),
        short: 'A modern all-in-one style tower with Ryzen 7 and integrated Radeon 780M graphics.',
    },
    {
        title: 'Dell OptiPlex 5060 - Windows 11 Desktop Computer | Intel i5-8500 Six Core (4.3GHz Turbo) | 16GB DDR4 RAM | 1TB SSD | WiFi + Bluetooth | RGB Mouse + Keyboard | 24" 1080p Monitor',
        category: 'Desktop PC',
        price: '$402.00',
        compare: '$405.00',
        save: '',
        badge: '',
        rating: '4.3',
        reviews: '1',
        image: productImage('desktop-bundle-monitor'),
        short: 'A complete desktop bundle for office or student use.',
    },
];

const memoryItems = [
    {
        title: 'AquaFlow 240mm ARGB Liquid CPU Cooler with dual PWM fans',
        price: '$249.99',
        compare: '',
        save: '',
        badge: '',
        image: productImage('shelf-cooling-01'),
    },
    {
        title: 'FrostCore 360mm AIO Liquid Cooler with braided tubing',
        price: '$439.99',
        compare: '$483.99',
        save: 'Save 9%',
        badge: '',
        image: productImage('shelf-cooling-02'),
    },
    {
        title: 'High-pressure 120mm ARGB cooling fan triple pack',
        price: '$133.08',
        compare: '',
        save: '',
        badge: '',
        image: productImage('shelf-cooling-03'),
    },
    {
        title: 'Slim 280mm radiator kit for compact gaming builds',
        price: '$449.99',
        compare: '$494.99',
        save: 'Save 9%',
        badge: '',
        image: productImage('shelf-cooling-04'),
    },
    {
        title: 'HydroPulse LCD pump block with addressable RGB lighting',
        price: '$99.99',
        compare: '$129.99',
        save: 'Save 23%',
        badge: '',
        image: productImage('shelf-cooling-05'),
    },
    {
        title: 'Copper cold plate liquid cooling maintenance kit',
        price: '$13.99',
        compare: '$19.99',
        save: 'Save 30%',
        badge: '',
        image: productImage('shelf-cooling-06'),
    },
];

const electronicsItems = [
    {
        title: 'Soundcore by Anker, Space One, Active Noise Cancelling Headphones, 2X Stronger Voice Reduction, 40H ANC Playtime, App Control, LDAC Hi-Res Wireless Audio, Comfortable Fit, Clear Calls, Bluetooth 5.3',
        price: '$99.99',
        compare: '',
        save: '',
        image: productImage('headset-scape'),
    },
    {
        title: 'Team 32GB microSDHC UHS-I/U1 Class 10 Memory Card with Adapter, Speed Up to 100MB/s (TUSDH32GCL10U03)',
        price: '$13.99',
        compare: '$14.99',
        save: 'Save 6%',
        image: productImage('sd-card'),
    },
    {
        title: 'Rosewill 70W Retractable USB-C Wall Charger with Built-in Cable, 3-Port GaN 3 Fast Charging, Foldable Design & Real-Time TFT Display',
        price: '$19.99',
        compare: '$49.99',
        save: 'Save 60%',
        image: productImage('charger-usbc'),
    },
    {
        title: 'NVIDIA SHIELD Android TV Pro - 4K HDR Streaming Media Player - High Performance, Dolby Vision, 3GB RAM, 2 x USB, Google Assistant Built-In, Works with Alexa',
        price: '$199.00',
        compare: '',
        save: '',
        image: productImage('shield-tv'),
    },
];

const moreToConsiderItems = [
    {
        title: 'CoreXY enclosed 3D printer with high-speed auto leveling',
        price: '$619.99',
        compare: '$699.99',
        save: 'Save 11%',
        image: productImage('printer-3d-corexy'),
    },
    {
        title: 'Desktop laser engraver with air assist and camera preview',
        price: '$339.99',
        compare: '',
        save: '',
        image: productImage('printer-engraver'),
    },
    {
        title: 'Resin 3D printer starter kit with wash and cure station',
        price: '$146.80',
        compare: '$249.00',
        save: 'Save 41%',
        image: productImage('printer-resin'),
    },
    {
        title: 'Matte PLA filament bundle for prototyping and engraving jigs',
        price: 'Shop Now',
        compare: '',
        save: '',
        image: productImage('filament-spool'),
    },
    {
        title: 'Compact maker control board with silent stepper drivers',
        price: '$89.99',
        compare: '$119.99',
        save: 'Save 25%',
        image: productImage('sas-adapter'),
    },
    {
        title: '850W maker workstation power supply for printers and CNC tools',
        price: '$129.99',
        compare: '$159.99',
        save: 'Save 18%',
        image: productImage('psu-850w'),
    },
];

const communityInsights = [
    'Join ASUS for the ROG 20th Anniversary @ PlexusBiz Gamer Zone (Diamond Bar, CA) May 9, 11AM-3PM',
    'Steam Controller: A New Revision Ten Years in the Making',
    'Upgrade Your Build: Cougar FV150 & Airface Pure Pro on Sale',
    'PlexusBiz Golden Dragon Question: What would you name your Golden Dragon? Comment down below for 5 entries!',
];

const shoppingTools = [
    { title: 'PC Builder', copy: 'Compare parts and save builds.', imageUrl: bannerImage('tool-pc-builder') },
    { title: 'NAS Builder', copy: 'Plan disks and storage tiers.', imageUrl: bannerImage('tool-nas-builder') },
    { title: 'PC Upgrader', copy: 'BETA upgrade recommendations.', imageUrl: bannerImage('tool-pc-upgrader') },
    { title: 'Gaming PC Finder', copy: 'Find a prebuilt that fits.', imageUrl: bannerImage('tool-gaming-finder') },
    { title: 'Server Configurator', copy: 'Build around server needs.', imageUrl: bannerImage('tool-server-config') },
    { title: 'PSU Wattage Calculator', copy: 'Estimate wattage fast.', imageUrl: bannerImage('tool-psu-calculator') },
    { title: 'ASUS NUC Configurator', copy: 'Compact, powerful, AI ready*', imageUrl: bannerImage('tool-nuc-config') },
    { title: 'Laptop Finder', copy: 'Match size and performance.', imageUrl: bannerImage('tool-laptop-finder') },
    { title: 'Memory Finder', copy: 'Sort RAM by speed and size.', imageUrl: bannerImage('tool-memory-finder') },
    { title: 'Network Builder', copy: 'Switches, routers, and Wi-Fi.', imageUrl: bannerImage('tool-network-builder') },
];

const popularKeywords = [
    'rtx',
    'AMD',
    '9800X3D',
    'PS5',
    'Fractal Design',
    '4090',
    'ssd',
    'RTX 5080',
    'Intel',
    'laptop',
    '7800x3d',
    'G.Skill',
    'MSI',
    't-force',
    '7600X',
    '7900xtx',
    'gaming pc',
    'power supply',
    '4k 2K Monitor',
    'wd black',
    'graphics card',
    'monitor',
    'corsair case',
    'ddr5',
    'cpu',
    'asus psu',
    'motherboard',
    'cpu cooler',
    'gaming monitor',
    'ram',
    'keyboard',
    'Windows 11 Home',
    'oled monitor',
    'gigabyte',
    'Gaming PC',
    'ai ready',
    'Windows 11 Pro',
    'Lian Li',
    'flare x5',
    'Aspire',
    'Antec',
    'copilot+ pc',
    'touchscreen',
];

const brandLogoItems = [
    { name: 'ASUS', logo: brandImage('asus') },
    { name: 'AMD', logo: brandImage('amd') },
    { name: 'ASRock', logo: brandImage('asrock') },
    { name: 'GIGABYTE', logo: brandImage('gigabyte') },
    { name: 'Intel', logo: brandImage('intel') },
    { name: 'MSI', logo: brandImage('msi') },
    { name: 'Meta Quest', logo: brandImage('meta-quest') },
];

const uniqueEcommerceProductNames = [
    'product_001',
    'product_003',
    'product_005',
    'product_006',
    'product_007',
    'product_008',
    'product_010',
    'product_011',
    'product_016',
    'product_017',
    'product_018',
    'product_025',
    'product_040',
];

const withUniqueProductImages = (items, startIndex = 0) =>
    items.map((item, index) => ({
        ...item,
        artVariant: undefined,
        image: ecommerceProductImage(uniqueEcommerceProductNames[(startIndex + index) % uniqueEcommerceProductNames.length]),
    }));

const bestDealShelfItems = withUniqueProductImages(bestDealItems, 0);
const smartComfortItems = withUniqueProductImages(officeDealItems, 5);
const gamingLaptopItems = withUniqueProductImages([...bestDealItems, ...officeDealItems].slice(0, 6), 8);
const coolingDealItems = withUniqueProductImages([...memoryItems, ...electronicsItems, ...desktopPcItems].slice(0, 6), 14)
    .map((item, index) => ({
        ...item,
        category: item.category || 'Cooling',
        rating: item.rating || '4.5',
        reviews: item.reviews || `${(index + 2) * 17}`,
    }));
const printingDealItems = withUniqueProductImages([...moreToConsiderItems, ...desktopPcItems, ...bestDealItems].slice(0, 6), 20)
    .map((item, index) => ({
        ...item,
        category: item.category || '3D Printing',
        rating: item.rating || '4.3',
        reviews: item.reviews || `${(index + 1) * 14}`,
    }));
const considerationItems = withUniqueProductImages(
    [...moreToConsiderItems, ...desktopPcItems, ...bestDealItems, ...officeDealItems].slice(0, 16),
    26,
).map((item, index) => ({
        ...item,
        category: item.category || (index % 2 === 0 ? 'Storage' : 'Components'),
        rating: item.rating || '4.4',
        reviews: item.reviews || `${(index + 2) * 11}`,
    }));

const toolPromoScenes = [
    bannerImage('tools-feature-01'),
    bannerImage('tools-feature-02'),
    bannerImage('tools-feature-03'),
    bannerImage('tools-feature-04'),
    bannerImage('tools-feature-05'),
];

function SectionHeading({ kicker, title, action, href = '#' }) {
    return (
        <div className="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p className="text-[11px] font-black uppercase tracking-[0.22em] text-slate-500">
                    {kicker}
                </p>
                <h2 className="mt-2 text-2xl font-black tracking-[-0.04em] text-[#0b2e71]">
                    {title}
                </h2>
            </div>
            {action ? (
                <a href={href} className="text-sm font-bold text-slate-600 transition hover:text-[#0b2e71]">
                    {action} <span aria-hidden="true">&gt;</span>
                </a>
            ) : null}
        </div>
    );
}

function ShelfHeading({ title, href = '#', action = 'See More' }) {
    return (
        <div className="mb-5 flex flex-col items-start gap-2 sm:flex-row sm:items-end sm:gap-4">
            <h2 className="text-2xl font-black tracking-[-0.04em] text-[#001e62] sm:text-3xl lg:text-4xl">
                {title}
            </h2>
            <a href={href} className="text-sm font-bold text-slate-500 transition hover:text-[#0b2e71] sm:text-base">
                {action} <span aria-hidden="true">&gt;</span>
            </a>
        </div>
    );
}

function RatingDots({ rating = '4.5', reviews = '0' }) {
    const dots = Math.max(1, Math.min(5, Math.round(Number.parseFloat(rating) || 4)));

    return (
        <div className="flex items-center gap-1.5 text-xs text-slate-500">
            <span className="inline-flex items-center gap-0.5">
                {Array.from({ length: 5 }).map((_, index) => (
                    <span
                        key={index}
                        className={`h-2.5 w-2.5 rounded-full ${index < dots ? 'bg-[#ffb300]' : 'bg-[#d5dbe6]'}`}
                    />
                ))}
            </span>
            <span>({reviews})</span>
        </div>
    );
}

function ShelfProductCard({ item }) {
    return (
        <article className="flex h-full flex-col rounded-[10px] bg-[#edf2fb] p-4">
            <div className="overflow-hidden rounded-[8px] bg-white">
                {item.artVariant ? (
                    <PromoArtwork variant={item.artVariant} className="aspect-[4/3]" framed={false} />
                ) : (
                    <ProductImageFrame src={item.image} />
                )}
            </div>
            <div className="mt-3">
                <RatingDots rating={item.rating} reviews={item.reviews} />
                <h3 className="mt-2 min-h-[3.2rem] text-sm font-semibold leading-6 text-slate-800 sm:text-[15px]">
                    {item.title}
                </h3>
                {item.save ? (
                    <p className="mt-1 text-xs font-semibold text-[#e64620]">{item.save}</p>
                ) : null}
                <div className="mt-2 flex items-end gap-2">
                    <span className="text-3xl font-black tracking-[-0.05em] text-[#1f2937] sm:text-4xl">
                        {item.price}
                    </span>
                    {item.compare ? (
                        <span className="pb-1 text-xs font-semibold text-slate-400 line-through">
                            {item.compare}
                        </span>
                    ) : null}
                </div>
            </div>
        </article>
    );
}

function ToolStripCard({ tool, tone = 'from-[#587aa3] to-[#3367a5]' }) {
    return (
        <article className={`relative overflow-hidden rounded-[10px] bg-gradient-to-r ${tone} p-4 text-white`}>
            <div className="grid grid-cols-1 items-start gap-4 sm:grid-cols-[1fr_120px] sm:items-center">
                <div>
                    <h3 className="text-2xl font-black leading-tight sm:text-3xl">{tool.title}</h3>
                    <p className="mt-1 text-sm font-semibold text-white/95 sm:text-base">Check it out &gt;</p>
                </div>
                <div className="overflow-hidden rounded-[8px] bg-white/10">
                    {tool.imageUrl ? (
                        <img
                            src={tool.imageUrl}
                            alt=""
                            className="aspect-[4/3] h-full w-full object-cover"
                            loading="lazy"
                        />
                    ) : (
                        <PromoArtwork variant={tool.artVariant || 'tile'} className="aspect-[4/3]" framed={false} />
                    )}
                </div>
            </div>
        </article>
    );
}

function BrandLogoCard({ brand }) {
    return (
        <article className="flex h-[110px] items-center justify-center rounded-[10px] bg-white p-4">
            {brand.logo ? (
                <img
                    src={brand.logo}
                    alt={brand.name}
                    className="max-h-14 w-full object-contain"
                    loading="lazy"
                />
            ) : (
                <span className="text-2xl font-black tracking-tight text-slate-700">{brand.name}</span>
            )}
        </article>
    );
}

function PlexusBizMark() {
    return (
        <Link href="/" className="flex shrink-0 items-center gap-2 sm:gap-3">
            <img
                src="/images/project-logo.png"
                alt="PlexusBiz Automate"
                className="h-11 w-11 rounded-full bg-white object-cover shadow-[0_0_18px_rgba(255,255,255,0.18)] sm:h-12 sm:w-12"
            />
            <span className="hidden leading-tight sm:block">
                <span className="block text-[20px] font-black tracking-[-0.04em] text-white">PlexusBiz</span>
                <span className="block text-[11px] font-semibold uppercase tracking-[0.24em] text-white/70">
                    commerce hub
                </span>
            </span>
        </Link>
    );
}

function AddressTile() {
    return (
        <button
            type="button"
            className="flex min-w-0 items-center gap-2 rounded-[18px] px-2 py-2 text-left transition hover:bg-white/10 sm:gap-3 sm:px-3 xl:hidden"
        >
            <span className="grid h-8 w-8 shrink-0 place-items-center rounded-full border border-white/15 bg-[#2c5fb7] shadow-[inset_0_1px_0_rgba(255,255,255,0.15)] sm:h-9 sm:w-9">
                <svg
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                    className="h-4.5 w-4.5 text-white sm:h-5 sm:w-5"
                    fill="none"
                >
                    <path
                        d="M12 2.75c-3.6 0-6.5 2.8-6.5 6.3 0 4.9 6.5 11.9 6.5 11.9s6.5-7 6.5-11.9c0-3.5-2.9-6.3-6.5-6.3Z"
                        stroke="currentColor"
                        strokeWidth="1.8"
                        strokeLinejoin="round"
                    />
                    <path
                        d="M12 6.9a2.1 2.1 0 1 1 0 4.2 2.1 2.1 0 0 1 0-4.2Z"
                        fill="currentColor"
                    />
                </svg>
            </span>
            <span className="leading-tight">
                <span className="block text-[10px] font-semibold text-white/75 sm:text-[11px]">Hello</span>
                <span className="block text-[13px] font-black tracking-[-0.02em] text-white sm:text-sm">
                    Select address
                </span>
            </span>
        </button>
    );
}

function HeaderIcon({ children, className = '' }) {
    return (
        <span
            className={`grid h-10 w-10 place-items-center rounded-full border border-white/15 bg-white/10 text-white ${className}`}
        >
            {children}
        </span>
    );
}

function BundleCard({ bundle }) {
    const isDark = bundle.tone === 'dark';

    return (
        <article
            className={`flex h-full flex-col overflow-hidden rounded-[22px] border p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md ${isDark ? 'border-[#0f408f] bg-[#0b2e71] text-white' : `border-transparent bg-gradient-to-br ${bundle.accent} text-slate-900`}`}
        >
            <div className="flex items-start justify-between gap-3">
                <div>
                    <p className={`text-[11px] font-black uppercase tracking-[0.2em] ${isDark ? 'text-[#ffbf73]' : 'text-[#0b3d91]'}`}>
                        {bundle.brand}
                    </p>
                    <h3 className="mt-1 text-base font-extrabold tracking-[-0.03em]">
                        {bundle.savings}
                    </h3>
                </div>
                <a
                    href="#"
                    className={`text-xs font-bold ${isDark ? 'text-white/80' : 'text-[#0b3d91]'}`}
                >
                    More options
                </a>
            </div>

            <div className="mt-4 space-y-2">
                {bundle.items.map((item, index) => (
                    <div
                        key={`${item.title}-${index}`}
                        className={`flex items-center gap-3 rounded-[16px] p-2 ${isDark ? 'bg-white/10' : 'bg-white/80'}`}
                    >
                        <div className="h-14 w-14 shrink-0 overflow-hidden rounded-xl bg-slate-100">
                            <ProductImageFrame src={item.image} className="h-full w-full" imageClassName="p-1.5" />
                        </div>
                        <p className={`text-[11px] font-semibold leading-5 ${isDark ? 'text-white/90' : 'text-slate-700'}`}>
                            {item.title}
                        </p>
                    </div>
                ))}
            </div>

            {bundle.note ? (
                <p className={`mt-3 text-xs font-semibold ${isDark ? 'text-white/80' : 'text-[#d75d00]'}`}>
                    {bundle.note}
                </p>
            ) : null}

            <div className="mt-4 flex items-end justify-center gap-2">
                <span className={`text-3xl font-black tracking-[-0.05em] ${isDark ? 'text-white' : 'text-[#0b2e71]'}`}>
                    {bundle.price}
                </span>
                {bundle.compare ? (
                    <span className={`pb-0.5 text-xs font-semibold line-through ${isDark ? 'text-white/60' : 'text-slate-500'}`}>
                        {bundle.compare}
                    </span>
                ) : null}
            </div>

            <div className="mt-auto flex flex-col gap-3 pt-4 sm:flex-row">
                <button
                    type="button"
                    className={`flex-1 rounded-full border px-4 py-2 text-xs font-black uppercase tracking-[0.16em] ${isDark ? 'border-white/15 bg-white/10 text-white' : 'border-[#d7e3f4] bg-white text-[#0b3d91]'}`}
                >
                    {bundle.cta}
                </button>
                <button
                    type="button"
                    className="flex-1 rounded-full bg-[#ff9a1f] px-4 py-2 text-xs font-black uppercase tracking-[0.16em] text-white"
                >
                    Add to cart
                </button>
            </div>
        </article>
    );
}

function PromoBanner({ banner }) {
    return (
        <article className="overflow-hidden rounded-[28px] border border-[#d7e3f4] bg-white shadow-sm">
            <div className="grid min-h-[240px] gap-0 lg:grid-cols-2">
                <div className="relative min-h-[240px] overflow-hidden">
                    <PromoArtwork variant={banner.artVariant || 'banner'} className="h-full min-h-[240px]" framed={false} />
                </div>
                <div className="flex flex-col justify-center gap-4 bg-[#0b2e71] px-6 py-6 text-white">
                    <p className="text-[11px] font-black uppercase tracking-[0.2em] text-[#ffd59a]">
                        {banner.title}
                    </p>
                    <h3 className="max-w-md text-2xl font-black tracking-[-0.05em] sm:text-3xl">
                        {banner.subtitle}
                    </h3>
                    <a
                        href="#deals"
                        className="inline-flex w-fit items-center rounded-full bg-[#ffcf30] px-5 py-2 text-xs font-black uppercase tracking-[0.16em] text-[#0b2e71]"
                    >
                        {banner.cta}
                    </a>
                </div>
            </div>
        </article>
    );
}

function CompactProductCard({ item, dark = false }) {
    return (
        <article
            className={`overflow-hidden rounded-[24px] border shadow-sm transition hover:-translate-y-0.5 hover:shadow-md ${dark ? 'border-white/10 bg-[#0b2e71] text-white' : 'border-[#d7e3f4] bg-white text-slate-900'}`}
        >
            <div className="p-4">
                <div className="overflow-hidden rounded-[18px] bg-slate-50">
                    <ProductImageFrame src={item.image} />
                </div>
                {item.badge ? (
                    <span className={`mt-3 inline-flex rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-[0.16em] ${dark ? 'bg-white/10 text-white' : 'bg-[#eef4ff] text-[#0b3d91]'}`}>
                        {item.badge}
                    </span>
                ) : null}
                {item.category ? (
                    <p className={`mt-3 text-xs font-black uppercase tracking-[0.18em] ${dark ? 'text-white/60' : 'text-slate-400'}`}>
                        {item.category}
                    </p>
                ) : null}
                <h3 className={`mt-2 text-sm font-bold leading-6 ${dark ? 'text-white' : 'text-slate-900'}`}>
                    {item.title}
                </h3>
                {item.short ? (
                    <p className={`mt-2 text-xs leading-5 ${dark ? 'text-white/75' : 'text-slate-600'}`}>
                        {item.short}
                    </p>
                ) : null}
                <div className={`mt-3 flex items-end gap-2 ${dark ? 'text-white' : 'text-[#0b2e71]'}`}>
                    <span className="text-2xl font-black tracking-[-0.04em]">{item.price}</span>
                    {item.compare ? (
                        <span className={`pb-0.5 text-xs font-semibold line-through ${dark ? 'text-white/60' : 'text-slate-400'}`}>
                            {item.compare}
                        </span>
                    ) : null}
                </div>
                {item.save ? (
                    <p className={`mt-2 text-xs font-semibold ${dark ? 'text-[#ffd59a]' : 'text-[#d75d00]'}`}>{item.save}</p>
                ) : null}
            </div>
        </article>
    );
}

function CategoryPanel({ panel }) {
    const tileGridClass = panel.tileGridClass || 'mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2';
    const tileAspectClass = panel.tileAspectClass || 'aspect-[4/3]';
    const bannerClassName = panel.bannerClassName || 'min-h-[220px] sm:min-h-[260px]';

    return (
        <article className="overflow-hidden rounded-[28px] border border-[#d7e3f4] bg-white p-5 shadow-sm">
            <h3 className="text-xl font-black tracking-[-0.04em] text-[#0b2e71] sm:text-2xl">{panel.title}</h3>

            {panel.bannerArtVariant ? (
                <div className="mt-4 overflow-hidden rounded-[18px] border border-[#eef3fb] bg-slate-50">
                    <PromoArtwork
                        variant={panel.bannerArtVariant}
                        imageUrl={panel.bannerImageUrl}
                        imageFit={panel.bannerImageFit || 'cover'}
                        className={bannerClassName}
                        framed={false}
                    />
                </div>
            ) : null}

            {panel.bannerLabel ? <p className="mt-3 text-sm font-semibold text-slate-700">{panel.bannerLabel}</p> : null}
            {panel.bannerCopy ? <p className="mt-1 text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{panel.bannerCopy}</p> : null}

            {panel.tiles ? (
                <div className={tileGridClass}>
                    {panel.tiles.map((tile) => (
                        <div key={tile.label} className="rounded-[18px] bg-[#f4f8ff] p-3">
                            <div className="overflow-hidden rounded-[14px] bg-white">
                                <PromoArtwork
                                    variant={tile.artVariant || 'categoryTile'}
                                    sceneLabel={tile.sceneLabel || tile.label}
                                    imageUrl={tile.imageUrl}
                                    imageAlt={tile.imageAlt || tile.label}
                                    imageFit={tile.imageFit || 'contain'}
                                    className={tileAspectClass}
                                    framed={false}
                                />
                            </div>
                            <p className="mt-3 text-sm font-semibold text-slate-900">{tile.label}</p>
                        </div>
                    ))}
                </div>
            ) : null}
        </article>
    );
}

function ToolCard({ tool }) {
    return (
        <article className="overflow-hidden rounded-[24px] border border-[#d7e3f4] bg-white p-4 shadow-sm">
            <div className="overflow-hidden rounded-[18px] bg-[#0b3d91]">
                <PromoArtwork variant={tool.artVariant || 'tile'} className="aspect-[16/10]" framed={false} />
            </div>
            <h3 className="mt-3 text-lg font-black tracking-[-0.03em] text-[#0b2e71]">{tool.title}</h3>
            <p className="mt-1 text-sm leading-6 text-slate-600">{tool.copy}</p>
            <a
                href="#"
                className="mt-4 inline-flex rounded-full border border-[#d7e3f4] bg-[#f4f8ff] px-4 py-2 text-xs font-black uppercase tracking-[0.16em] text-[#0b3d91]"
            >
                Check it out
            </a>
        </article>
    );
}

function KeywordPill({ keyword }) {
    return (
        <span className="rounded-full border border-[#d7e3f4] bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm">
            {keyword}
        </span>
    );
}

function InsightCard({ insight }) {
    return (
        <article className="rounded-[24px] border border-[#d7e3f4] bg-white p-4 shadow-sm">
            <div className="mb-3 inline-flex rounded-full bg-[#eef4ff] px-3 py-1 text-[10px] font-black uppercase tracking-[0.16em] text-[#0b3d91]">
                Community
            </div>
            <p className="text-sm leading-6 text-slate-700">{insight}</p>
        </article>
    );
}

export default function Welcome({ auth = {}, canLogin, canRegister }) {
    const isAuthed = Boolean(auth?.user);
    const primaryHref = isAuthed ? route('dashboard') : route('register');

    return (
        <FrontendLayout auth={auth} canLogin={canLogin}>
            <Head title="PlexusBiz Automate | Electronics Store: Tech, PC Parts, AI PC & More" />

            <div
                id="hero"
                className="show-tab-store show-img-bg relative min-h-screen overflow-x-hidden bg-[#eaf2ff] text-slate-900 antialiased"
                style={{ fontFamily: "'Segoe UI', Arial, sans-serif" }}
            >
                <div
                    className="pointer-events-none absolute inset-x-0 top-0 h-[26rem]"
                    style={{
                        background:
                            'linear-gradient(180deg, rgba(202, 221, 255, 0.98) 0%, rgba(240, 246, 255, 0.96) 46%, rgba(234, 242, 255, 0) 100%)',
                    }}
                />
                <div className="pointer-events-none absolute -left-24 top-28 h-72 w-72 rounded-full bg-[#2b74db]/12 blur-3xl" />
                <div className="pointer-events-none absolute right-0 top-16 h-80 w-80 rounded-full bg-[#ff7b22]/10 blur-3xl" />

                <main className="relative z-10 mx-auto w-full max-w-[1900px] space-y-4 px-4 py-4 sm:px-6 xl:px-8">
                    <section className="grid items-stretch gap-3 grid-cols-1 xl:grid-cols-[420px_minmax(0,1fr)]">
                        <aside className="order-2 h-[380px] overflow-hidden rounded-[16px] bg-[#0b2e71] text-white shadow-[0_18px_40px_-22px_rgba(7,18,46,0.85)] sm:h-[440px] lg:h-[520px] xl:order-1 xl:h-[580px] xl:sticky xl:top-4">
                            <div className="flex h-full flex-col">
                                <div className="mb-3 flex items-center justify-between gap-3 border-b border-white/10 px-4 py-3">
                                    <div>
                                        <h2 className="text-base font-black tracking-[-0.03em]">
                                            Business Essentials
                                        </h2>
                                    </div>
                                    <span className="rounded-full bg-white/10 px-2 py-0.5 text-[9px] font-black uppercase tracking-[0.16em] text-[#ffd59a]">
                                        Live
                                    </span>
                                </div>
                                <div className="flex-1 overflow-y-auto px-2">
                                    {marketplaceShortcutItems.map((item) => (
                                        <Link
                                            key={item.title}
                                            href={item.href}
                                            className="flex w-full items-center gap-3 rounded-[12px] px-3 py-2.5 text-left transition hover:bg-white/10"
                                        >
                                            <span className="inline-flex shrink-0 items-center justify-center text-white/90">
                                                <item.icon className="h-6 w-6" />
                                            </span>
                                            <span className="min-w-0 flex-1 text-sm font-semibold leading-5 text-white">
                                                {item.title}
                                            </span>
                                            <span className="text-lg font-black text-white/35">&gt;</span>
                                        </Link>
                                    ))}
                                </div>
                            </div>
                        </aside>

                        <div className="order-1 space-y-4 xl:order-2 xl:flex xl:h-[580px] xl:flex-col xl:gap-4 xl:space-y-0">
                            <article className="relative min-h-[280px] overflow-hidden rounded-[16px] border border-[#d7e3f4] shadow-[0_24px_70px_-34px_rgba(8,24,66,0.9)] sm:min-h-[300px] xl:h-[300px] xl:min-h-0 xl:flex-none">
                                <img 
                                    src={heroBackground} 
                                    alt="Gaming Week Extended" 
                                    className="h-full w-full object-cover"
                                />
                            </article>

                            <div className="grid gap-4 lg:grid-cols-2 xl:grid-cols-3 xl:min-h-0 xl:flex-1">
                                {heroImageCards.map((card) => (
                                    <ImageOnlyPromoCard key={card.image} image={card.image} />
                                ))}
                            </div>
                        </div>
                    </section>

                    <div className="space-y-10">
                        <BannerImageGrid banners={lowerBannerImages} />

                        <section id="deals" className="rounded-[12px] bg-[#f2f4f8] px-5 py-8">
                            <ShelfHeading title="Today's Best Deals" href="#footer" action="See all deals" />
                            <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                                {bestDealShelfItems.map((item) => (
                                    <ShelfProductCard key={item.title} item={item} />
                                ))}
                            </div>
                            <div className="mt-6 flex justify-center">
                                <a
                                    href="#footer"
                                    className="inline-flex min-w-[220px] items-center justify-center rounded-full bg-[#1f67c9] px-6 py-3 text-sm font-black text-white"
                                >
                                    See all deals &gt;
                                </a>
                            </div>
                        </section>

                        <section className="rounded-[12px] bg-[#f2f4f8] px-5 py-8">
                            <ShelfHeading title="Smart Comfort Home" href="#footer" />
                            <div className="grid gap-6 xl:grid-cols-[1fr_860px]">
                                <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                    {smartComfortItems.map((item) => (
                                        <ShelfProductCard key={item.title} item={item} />
                                    ))}
                                </div>
                                <article className="overflow-hidden rounded-[12px] bg-[#f7efe7]">
                                    <img
                                        src={bannerImage('smart-home')}
                                        alt=""
                                        className="h-full min-h-[360px] w-full object-cover"
                                        loading="lazy"
                                    />
                                </article>
                            </div>
                        </section>

                        <section className="grid gap-4 xl:grid-cols-4">
                            {categoryPanels.map((panel) => (
                                <CategoryPanel key={panel.title} panel={panel} />
                            ))}
                        </section>

                        <section className="overflow-hidden rounded-[12px] bg-white">
                            <img src={bannerImage('brand-strip')} alt="" className="h-[120px] w-full object-cover" loading="lazy" />
                        </section>

                        <section className="rounded-[12px] bg-[#f2f4f8] px-5 py-8">
                            <ShelfHeading title="Gaming Laptops" href="#footer" />
                            <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                                {gamingLaptopItems.map((item) => (
                                    <ShelfProductCard key={`gaming-${item.title}`} item={item} />
                                ))}
                            </div>
                        </section>

                        <section className="rounded-[12px] bg-[#f2f4f8] px-5 py-8">
                            <ShelfHeading title="Water / Liquid Cooling" href="#footer" />
                            <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                                {coolingDealItems.map((item) => (
                                    <ShelfProductCard key={`cooling-${item.title}`} item={item} />
                                ))}
                            </div>
                        </section>

                        <section className="rounded-[12px] bg-[#f2f4f8] px-5 py-8">
                            <ShelfHeading title="3D Printing & Engraving" href="#footer" />
                            <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                                {printingDealItems.map((item) => (
                                    <ShelfProductCard key={`printing-${item.title}`} item={item} />
                                ))}
                            </div>
                        </section>

                        <section className="rounded-[12px] bg-[#f2f4f8] px-5 py-8">
                            <ShelfHeading title="More Items to Consider" href="#footer" />
                            <div className="grid gap-4 md:grid-cols-3 xl:grid-cols-6">
                                {considerationItems.slice(0, 6).map((item) => (
                                    <ShelfProductCard key={`consider-top-${item.title}`} item={item} />
                                ))}
                            </div>
                            <div className="mt-4 grid gap-4 md:grid-cols-3 xl:grid-cols-6">
                                <article className="rounded-[10px] bg-[#0f4fb1] p-4 text-white xl:col-span-1">
                                    <h3 className="text-lg font-black">Community insights</h3>
                                    <ul className="mt-3 space-y-2 text-sm leading-6 text-white/95">
                                        {communityInsights.map((insight) => (
                                            <li key={insight}>• {insight}</li>
                                        ))}
                                    </ul>
                                </article>
                                {considerationItems.slice(6, 11).map((item) => (
                                    <ShelfProductCard key={`consider-bottom-${item.title}`} item={item} />
                                ))}
                            </div>
                            <div className="mt-6 flex justify-center">
                                <button
                                    type="button"
                                    className="rounded-full border border-slate-300 px-8 py-2 text-sm font-black text-slate-600"
                                >
                                    Load More
                                </button>
                            </div>
                        </section>

                        <section id="tools" className="rounded-[12px] bg-[#f2f4f8] px-5 py-8">
                            <ShelfHeading title="Shopping Tools" href="#footer" />
                            <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                                {shoppingTools.map((tool, index) => (
                                    <ToolStripCard
                                        key={tool.title}
                                        tool={tool}
                                        tone={
                                            [
                                                'from-[#5c7fa8] to-[#3f6895]',
                                                'from-[#0e89bf] to-[#147eb2]',
                                                'from-[#264fb0] to-[#23409a]',
                                                'from-[#5e759f] to-[#57709a]',
                                                'from-[#0e939d] to-[#0f8a93]',
                                            ][index % 5]
                                        }
                                    />
                                ))}
                            </div>

                            <div className="mt-6 grid gap-4 xl:grid-cols-[1.15fr_1.15fr_1.05fr_.95fr]">
                                <article className="overflow-hidden rounded-[12px] bg-white">
                                    <img src={toolPromoScenes[0]} alt="" className="h-[340px] w-full object-cover" loading="lazy" />
                                </article>
                                <div className="grid gap-4">
                                    <article className="overflow-hidden rounded-[12px] bg-white">
                                        <img src={toolPromoScenes[1]} alt="" className="h-[162px] w-full object-cover" loading="lazy" />
                                    </article>
                                    <article className="overflow-hidden rounded-[12px] bg-white">
                                        <img src={toolPromoScenes[2]} alt="" className="h-[162px] w-full object-cover" loading="lazy" />
                                    </article>
                                </div>
                                <article className="overflow-hidden rounded-[12px] bg-white">
                                    <img src={toolPromoScenes[3]} alt="" className="h-[340px] w-full object-cover" loading="lazy" />
                                </article>
                                <div className="grid gap-4">
                                    <article className="overflow-hidden rounded-[12px] bg-white">
                                        <img src={toolPromoScenes[4]} alt="" className="h-[162px] w-full object-cover" loading="lazy" />
                                    </article>
                                </div>
                            </div>
                        </section>

                        <section id="brands" className="rounded-[12px] bg-[#f2f4f8] px-5 py-8">
                            <ShelfHeading title="Featured Brands" href="#footer" />
                            <div className="grid gap-3 sm:grid-cols-2 md:grid-cols-4 xl:grid-cols-7">
                                {brandLogoItems.map((brand) => (
                                    <BrandLogoCard key={brand.name} brand={brand} />
                                ))}
                            </div>
                            <article className="mt-8 rounded-[12px] border border-[#d7e0ef] bg-white p-4">
                                <h3 className="text-2xl font-black italic tracking-[-0.03em] text-slate-800">Popular Products</h3>
                                <div className="mt-4 flex flex-wrap gap-2">
                                    {popularKeywords.map((keyword) => (
                                        <KeywordPill key={keyword} keyword={keyword} />
                                    ))}
                                </div>
                            </article>
                        </section>

                        <LandingPromoStrip />
                    </div>
                </main>
            </div>
        </FrontendLayout>
    );
}
