import { Link } from '@inertiajs/react';

const blueSurfaceGradient = 'bg-gradient-to-r from-[#4f7fe0] via-[#3f70d4] to-[#2953b1]';

const footerSections = [
    {
        title: 'Quick Links',
        links: [
            { label: 'Marketplace', href: '/marketplace' },
            { label: 'Become a supplier', href: route('supplier.apply') },
            { label: 'About Us', href: '/about' },
            { label: 'Contact', href: '/contact' },
        ],
    },
    {
        title: 'Support',
        links: [
            { label: 'Help Center', href: '/support/tickets' },
            { label: 'FAQs', href: '/faq' },
        ],
    },
    {
        title: 'Contact',
        links: [
            { label: 'Dhaka, Bangladesh', text: true },
            { label: 'support@plexusbiz.com', href: 'mailto:support@plexusbiz.com' },
        ],
    },
    {
        title: 'Legal',
        links: [
            { label: 'Terms', href: '/terms' },
            { label: 'Privacy', href: '/privacy' },
        ],
    },
];

export default function FooterSimple() {
    return (
        <footer id="footer" className="w-full text-white">
            <div
                className={`border-t border-white/10 py-10 ${blueSurfaceGradient}`}
            >
                <div className="mx-auto w-full px-4 sm:px-6 lg:px-8 xl:px-10">
                    <div className="mb-8 flex flex-col gap-5 border-b border-white/20 pb-6 sm:flex-row sm:items-end sm:justify-between">
                        <Link href="/" className="group inline-flex items-center gap-4">
                            <span className="grid h-16 w-16 shrink-0 place-items-center rounded-[22px] border border-white/15 bg-white p-2 shadow-[0_18px_42px_-26px_rgba(0,0,0,0.45)] transition group-hover:scale-[1.02]">
                                <img
                                    src="/images/project-logo.png"
                                    alt="PlexusBiz Automate"
                                    className="h-full w-full rounded-[16px] object-cover"
                                />
                            </span>
                            <span className="min-w-0 space-y-1">
                                <span className="inline-flex w-fit rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[10px] font-black uppercase tracking-[0.24em] text-[#ffd59a]">
                                    B2B Marketplace
                                </span>
                                <span className="flex flex-wrap items-baseline gap-x-2 gap-y-1">
                                    <span className="text-[2rem] font-black tracking-[-0.07em] text-white transition group-hover:text-[#ffd59a] sm:text-[2.2rem]">
                                        PlexusBiz
                                    </span>
                                    <span className="text-[11px] font-semibold uppercase tracking-[0.38em] text-white/65">
                                        Commerce Hub
                                    </span>
                                </span>
                                <span className="block max-w-xl text-sm leading-6 text-white/78">
                                    B2B commerce, supplier onboarding, bulk orders, invoices, and support in one place.
                                </span>
                            </span>
                        </Link>

                        <div className="flex flex-wrap gap-2 text-xs font-black uppercase tracking-[0.18em] text-white/70 sm:justify-end">
                            <span className="rounded-full border border-white/15 bg-white/10 px-3 py-1">Bulk Orders</span>
                            <span className="rounded-full border border-white/15 bg-white/10 px-3 py-1">Supplier Tools</span>
                            <span className="rounded-full border border-white/15 bg-white/10 px-3 py-1">Invoices</span>
                        </div>
                    </div>

                    <div className="grid grid-cols-2 gap-8 text-sm md:grid-cols-4">
                        {footerSections.map((section) => (
                            <div key={section.title}>
                                <h4 className="mb-3 font-semibold">{section.title}</h4>
                                <ul className="space-y-2 text-white/90">
                                    {section.links.map((link) => (
                                        <li key={link.label}>
                                            {link.text ? (
                                                <span>{link.label}</span>
                                            ) : link.href.startsWith('mailto:') || link.href.startsWith('#') ? (
                                                <a href={link.href} className="hover:text-white">
                                                    {link.label}
                                                </a>
                                            ) : (
                                                <Link href={link.href} className="hover:text-white">
                                                    {link.label}
                                                </Link>
                                            )}
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        ))}
                    </div>

                    <div className="mt-8 border-t border-white/20 pt-4 text-center text-sm text-white/90">
                        &copy; 2003-{new Date().getFullYear()} PlexusBiz Automate. All rights reserved.
                    </div>
                </div>
            </div>
        </footer>
    );
}
