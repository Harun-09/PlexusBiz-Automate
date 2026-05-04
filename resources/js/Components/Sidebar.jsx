import ApplicationLogo from '@/Components/ApplicationLogo';
import SidebarItem from '@/Components/SidebarItem';
import { Link } from '@inertiajs/react';

const hasRole = (roles, allowed) => allowed.length === 0 || allowed.some((role) => roles.includes(role));

export const SIDEBAR_SECTIONS = [
    {
        label: 'Workspace',
        items: [
            { label: 'Dashboard', href: '/dashboard', icon: 'D', roles: [] },
            { label: 'Marketplace Catalog', href: '/marketplace', icon: 'M', roles: ['buyer', 'admin'] },
            { label: 'Cart', href: '/cart', icon: 'C', roles: ['buyer'] },
            { label: 'Notifications', href: '/notifications', icon: 'N', roles: [] },
            { label: 'Profile', href: '/profile', icon: 'P', roles: [] },
        ],
    },
    {
        label: 'Admin',
        items: [
            { label: 'Admin Dashboard', href: '/admin', icon: 'A', roles: ['admin'] },
            { label: 'Users', href: '/admin/users', icon: 'U', roles: ['admin'] },
            { label: 'Suppliers', href: '/admin/suppliers', icon: 'S', roles: ['admin'] },
            { label: 'Products', href: '/admin/products', icon: 'P', roles: ['admin'] },
            { label: 'Customers', href: '/admin/customers', icon: 'C', roles: ['admin'] },
            { label: 'Module Settings', href: '/settings/modules', icon: 'MS', roles: ['admin'] },
            { label: 'Audit Logs', href: '/admin/audit-logs', icon: 'AL', roles: ['admin'] },
        ],
    },
    {
        label: 'Commerce',
        items: [
            { label: 'Products', href: '/commerce/products', icon: 'P', roles: ['supplier', 'admin'] },
            { label: 'Orders', href: '/commerce/orders', icon: 'O', roles: ['buyer', 'supplier', 'admin'] },
            { label: 'Invoices', href: '/invoices', icon: 'I', roles: ['buyer', 'supplier', 'admin'] },
        ],
    },
    {
        label: 'Growth',
        items: [
            { label: 'Campaigns', href: '/marketing/campaigns', icon: 'C', roles: ['marketing_manager', 'admin'] },
            { label: 'Social Calendar', href: '/social/calendar', icon: 'SC', roles: ['marketing_manager', 'admin'] },
        ],
    },
    {
        label: 'Automation',
        items: [
            { label: 'Workflow Logs', href: '/workflow/logs', icon: 'WL', roles: ['workflow_manager', 'marketing_manager', 'admin'] },
            { label: 'Failed Logs', href: '/workflow/logs?status=failed', icon: 'F', roles: ['workflow_manager', 'marketing_manager', 'admin'] },
        ],
    },
    {
        label: 'Support',
        items: [
            { label: 'Support Tickets', href: '/support/tickets', icon: 'ST', roles: ['buyer', 'supplier', 'admin'] },
        ],
    },
];

export function navigationForRoles(roles = []) {
    return SIDEBAR_SECTIONS
        .map((section) => ({
            ...section,
            items: section.items.filter((item) => hasRole(roles, item.roles)),
        }))
        .filter((section) => section.items.length > 0);
}

export default function Sidebar({ user, currentPath, onNavigate = null }) {
    const roles = user?.roles || [];
    const sections = navigationForRoles(roles);
    const isActive = (href) => {
        const [path] = href.split('?');
        return currentPath === path || (path !== '/' && currentPath.startsWith(`${path}/`));
    };

    return (
        <aside className="flex h-full min-h-0 flex-col border-r border-slate-200 bg-white">
            <div className="flex h-16 items-center gap-3 border-b border-slate-200 px-4">
                <Link href="/dashboard" className="flex items-center gap-3">
                    <span className="grid h-10 w-10 place-items-center rounded-lg bg-slate-950">
                        <ApplicationLogo className="h-6 w-6 text-white" />
                    </span>
                    <span className="leading-tight">
                        <span className="block text-sm font-black text-slate-950">PlexusBiz</span>
                        <span className="block text-xs font-semibold text-slate-500">PLEXUS CLOUD</span>
                    </span>
                </Link>
            </div>

            <nav className="app-shell-scrollbar min-h-0 flex-1 space-y-5 overflow-y-auto px-3 py-4">
                {sections.map((section) => (
                    <div key={section.label}>
                        <p className="px-3 text-[11px] font-black uppercase tracking-wider text-slate-400">{section.label}</p>
                        <div className="mt-2 space-y-1">
                            {section.items.map((item) => (
                                <SidebarItem key={`${section.label}-${item.href}-${item.label}`} item={item} active={isActive(item.href)} onNavigate={onNavigate} />
                            ))}
                        </div>
                    </div>
                ))}
            </nav>
        </aside>
    );
}
