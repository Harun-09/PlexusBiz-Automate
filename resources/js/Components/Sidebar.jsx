import SidebarItem from '@/Components/SidebarItem';
import { canAccess } from '@/Utils/access';
import { Link } from '@inertiajs/react';
import { useState } from 'react';

const SIDEBAR_MODULES = [
    {
        key: 'ecommerce',
        label: 'E-Commerce',
        roles: ['buyer', 'supplier', 'admin'],
        icon: 'EC',
        items: [
            { label: 'Marketplace Catalog', href: '/marketplace', icon: 'M', roles: ['buyer', 'supplier', 'admin'] },
            { label: 'Bulk Orders', href: '/products/bulk-orders', icon: 'BO', roles: ['buyer', 'supplier', 'admin'] },
            { label: 'MOQ Pricing', href: '/products/moq-pricing', icon: 'MQ', roles: ['buyer', 'supplier', 'admin'] },
            { label: 'Cart', href: '/cart', icon: 'C', roles: ['buyer'], permissions: ['manage_cart'] },
            { label: 'Supplier Onboarding', href: '/admin/suppliers', icon: 'SO', roles: ['admin'], permissions: ['manage_suppliers'] },
            { label: 'Product CRUD', href: '/admin/products', icon: 'PR', roles: ['admin'], permissions: ['manage_products'] },
            { label: 'Inventory & Stock', href: '/commerce/products', icon: 'IS', roles: ['supplier', 'admin'], permissions: ['manage_own_products', 'manage_products'], requiresSupplierApproval: true },
            { label: 'Add Product', href: '/commerce/products/create', icon: 'AP', roles: ['supplier'], permissions: ['manage_own_products'], requiresSupplierApproval: true },
            { label: 'Orders', href: '/commerce/orders', icon: 'O', roles: ['buyer', 'supplier', 'admin'] },
            { label: 'Supplier Orders', href: '/commerce/supplier-orders', icon: 'SO', roles: ['supplier', 'admin'], permissions: ['manage_own_orders', 'manage_orders'], requiresSupplierApproval: true },
            { label: 'Invoices', href: '/invoices', icon: 'I', roles: ['buyer', 'supplier', 'admin'] },
        ],
    },
    {
        key: 'crm',
        label: 'CRM',
        roles: ['admin', 'marketing_manager'],
        icon: 'CR',
        items: [
            { label: 'Customers', href: '/crm/customers', icon: 'CU', roles: ['admin', 'marketing_manager'] },
            { label: 'Purchase History', href: '/crm/purchases', icon: 'PH', roles: ['admin', 'marketing_manager'] },
            { label: 'Segments', href: '/crm/segments', icon: 'SG', roles: ['admin', 'marketing_manager'] },
            { label: 'Leads', href: '/crm/leads', icon: 'LD', roles: ['admin', 'marketing_manager'] },
            { label: 'Interactions', href: '/crm/interactions', icon: 'IN', roles: ['admin', 'marketing_manager'] },
        ],
    },
    {
        key: 'social',
        label: 'Social Media Automation',
        roles: ['marketing_manager', 'admin'],
        icon: 'SM',
        items: [
            { label: 'Social Calendar', href: '/social/calendar', icon: 'C', roles: ['marketing_manager', 'admin'] },
            { label: 'Scheduled Posts', href: '/social/posts', icon: 'P', roles: ['marketing_manager', 'admin'] },
            { label: 'Social Accounts', href: '/social/accounts', icon: 'A', roles: ['marketing_manager', 'admin'] },
        ],
    },
    {
        key: 'marketing',
        label: 'Marketing Automation',
        roles: ['marketing_manager', 'admin'],
        icon: 'MK',
        items: [
            { label: 'Campaigns', href: '/marketing/campaigns', icon: 'C', roles: ['marketing_manager', 'admin'] },
            { label: 'Campaign Templates', href: '/marketing/templates', icon: 'T', roles: ['marketing_manager', 'admin'] },
        ],
    },
    {
        key: 'workflow',
        label: 'Workflow Automation',
        roles: ['workflow_manager', 'marketing_manager', 'admin'],
        icon: 'WF',
        items: [
            { label: 'Automation Rules', href: '/workflow/rules', icon: 'R', roles: ['workflow_manager', 'marketing_manager', 'admin'] },
            { label: 'Workflow Logs', href: '/workflow/logs', icon: 'WL', roles: ['workflow_manager', 'marketing_manager', 'admin'] },
            { label: 'Failed Logs', href: '/workflow/logs?status=failed', icon: 'F', roles: ['workflow_manager', 'marketing_manager', 'admin'] },
        ],
    },
    {
        key: 'admin',
        label: 'Admin Panel',
        roles: ['admin'],
        icon: 'AD',
        items: [
            { label: 'Admin Dashboard', href: '/admin', icon: 'A', roles: ['admin'] },
            { label: 'Users', href: '/admin/users', icon: 'U', roles: ['admin'] },
            { label: 'Bulk Pricing & MOQ', href: '/admin/bulk-pricing', icon: 'BP', roles: ['admin'] },
            { label: 'Module Settings', href: '/settings/modules', icon: 'MS', roles: ['admin'] },
            { label: 'Audit Logs', href: '/admin/audit-logs', icon: 'AL', roles: ['admin'] },
        ],
    },
    {
        key: 'support',
        label: 'Order & Support Automation',
        roles: ['buyer', 'supplier', 'admin', 'marketing_manager'],
        icon: 'SP',
        items: [
            { label: 'Support Tickets', href: '/support/tickets', icon: 'ST', roles: ['buyer', 'supplier', 'admin'], permissions: ['manage_own_tickets', 'manage_tickets'] },
            { label: 'Support FAQ', href: '/support/faq', icon: 'Q', roles: ['buyer', 'supplier', 'admin', 'marketing_manager'] },
        ],
    },
];

