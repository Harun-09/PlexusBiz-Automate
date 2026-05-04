import { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';

const navigation = [
    { name: 'Dashboard', href: route('dashboard'), icon: HomeIcon, current: true },
    { name: 'Products', href: route('admin.products.index'), icon: ShoppingBagIcon, current: false },
    { name: 'Users', href: route('admin.users.index'), icon: UsersIcon, current: false },
    { name: 'Suppliers', href: route('admin.suppliers.index'), icon: TruckIcon, current: false },
    { name: 'Analytics', href: '#analytics', icon: ChartBarIcon, current: false },
    { name: 'Settings', href: '#settings', icon: Cog6ToothIcon, current: false },
];

function classNames(...classes) {
    return classes.filter(Boolean).join(' ');
}

function IconSvg({ className = 'h-5 w-5', children }) {
    return (
        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="1.8"
            strokeLinecap="round"
            strokeLinejoin="round"
            className={className}
            aria-hidden="true"
        >
            {children}
        </svg>
    );
}

function Bars3Icon(props) {
    return (
        <IconSvg {...props}>
            <path d="M4 6h16" />
            <path d="M4 12h16" />
            <path d="M4 18h16" />
        </IconSvg>
    );
}

function XMarkIcon(props) {
    return (
        <IconSvg {...props}>
            <path d="M6 6l12 12" />
            <path d="M18 6 6 18" />
        </IconSvg>
    );
}

function HomeIcon(props) {
    return (
        <IconSvg {...props}>
            <path d="M3.5 11.5 12 4l8.5 7.5" />
            <path d="M5 10.5V20a1 1 0 0 0 1 1h4.5v-6h3v6H18a1 1 0 0 0 1-1v-9.5" />
        </IconSvg>
    );
}

function UsersIcon(props) {
    return (
        <IconSvg {...props}>
            <circle cx="9" cy="8" r="3" />
            <path d="M3.5 20v-1.2A4.8 4.8 0 0 1 8.3 14h1.4A4.8 4.8 0 0 1 14.5 18.8V20" />
            <circle cx="17" cy="9" r="2.2" />
            <path d="M14.8 20v-1a3.7 3.7 0 0 1 3.7-3.7h.5A3.7 3.7 0 0 1 22.7 19v1" />
        </IconSvg>
    );
}

function ShoppingBagIcon(props) {
    return (
        <IconSvg {...props}>
            <path d="M6 8h12l-1 11H7L6 8Z" />
            <path d="M9 8a3 3 0 0 1 6 0" />
        </IconSvg>
    );
}

function TruckIcon(props) {
    return (
        <IconSvg {...props}>
            <path d="M3 7h11v8H3z" />
            <path d="M14 10h4l3 3v2h-7z" />
            <circle cx="7" cy="17" r="2" />
            <circle cx="17" cy="17" r="2" />
        </IconSvg>
    );
}

function ChartBarIcon(props) {
    return (
        <IconSvg {...props}>
            <path d="M5 19V5" />
            <path d="M9 19v-6" />
            <path d="M13 19v-9" />
            <path d="M17 19v-3" />
            <path d="M3 19h18" />
        </IconSvg>
    );
}

function Cog6ToothIcon(props) {
    return (
        <IconSvg {...props}>
            <circle cx="12" cy="12" r="3" />
            <path d="M12 2.8v2.2" />
            <path d="M12 19v2.2" />
            <path d="M4.9 4.9l1.6 1.6" />
            <path d="M17.5 17.5l1.6 1.6" />
            <path d="M2.8 12h2.2" />
            <path d="M19 12h2.2" />
            <path d="M4.9 19.1l1.6-1.6" />
            <path d="M17.5 6.5l1.6-1.6" />
        </IconSvg>
    );
}

function ArrowRightOnRectangleIcon(props) {
    return (
        <IconSvg {...props}>
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
            <path d="M10 16l5-4-5-4" />
            <path d="M15 12H3" />
        </IconSvg>
    );
}

export default function AdminLayout({ children, header }) {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const { auth } = usePage().props;

    return (
        <div className="min-h-screen bg-[#f1f5f9]">
            {/* Mobile sidebar */}
            <div className={`fixed inset-0 z-50 lg:hidden ${sidebarOpen ? 'block' : 'hidden'}`}>
                <div className="fixed inset-0 bg-gray-900/80 transition-opacity" onClick={() => setSidebarOpen(false)} />
                <div className="fixed inset-y-0 left-0 z-50 w-72 bg-[#0b2e71] overflow-y-auto">
                    <div className="flex items-center justify-between px-6 py-4 border-b border-white/10">
                        <Link href={route('welcome')} className="flex items-center gap-3">
                            <span className="text-2xl font-black text-white tracking-tight">Plexus<span className="text-[#ff8a00]">Biz</span></span>
                        </Link>
                        <button onClick={() => setSidebarOpen(false)} className="text-white/70 hover:text-white">
                            <XMarkIcon className="h-6 w-6" />
                        </button>
                    </div>
                    <nav className="flex flex-col gap-1 p-4">
                        {navigation.map((item) => (
                            <Link
                                key={item.name}
                                href={item.href}
                                className={classNames(
                                    item.current
                                        ? 'bg-[#ff8a00] text-white'
                                        : 'text-white/80 hover:bg-white/10 hover:text-white',
                                    'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition-all duration-200'
                                )}
                            >
                                <item.icon className="h-5 w-5" />
                                {item.name}
                            </Link>
                        ))}
                    </nav>
                </div>
            </div>

            {/* Desktop sidebar */}
            <div className="hidden lg:fixed lg:inset-y-0 lg:z-40 lg:flex lg:w-72 lg:flex-col">
                <div className="flex grow flex-col gap-y-5 overflow-y-auto bg-[#0b2e71] px-6 pb-4">
                    <div className="flex h-20 items-center border-b border-white/10">
                        <Link href={route('welcome')} className="flex items-center gap-3">
                            <span className="text-2xl font-black text-white tracking-tight">Plexus<span className="text-[#ff8a00]">Biz</span></span>
                        </Link>
                    </div>
                    <nav className="flex flex-1 flex-col gap-1">
                        {navigation.map((item) => (
                            <Link
                                key={item.name}
                                href={item.href}
                                className={classNames(
                                    route().current(item.href.replace('#', '')) || item.current
                                        ? 'bg-[#ff8a00] text-white shadow-lg shadow-orange-500/25'
                                        : 'text-white/80 hover:bg-white/10 hover:text-white',
                                    'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition-all duration-200'
                                )}
                            >
                                <item.icon className="h-5 w-5" />
                                {item.name}
                            </Link>
                        ))}
                    </nav>
                    <div className="border-t border-white/10 pt-4">
                        <div className="flex items-center gap-3 px-4 py-3">
                            <div className="h-10 w-10 rounded-full bg-[#ff8a00] flex items-center justify-center text-white font-bold">
                                {auth.user?.name?.charAt(0) || 'A'}
                            </div>
                            <div className="flex-1 min-w-0">
                                <p className="text-sm font-semibold text-white truncate">{auth.user?.name || 'Admin User'}</p>
                                <p className="text-xs text-white/60 truncate">{auth.user?.email || 'admin@plexusbiz.com'}</p>
                            </div>
                        </div>
                        <Link
                            href={route('logout')}
                            method="post"
                            as="button"
                            className="mt-2 flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-white/80 hover:bg-white/10 hover:text-white transition-all duration-200"
                        >
                            <ArrowRightOnRectangleIcon className="h-5 w-5" />
                            Sign out
                        </Link>
                    </div>
                </div>
            </div>

            {/* Main content */}
            <div className="lg:pl-72">
                {/* Top header */}
                <div className="sticky top-0 z-30 flex h-20 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                    <button
                        type="button"
                        className="-m-2.5 p-2.5 text-gray-700 lg:hidden"
                        onClick={() => setSidebarOpen(true)}
                    >
                        <span className="sr-only">Open sidebar</span>
                        <Bars3Icon className="h-6 w-6" aria-hidden="true" />
                    </button>

                    <div className="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                        <div className="flex flex-1 items-center">
                            {header && (
                                <div>
                                    <h1 className="text-2xl font-bold text-gray-900">{header.title}</h1>
                                    {header.subtitle && (
                                        <p className="text-sm text-gray-500">{header.subtitle}</p>
                                    )}
                                </div>
                            )}
                        </div>
                        <div className="flex items-center gap-x-4 lg:gap-x-6">
                            <Link
                                href={route('welcome')}
                                className="text-sm font-semibold text-[#0b2e71] hover:text-[#ff8a00] transition-colors"
                            >
                                View Site
                            </Link>
                        </div>
                    </div>
                </div>

                {/* Page content */}
                <main className="py-8 px-4 sm:px-6 lg:px-8">
                    {children}
                </main>
            </div>
        </div>
    );
}
