import { useEffect, useRef, useState } from 'react';
import { Link, Head } from '@inertiajs/react';

const quickNavLinks = [
    { label: 'Bulk Orders', href: route('products.bulk') },
    { label: 'MOQ Pricing', href: route('products.moq') },
    { label: 'Request a Quote', href: route('rfq.create') },
    { label: 'Become a Supplier', href: route('supplier.apply') },
];

const footerSections = [
    {
        title: 'Customer Service',
        links: [
            { label: 'Help Center', href: '#footer' },
            { label: 'Track an Order', href: '#footer' },
            { label: 'Return an Item', href: '#footer' },
            { label: 'Return Policy', href: '#footer' },
            { label: 'Privacy & Security', href: '#footer' },
            { label: 'Feedback', href: '#footer' },
        ],
    },
    {
        title: 'My Account',
        links: [
            { label: 'Login/Register', href: '#footer' },
            { label: 'Browsing History', href: '#footer' },
            { label: 'Order History', href: '#footer' },
            { label: 'Returns History', href: '#footer' },
            { label: 'Address Book', href: '#footer' },
            { label: 'Wish Lists', href: '#footer' },
            { label: 'My Build Lists', href: '#footer' },
            { label: 'My Build Showcase', href: '#footer' },
            { label: 'Email Notifications', href: '#footer' },
            { label: 'Subscriptions Orders', href: '#footer' },
            { label: 'Auto Notifications', href: '#footer' },
        ],
    },
    {
        title: 'Company Information',
        links: [
            { label: 'About PlexusBiz', href: '#footer' },
            { label: 'Investor Relations', href: '#footer' },
            { label: 'PlexusBiz Student Internship Program', href: '#footer' },
            { label: 'Gamer Zone', href: '#footer' },
            { label: 'Awards/Rankings', href: '#footer' },
            { label: 'Hours and Locations', href: '#footer' },
            { label: 'Press Inquiries', href: '#footer' },
            { label: 'PlexusBiz Careers', href: '#footer' },
            { label: 'Newsroom', href: '#footer' },
            { label: 'Cigna MRF', href: '#footer' },
            { label: 'PlexusBiz Insider', href: '#footer' },
            { label: 'Calif. Transparency in Supply Chains Act', href: '#footer' },
        ],
    },
    {
        title: 'Tools & Resources',
        links: [
            { label: 'Become a supplier', href: route('supplier.apply') },
            { label: 'Sell on PlexusBiz', href: route('supplier.apply') },
            { label: 'For Your Business', href: '#footer' },
            { label: 'PlexusBiz Partner Services', href: '#footer' },
            { label: 'Become an Affiliate', href: '#footer' },
            { label: 'PlexusBiz Creators', href: '#footer' },
            { label: 'Site Map', href: '#footer' },
            { label: 'Shop by Brand', href: '#footer' },
            { label: 'Rebates', href: '#footer' },
            { label: 'Mobile Apps', href: '#footer' },
            { label: 'Student Discount', href: '#footer' },
            { label: 'PlexusBiz Store Credit Card', href: '#footer' },
            { label: 'Build Showcases', href: '#footer' },
            { label: 'Progressive Leasing', href: '#footer' },
            { label: 'Trade In', href: '#footer' },
        ],
    },
    {
        title: 'Shop Our Brands',
        links: [
            { label: 'PlexusBiz Business', href: '#footer' },
            { label: 'PlexusBiz Global', href: '#footer' },
            { label: 'ABS', href: '#footer' },
            { label: 'Rosewill', href: '#footer' },
        ],
    },
];



function PlexusBizMark() {
    return (
        <Link href="/" className="flex shrink-0 items-center gap-2 sm:gap-3">
            <img
                src="/images/project-logo.png"
                alt="PlexusBiz Automate"
                className="h-10 w-10 rounded-full bg-white object-cover shadow-[0_0_18px_rgba(255,255,255,0.18)] sm:h-11 sm:w-11"
            />
            <span className="hidden leading-tight xl:block">
                <span className="block text-[18px] font-black tracking-[-0.04em] text-white">PlexusBiz</span>
                <span className="hidden text-[11px] font-semibold uppercase tracking-[0.24em] text-white/70 xl:block">
                    e-commerce hub
                </span>
            </span>
        </Link>
    );
}