function Chevron({ className = '' }) {
    return (
        <svg aria-hidden="true" viewBox="0 0 24 24" className={className} fill="none">
            <path d="m6 9 6 6 6-6" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
        </svg>
    );
}

function visibleModules(user) {
    return SIDEBAR_MODULES
        .map((module) => ({
            ...module,
            items: module.items.filter((item) => canAccess(user, item)),
        }))
        .filter((module) => canAccess(user, module) && module.items.length > 0);
}

export default function Sidebar({ user, currentPath, onNavigate = null }) {
    const modules = visibleModules(user);
    const [openKeys, setOpenKeys] = useState([]);

    const isActive = (href) => {
        const [path] = href.split('?');
        return currentPath === path || (path !== '/' && currentPath.startsWith(`${path}/`));
    };

    const toggleModule = (key) => {
        setOpenKeys((previous) => (
            previous.includes(key)
                ? previous.filter((entry) => entry !== key)
                : [...previous, key]
        ));
    };

    const activeModuleKey = modules.find((module) => module.items.some((item) => isActive(item.href)))?.key ?? null;

    const isOpen = (key) => openKeys.includes(key) || activeModuleKey === key;

    return (
        <aside className="flex h-full min-h-0 flex-col border-r border-slate-800 bg-slate-950">
            <div className="flex h-16 items-center border-b border-white/10 px-4">
                <Link href={route('dashboard')} className="flex items-center gap-3">
                    <img
                        src="/images/project-logo.png"
                        alt="PlexusBiz"
                        className="h-10 w-10 rounded-lg bg-white object-cover shadow"
                    />
                    <span className="leading-tight">
                        <span className="block text-sm font-black text-white">PlexusBiz</span>
                        <span className="block text-[11px] font-semibold uppercase text-slate-400">
                            e-commerce hub
                        </span>
                    </span>
                </Link>
            </div>

            <nav className="app-shell-scrollbar min-h-0 flex-1 space-y-5 overflow-y-auto px-3 py-4">
                <div>
                    <p className="px-3 text-[11px] font-black uppercase text-slate-500">Workspace</p>
                    <div className="mt-2 space-y-1">
                        <SidebarItem
                            item={{ label: 'Dashboard', href: '/dashboard', icon: 'D' }}
                            active={isActive('/dashboard')}
                            onNavigate={onNavigate}
                        />
                    </div>
                </div>

                {modules.map((module) => {
                    const open = isOpen(module.key);

                    return (
                        <div key={module.key} className="rounded-lg border border-white/10 bg-white/[0.04]">
                            <button
                                type="button"
                                onClick={() => toggleModule(module.key)}
                                className="flex w-full items-center gap-3 px-4 py-3 text-left"
                                aria-expanded={open}
                            >
                                <span className="grid h-9 w-9 shrink-0 place-items-center rounded-md bg-white/10 text-[11px] font-black text-slate-200 ring-1 ring-white/10">
                                    {module.icon}
                                </span>
                                <span className="min-w-0 flex-1">
                                    <span className="block text-sm font-black text-white">{module.label}</span>
                                    <span className="block text-[11px] font-semibold uppercase text-slate-500">
                                        {module.items.length} menus
                                    </span>
                                </span>
                                <Chevron className={`h-4 w-4 shrink-0 text-slate-400 transition ${open ? 'rotate-180' : ''}`} />
                            </button>

                            {open ? (
                                <div className="space-y-1 px-3 pb-3">
                                    {module.items.map((item) => (
                                        <SidebarItem
                                            key={`${module.key}-${item.href}-${item.label}`}
                                            item={item}
                                            active={isActive(item.href)}
                                            onNavigate={onNavigate}
                                        />
                                    ))}
                                </div>
                            ) : null}
                        </div>
                    );
                })}
            </nav>
        </aside>
    );
}