function AddressTile({ onClick }) {
    return (
        <button
            type="button"
            onClick={onClick}
            className="hidden min-w-0 items-center gap-2 rounded-[18px] px-2 py-2 text-left transition hover:bg-white/10 sm:flex sm:gap-3 sm:px-3"
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
        <span className={`inline-flex items-center justify-center text-white ${className}`}>
            {children}
        </span>
    );
}

function SearchIcon({ className = '' }) {
    return (
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" className={className}>
            <circle cx="10.5" cy="10.5" r="5.75" stroke="currentColor" strokeWidth="1.8" />
            <path d="m15.25 15.25 4 4" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" />
        </svg>
    );
}

function BellIcon({ className = '' }) {
    return (
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" className={className}>
            <path
                d="M12 4.75c-2.9 0-5.25 2.35-5.25 5.25v2.12c0 .77-.2 1.53-.58 2.2l-1.02 1.77a.9.9 0 0 0 .78 1.36h12.14a.9.9 0 0 0 .78-1.36l-1.02-1.77a4.1 4.1 0 0 1-.58-2.2V10c0-2.9-2.35-5.25-5.25-5.25Z"
                stroke="currentColor"
                strokeWidth="1.8"
                strokeLinejoin="round"
            />
            <path d="M9.8 18.15a2.2 2.2 0 0 0 4.4 0" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" />
        </svg>
    );
}

function UserIcon({ className = '' }) {
    return (
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" className={className}>
            <path
                d="M12 13.1a3.4 3.4 0 1 0 0-6.8 3.4 3.4 0 0 0 0 6.8Z"
                stroke="currentColor"
                strokeWidth="1.8"
            />
            <path
                d="M5.8 19.2c.95-3.1 3.3-4.7 6.2-4.7s5.25 1.6 6.2 4.7"
                stroke="currentColor"
                strokeWidth="1.8"
                strokeLinecap="round"
            />
        </svg>
    );
}

function CartIcon({ className = '' }) {
    return (
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" className={className}>
            <path
                d="M4.25 5.25h1.35l1.65 8.35c.14.7.75 1.2 1.47 1.2h7.98c.71 0 1.34-.49 1.49-1.18l1.25-5.72H7.28"
                stroke="currentColor"
                strokeWidth="1.8"
                strokeLinejoin="round"
            />
            <circle cx="9.1" cy="18.35" r="1.15" fill="currentColor" />
            <circle cx="16.85" cy="18.35" r="1.15" fill="currentColor" />
        </svg>
    );
}

function MoonIcon({ className = '' }) {
    return (
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" className={className}>
            <path
                d="M15.8 14.7A6.7 6.7 0 0 1 9.3 5.1a7.1 7.1 0 1 0 6.5 9.6Z"
                stroke="currentColor"
                strokeWidth="1.7"
                strokeLinejoin="round"
            />
        </svg>
    );
}

function SunIcon({ className = '' }) {
    return (
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" className={className}>
            <circle cx="12" cy="12" r="4.2" stroke="currentColor" strokeWidth="1.7" />
            <path
                d="M12 3.8v2.1M12 18.1v2.1M3.8 12h2.1M18.1 12h2.1M6.2 6.2l1.5 1.5M16.3 16.3l1.5 1.5M16.3 7.7l1.5-1.5M6.2 17.8l1.5-1.5"
                stroke="currentColor"
                strokeWidth="1.7"
                strokeLinecap="round"
            />
        </svg>
    );
}

function FlagBadge({ className = '' }) {
    return (
        <span className={`inline-flex h-8 w-8 items-center justify-center rounded-full border border-white/15 bg-white/10 ${className}`}>
            <span className="relative block h-5 w-5 overflow-hidden rounded-full border border-white/70 bg-white">
                <span
                    className="absolute inset-0"
                    style={{
                        backgroundImage:
                            'repeating-linear-gradient(180deg, #b4232e 0 2px, #ffffff 2px 4px)',
                    }}
                />
                <span className="absolute left-0 top-0 h-[52%] w-[46%] bg-[#1d4ea6]" />
            </span>
        </span>
    );
}

function ThemeToggle() {
    return (
        <span className="hidden h-10 items-center rounded-full border border-white/15 bg-white px-1.5 text-left shadow-[0_6px_14px_-10px_rgba(9,20,48,0.7)] sm:flex">
            <span className="grid h-7 w-7 place-items-center rounded-full bg-[#0b2e71] text-white">
                <MoonIcon className="h-3.5 w-3.5" />
            </span>
            <span className="grid h-7 w-7 place-items-center rounded-full bg-[#edf2fb] text-[#0b2e71]">
                <SunIcon className="h-3.5 w-3.5" />
            </span>
        </span>
    );
}

function AddressSelectorModal({ isOpen, onClose, isAuthed }) {
    const [zipCode, setZipCode] = useState('');
    const [selectedRegion, setSelectedRegion] = useState('');
    const [isLoading, setIsLoading] = useState(false);

    if (!isOpen) return null;

    const handleApplyZip = () => {
        if (zipCode.trim()) {
            setIsLoading(true);
            // Simulate API call
            setTimeout(() => {
                setIsLoading(false);
                localStorage.setItem('deliveryZip', zipCode);
                onClose();
            }, 500);
        }
    };

    const handleRegionChange = (e) => {
        setSelectedRegion(e.target.value);
        if (e.target.value) {
            localStorage.setItem('deliveryRegion', e.target.value);
            onClose();
        }
    };

    const canDone = zipCode.trim() || selectedRegion;

    return (
        <div className="fixed inset-0 z-[100] flex items-start justify-center pt-20 sm:pt-24">
            {/* Backdrop */}
            <div 
                className="absolute inset-0 bg-black/40 backdrop-blur-sm" 
                onClick={onClose}
            />
            
            {/* Modal */}
            <div className="relative w-full max-w-md mx-4 rounded-2xl bg-white shadow-2xl overflow-hidden">
                {/* Triangle pointer */}
                <div className="absolute -top-2 left-8 sm:left-12 w-4 h-4 bg-white rotate-45" />
                
                {/* Header */}
                <div className="relative flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 className="text-lg font-bold italic text-gray-900">Choose your location</h2>
                    <button 
                        onClick={onClose}
                        className="p-2 text-gray-400 hover:text-gray-600 transition rounded-full hover:bg-gray-100"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div className="p-6 space-y-6">
                    {/* Description */}
                    <p className="text-sm text-gray-600 leading-relaxed">
                        Delivery options and delivery speeds may vary for different locations.
                    </p>

                    {/* Sign In Button */}
                    {!isAuthed && (
                        <>
                            <button
                                onClick={() => window.location.href = route('login')}
                                className="w-full py-3 px-4 bg-[#0b2e71] hover:bg-[#15408a] text-white font-semibold rounded-full transition"
                            >
                                Sign In to see your addresses
                            </button>

                            {/* Divider */}
                            <div className="relative flex items-center justify-center">
                                <div className="absolute inset-0 flex items-center">
                                    <div className="w-full border-t border-gray-200" />
                                </div>
                                <span className="relative px-4 bg-white text-sm text-gray-500">OR</span>
                            </div>
                        </>
                    )}

                    {/* ZIP Code Input */}
                    <div className="flex gap-2">
                        <div className="flex-1 relative">
                            <input
                                type="text"
                                value={zipCode}
                                onChange={(e) => setZipCode(e.target.value.replace(/\D/g, '').slice(0, 10))}
                                placeholder="Enter a US zip code"
                                className="w-full px-4 py-3 border border-gray-300 rounded-l-full focus:outline-none focus:ring-2 focus:ring-[#0b2e71] focus:border-transparent text-sm"
                            />
                        </div>
                        <button
                            onClick={handleApplyZip}
                            disabled={!zipCode.trim() || isLoading}
                            className="px-6 py-3 bg-[#96b8ef] hover:bg-[#7ca7e8] disabled:bg-gray-200 disabled:text-gray-400 text-[#0b2e71] font-semibold rounded-r-full transition"
                        >
                            {isLoading ? '...' : 'APPLY'}
                        </button>
                    </div>

                    {/* Divider */}
                    <div className="relative flex items-center justify-center">
                        <div className="absolute inset-0 flex items-center">
                            <div className="w-full border-t border-gray-200" />
                        </div>
                        <span className="relative px-4 bg-white text-sm text-gray-500">OR</span>
                    </div>

                    {/* Region Dropdown */}
                    <div className="relative">
                        <select
                            value={selectedRegion}
                            onChange={handleRegionChange}
                            className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0b2e71] focus:border-transparent text-sm appearance-none bg-white cursor-pointer"
                        >
                            <option value="">Ship Outside the US</option>
                            <option value="CA">Canada</option>
                            <option value="UK">United Kingdom</option>
                            <option value="EU">Europe</option>
                            <option value="AU">Australia</option>
                            <option value="BD">Bangladesh</option>
                            <option value="IN">India</option>
                            <option value="SG">Singapore</option>
                            <option value="AE">UAE</option>
                            <option value="OTHER">Other Countries</option>
                        </select>
                        <div className="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                            <svg className="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                {/* Footer */}
                <div className="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <button
                        onClick={onClose}
                        disabled={!canDone}
                        className={`w-full py-3 px-4 rounded-full font-semibold transition ${
                            canDone 
                                ? 'bg-[#0b2e71] hover:bg-[#15408a] text-white' 
                                : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                        }`}
                    >
                        Done
                    </button>
                </div>
            </div>
        </div>
    );
}

function FooterColumn({ section }) {
    const [isOpen, setIsOpen] = useState(false);
    const slugify = (text) => text.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');

    return (
        <div className={`mb-3 md:mb-0 transition-all duration-200 border border-white/20 md:border-transparent md:p-0 ${isOpen ? 'rounded-[12px] bg-[#0b192c]' : 'rounded-full'}`}>
            {/* Mobile Toggle Button */}
            <button 
                onClick={() => setIsOpen(!isOpen)}
                className="flex w-full items-center justify-between px-5 py-3.5 md:hidden focus:outline-none"
            >
                <h3 className="text-[15px] font-bold text-white uppercase">{section.title}</h3>
                <svg 
                    className={`h-4 w-4 text-white transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`} 
                    fill="none" viewBox="0 0 24 24" stroke="currentColor"
                >
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            
            {/* Desktop Heading (hidden on mobile) */}
            <h3 className="hidden text-[15px] font-bold text-white mb-4 md:block uppercase">
                {section.title}
            </h3>

            {/* Links List (toggled on mobile, always visible on desktop) */}
            <ul className={`md:mt-0 md:block ${isOpen ? 'block px-5 pb-2' : 'hidden'}`}>
                {section.links.map((link, index) => (
                    <li key={link.label} className={index !== section.links.length - 1 ? 'border-b border-white/20 md:border-transparent' : ''}>
                        <Link href={`/p/${slugify(link.label)}`} className="block py-3 md:py-1.5 text-[15px] font-medium text-white/95 transition hover:text-white hover:underline">
                            {link.label}
                        </Link>
                    </li>
                ))}
            </ul>
        </div>
    );
}

export default function FrontendLayout({ auth, canLogin, cartCount = 0, children }) {
    const isAuthed = Boolean(auth?.user);
    const headerRef = useRef(null);
    const [headerHeight, setHeaderHeight] = useState(148);
    const [isAddressModalOpen, setIsAddressModalOpen] = useState(false);

    useEffect(() => {
        const updateHeaderHeight = () => {
            if (!headerRef.current) {
                return;
            }

            const nextHeight = Math.ceil(headerRef.current.getBoundingClientRect().height);
            setHeaderHeight(nextHeight > 0 ? nextHeight : 148);
        };

        updateHeaderHeight();
        window.addEventListener('resize', updateHeaderHeight);
        return () => window.removeEventListener('resize', updateHeaderHeight);
    }, [isAuthed, canLogin, cartCount]);

    return (
        <div className="min-h-screen overflow-x-hidden bg-[#eaf2ff] text-slate-900 antialiased" style={{ fontFamily: "'Segoe UI', Arial, sans-serif" }}>
            <header ref={headerRef} className="fixed inset-x-0 top-0 z-50">
                    <div className="border-b border-[#042e6f] bg-[#0b2e71] text-white shadow-[0_10px_24px_-18px_rgba(7,18,46,0.8)]">
                        <div className="mx-auto grid w-full max-w-[1900px] grid-cols-[auto_minmax(0,1fr)_auto] items-center gap-2 px-4 py-3 sm:grid-cols-[auto_auto_minmax(0,1fr)_auto] sm:gap-3 sm:px-6 xl:gap-4 xl:px-8">
                            <PlexusBizMark />

                            <AddressTile onClick={() => setIsAddressModalOpen(true)} />

                            <form className="order-4 col-span-full min-w-0 flex-1 sm:order-none sm:col-span-1 xl:mr-[200px] xl:pl-1" onSubmit={(event) => event.preventDefault()}>
                                <div className="relative flex h-10 w-full items-stretch overflow-hidden rounded-full border border-[#d9e3f3] bg-white shadow-[0_10px_30px_-18px_rgba(9,20,48,0.9)] sm:h-11">
                                    <input
                                        type="search"
                                        placeholder="Search products, deals, and parts"
                                        className="min-w-0 flex-1 bg-transparent px-4 text-[13px] text-slate-800 outline-none placeholder:text-[#7c8aa3] sm:px-5"
                                    />
                                    <button
                                        type="submit"
                                        aria-label="Search"
                                        className="flex h-full w-10 shrink-0 items-center justify-center bg-[#96b8ef] text-[#0b2e71] transition hover:bg-[#7ca7e8] sm:w-12"
                                    >
                                        <SearchIcon className="h-4 w-4" />
                                    </button>
                                </div>
                            </form>

                            <div className="order-3 flex items-center gap-4 justify-self-end sm:order-none xl:gap-6 xl:pl-10">
                                <button
                                    type="button"
                                    className="relative inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/15 bg-white/10 text-white transition hover:bg-white/20"
                                >
                                    <BellIcon className="h-5 w-5" />
                                    <span className="absolute -right-0.5 -top-0.5 h-2.5 w-2.5 rounded-full bg-[#ff8a00] border-2 border-[#0b2e71]"></span>
                                </button>

                                {!isAuthed && canLogin && (
                                    <Link
                                        href={route('login')}
                                        className="inline-flex min-w-[148px] shrink-0 items-center gap-3 rounded-full border border-white/15 bg-white/10 px-4 py-2.5 text-left shadow-[0_6px_14px_-10px_rgba(9,20,48,0.7)] sm:hidden"
                                    >
                                        <UserIcon className="h-6 w-6 shrink-0 text-white" />
                                        <span className="min-w-0 leading-none">
                                            <span className="block whitespace-nowrap text-[11px] font-black tracking-[-0.02em]">
                                                Sign In / Register
                                            </span>
                                        </span>
                                    </Link>
                                )}
                                {!isAuthed && canLogin && (
                                    <Link
                                        href={route('login')}
                                        className="hidden min-w-[148px] shrink-0 items-center gap-3 rounded-full border border-white/15 bg-white/10 px-4 py-2.5 text-left shadow-[0_6px_14px_-10px_rgba(9,20,48,0.7)] sm:inline-flex"
                                    >
                                        <UserIcon className="h-6 w-6 shrink-0 text-white" />
                                        <span className="min-w-0 leading-none">
                                            <span className="block text-[10px] font-semibold text-white/75">Welcome</span>
                                            <span className="block whitespace-nowrap text-[13px] font-black tracking-[-0.02em]">
                                                Sign In / Register
                                            </span>
                                        </span>
                                    </Link>
                                )}
                                {isAuthed && (
                                    <Link
                                        href={route('dashboard')}
                                        className="inline-flex min-w-[148px] shrink-0 items-center gap-3 rounded-full border border-white/15 bg-white/10 px-4 py-2.5 text-left shadow-[0_6px_14px_-10px_rgba(9,20,48,0.7)] sm:hidden"
                                    >
                                        <UserIcon className="h-6 w-6 shrink-0 text-white" />
                                        <span className="min-w-0 leading-none">
                                            <span className="block whitespace-nowrap text-[11px] font-black tracking-[-0.02em]">
                                                Dashboard
                                            </span>
                                        </span>
                                    </Link>
                                )}
                                {isAuthed && (
                                    <Link
                                        href={route('dashboard')}
                                        className="hidden min-w-[148px] shrink-0 items-center gap-3 rounded-full border border-white/15 bg-white/10 px-4 py-2.5 text-left shadow-[0_6px_14px_-10px_rgba(9,20,48,0.7)] sm:inline-flex"
                                    >
                                        <UserIcon className="h-6 w-6 shrink-0 text-white" />
                                        <span className="min-w-0 leading-none">
                                            <span className="block text-[10px] font-semibold text-white/75">Welcome</span>
                                            <span className="block whitespace-nowrap text-[13px] font-black tracking-[-0.02em]">
                                                Dashboard
                                            </span>
                                        </span>
                                    </Link>
                                )}

                                <Link
                                    href={route('cart.index')}
                                    className="relative inline-flex h-12 w-12 items-center justify-center rounded-full border border-white/15 bg-white/10 text-white sm:hidden"
                                >
                                    <CartIcon className="h-6 w-6" />
                                    {Number(cartCount) > 0 && (
                                        <span className="absolute -right-1 -top-1 inline-flex min-w-5 items-center justify-center rounded-full bg-[#ff8a00] px-1.5 py-0.5 text-[10px] font-black leading-none text-white shadow-sm">
                                            {Number(cartCount) > 99 ? '99+' : Number(cartCount)}
                                        </span>
                                    )}
                                </Link>

                                <Link href={route('cart.index')} className="relative hidden sm:inline-flex h-12 w-12 items-center justify-center rounded-full border border-white/15 bg-white/10 text-white transition hover:bg-white/20">
                                    <CartIcon className="h-6 w-6" />
                                    {Number(cartCount) > 0 && (
                                        <span className="absolute -right-1 -top-1 inline-flex min-w-5 items-center justify-center rounded-full bg-[#ff8a00] px-1.5 py-0.5 text-[10px] font-black leading-none text-white shadow-sm">
                                            {Number(cartCount) > 99 ? '99+' : Number(cartCount)}
                                        </span>
                                    )}
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div className="border-b border-[#d8e4f5] bg-[#f8fbff] text-[#0b2e71]">
                        <div className="mx-auto flex w-full max-w-[1900px] flex-wrap items-center gap-2 px-4 py-2 sm:px-6 xl:px-8">
                            <span className="hidden shrink-0 text-[10px] font-black uppercase tracking-[0.24em] text-[#0b2e71]/55 sm:inline-flex">
                                Quick filters
                            </span>
                            {quickNavLinks.map((link) => (
                                <Link
                                    key={link.label}
                                    href={link.href}
                                    className="shrink-0 whitespace-nowrap rounded-full border border-[#d7e3f4] bg-white px-3 py-1.5 text-xs font-black uppercase tracking-[0.14em] text-[#0b2e71] transition hover:border-[#ffb16d] hover:bg-[#fff3e8] hover:text-[#d75d00]"
                                >
                                    {link.label}
                                </Link>
                            ))}
                        </div>
                    </div>

                </header>
            <div aria-hidden="true" style={{ height: headerHeight }} />
            
            {children}

            <footer id="footer" className="relative z-10 w-full mt-4">
                    {/* Top Dark Section */}
                    <div
                        className="pt-10 md:pt-16 pb-12"
                        style={{
                            background: 'linear-gradient(90deg, #0b2e71 0%, #1f68d9 100%)',
                        }}
                    >
                        <div className="mx-auto w-full max-w-[1900px] px-6 sm:px-10 lg:px-16 xl:px-20">
                            <div className="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-5 gap-2 md:gap-6 xl:gap-4">
                                {footerSections.map((section) => (
                                    <FooterColumn key={section.title} section={section} />
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Bottom Light Blue Section */}
                    <div
                        className="border-t border-blue-100 py-6"
                        style={{
                            background: 'linear-gradient(90deg, #eaf2ff 0%, #f8fbff 100%)',
                        }}
                    >
                        <div className="mx-auto w-full max-w-[1900px] px-6 lg:px-16 xl:px-20 flex flex-col md:flex-row items-center justify-between gap-6">
                            <div className="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6 text-xs sm:text-sm text-[#0b2e71]">
                                <span>© 2000-{new Date().getFullYear()} PlexusBiz Inc. All rights reserved.</span>
                                <div className="flex flex-wrap justify-center sm:justify-start items-center gap-3">
                                    <a href="#" className="font-medium hover:text-[#1f68d9] hover:underline whitespace-nowrap">Terms & Conditions</a>
                                    <a href="#" className="font-medium hover:text-[#1f68d9] hover:underline whitespace-nowrap">Privacy Policy</a>
                                    <a href="#" className="flex items-center gap-1 font-medium hover:text-[#1f68d9] hover:underline whitespace-nowrap">
                                        Your Privacy Choices
                                        <svg className="w-5 h-5 text-[#1f68d9]" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            
                            {/* Social Icons matching the dark circles with white logos */}
                            <div className="flex items-center gap-2.5">
                                {[
                                    { name: 'Reddit', icon: 'M24 11.5c0-1.65-1.35-3-3-3-.41 0-.79.08-1.14.24-2.09-1.45-4.96-2.39-8.11-2.5l1.72-3.66 4.79 1.02c.04.88.77 1.58 1.66 1.58 1.15 0 2.08-.93 2.08-2.08s-.93-2.08-2.08-2.08c-.97 0-1.78.67-2.01 1.58l-5.12-1.09c-.19-.04-.39.05-.48.23l-1.92 4.09c-3.23.05-6.2.98-8.38 2.47-.35-.16-.73-.24-1.14-.24-1.65 0-3 1.35-3 3 0 1.25.77 2.32 1.86 2.76-.04.24-.06.49-.06.74 0 3.86 4.48 7 10 7s10-3.14 10-7c0-.25-.02-.5-.06-.74 1.09-.44 1.86-1.51 1.86-2.76zM7 11.5c0-1.1 0.9-2 2-2s2 0.9 2 2-0.9 2-2 2-2-0.9-2-2zm10 5.5c-1.31 1.31-3.6 1.5-5 1.5s-3.69-.19-5-1.5c-.2-.2-.2-.51 0-.71.19-.19.51-.19.71 0 1.04 1.04 3.01 1.21 4.29 1.21s3.25-.17 4.29-1.21c0.2-.2 0.51-.2 0.71 0 0.19 0.2 0.19 0.51 0 0.71zm0-3.5c-1.1 0-2-.9-2-2s0.9-2 2-2 2 0.9 2 2-0.9 2-2 2z' },
                                    { name: 'Facebook', icon: 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z' },
                                    { name: 'Twitter', icon: 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z' },
                                    { name: 'Instagram', icon: 'M12 2c2.717 0 3.056.01 4.122.06 1.065.05 1.79.217 2.428.465.66.254 1.216.598 1.772 1.153a4.908 4.908 0 0 1 1.153 1.772c.247.637.415 1.363.465 2.428.047 1.066.06 1.405.06 4.122 0 2.717-.01 3.056-.06 4.122-.05 1.065-.218 1.79-.465 2.428a4.883 4.883 0 0 1-1.153 1.772 4.915 4.915 0 0 1-1.772 1.153c-.637.247-1.363.415-2.428.465-1.066.047-1.405.06-4.122.06-2.717 0-3.056-.01-4.122-.06-1.065-.05-1.79-.218-2.428-.465a4.89 4.89 0 0 1-1.772-1.153 4.904 4.904 0 0 1-1.153-1.772c-.248-.637-.415-1.363-.465-2.428C2.013 15.056 2 14.717 2 12c0-2.717.01-3.056.06-4.122.05-1.066.217-1.79.465-2.428a4.88 4.88 0 0 1 1.153-1.772A4.897 4.897 0 0 1 5.45 2.525c.638-.248 1.362-.415 2.428-.465C8.944 2.013 9.283 2 12 2zm0 5a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 8.2a3.2 3.2 0 1 1 0-6.4 3.2 3.2 0 0 1 0 6.4zm5.838-8.22a1.196 1.196 0 1 1-2.392 0 1.196 1.196 0 0 1 2.392 0z' },
                                    { name: 'LinkedIn', icon: 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z' },
                                    { name: 'Pinterest', icon: 'M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.966 1.406-5.966s-.359-.72-.359-1.781c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738.098.119.112.224.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.261 7.929-7.261 4.162 0 7.398 2.966 7.398 6.931 0 4.135-2.607 7.462-6.223 7.462-1.214 0-2.354-.631-2.745-1.373l-.749 2.853c-.27 1.031-1.002 2.324-1.492 3.12 1.066.303 2.191.467 3.352.467 6.621 0 11.988-5.367 11.988-11.987C24.02 5.367 18.638 0 12.017 0z' },
                                    { name: 'YouTube', icon: 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.5 12 3.5 12 3.5s-7.505 0-9.377.55a3.016 3.016 0 0 0-2.122 2.136C0 8.07 0 12 0 12s0 3.93.498 5.814a3.016 3.016 0 0 0 2.122 2.136c1.872.55 9.377.55 9.377.55s7.505 0 9.377-.55a3.016 3.016 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z' },
                                    { name: 'Twitch', icon: 'M11.571 4.714h1.715v5.143H11.571zm4.715 0h1.714v5.143h-1.714zm-10.286 0L3.429 6.857v12.429h4.714V23l3.857-3.857h3.429l5.571-5.571V4.714zm14.571 8.572-2.571 2.571h-3.857l-3 3v-3H7.286V6.429h12.429z' },
                                    { name: 'Discord', icon: 'M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128c.126-.094.252-.192.372-.291a.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z' },
                                    { name: 'TikTok', icon: 'M12.525.02c1.31-.032 2.622.016 3.924.062.03.1.063.2.096.3.262.804.664 1.547 1.184 2.193.633.784 1.42 1.436 2.296 1.91 1.002.54 2.103.856 3.23.93v4.064c-1.385-.015-2.766-.35-4.043-1.008-1.045-.537-1.956-1.328-2.656-2.28-.016.27-.033.54-.05 8.118-.03 1.838-.475 3.65-1.3 5.258-1.003 1.962-2.73 3.522-4.832 4.364-2.146.858-4.52 1.032-6.758.502-2.316-.547-4.417-1.92-5.882-3.843-1.464-1.923-2.15-4.316-1.918-6.697.232-2.38 1.36-4.595 3.16-6.195 1.8-1.6 4.144-2.455 6.556-2.4 1.54.034 3.053.486 4.363 1.304v4.453c-1.042-.644-2.253-.984-3.486-.984-1.233 0-2.444.34-3.486.984-.972.6-1.745 1.488-2.22 2.546-.476 1.058-.62 2.235-.414 3.38.206 1.144.773 2.193 1.627 3.013.854.82 1.95 1.36 3.148 1.55 1.198.19 2.428-.007 3.535-.565 1.107-.557 2.023-1.45 2.63-2.56.607-1.11.884-2.384.796-3.66L12.525.02z' }
                                ].map((social) => (
                                    <a key={social.name} href="#" className="flex h-6 w-6 items-center justify-center rounded-full text-[#0d1733] transition hover:opacity-75">
                                        <svg className="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d={social.icon} />
                                        </svg>
                                    </a>
                                ))}
                            </div>
                        </div>
                    </div>
                </footer>

            <AddressSelectorModal 
                isOpen={isAddressModalOpen} 
                onClose={() => setIsAddressModalOpen(false)} 
                isAuthed={isAuthed}
            />
        </div>
    );
}
