import SidebarItem, { SidebarIcon } from '@/Components/SidebarItem';
import {
    AlertTriangleIcon,
    CalendarDaysIcon,
    ChatBubbleIcon,
    ChartBarIcon,
    ClipboardListIcon,
    ClockIcon,
    CubeIcon,
    DashboardIcon,
    DocumentTextIcon,
    FileSearchIcon,
    FunnelIcon,
    IdentificationIcon,
    LifeBuoyIcon,
    MegaphoneIcon,
    PlusSquareIcon,
    QuestionMarkCircleIcon,
    CalculatorIcon,
    ShoppingCartIcon,
    ShieldCheckIcon,
    SlidersHorizontalIcon,
    SparklesIcon,
    StorefrontIcon,
    TargetIcon,
    TagIcon,
    TruckIcon,
    UsersIcon,
    WorkflowIcon,
} from '@/Components/SidebarIcons';
import { canAccess } from '@/Utils/access';
import { assetHref, normalizedPathAndQuery } from '@/Utils/url';
import { Link } from '@inertiajs/react';
import { useState } from 'react';

const SIDEBAR_MODULES = [
    {
        key: 'ecommerce',
        label: 'E-Commerce',
        roles: ['buyer', 'supplier', 'admin'],
        icon: StorefrontIcon,
        items: [
            { label: 'Marketplace Catalog', href: route('marketplace.index'), icon: StorefrontIcon, roles: ['buyer', 'supplier', 'admin'] },
            { label: 'Bulk Orders', href: route('products.bulk'), icon: ShoppingCartIcon, roles: ['buyer', 'supplier', 'admin'] },
            { label: 'MOQ Pricing', href: route('products.moq'), icon: TagIcon, roles: ['buyer', 'supplier', 'admin'] },
            { label: 'Cart', href: route('cart.index'), icon: ShoppingCartIcon, roles: ['buyer'], permissions: ['manage_cart'] },
            { label: 'Supplier Onboarding', href: route('admin.suppliers.index'), icon: TruckIcon, roles: ['admin'], permissions: ['manage_suppliers'] },
            { label: 'Product CRUD', href: route('admin.products.index'), icon: CubeIcon, roles: ['admin'], permissions: ['manage_products'] },
            { label: 'Inventory & Stock', href: route('commerce.products.index'), icon: ChartBarIcon, roles: ['supplier', 'admin'], permissions: ['manage_own_products', 'manage_products'], requiresSupplierApproval: true },
            { label: 'Add Product', href: route('commerce.products.create'), icon: PlusSquareIcon, roles: ['supplier'], permissions: ['manage_own_products'], requiresSupplierApproval: true },
            { label: 'Orders', href: route('commerce.orders.index'), icon: ClipboardListIcon, roles: ['buyer', 'supplier', 'admin'] },
            { label: 'Supplier Orders', href: route('commerce.supplier-orders.index'), icon: TruckIcon, roles: ['supplier', 'admin'], permissions: ['manage_own_orders', 'manage_orders'], requiresSupplierApproval: true },
            { label: 'Invoices', href: route('invoices.index'), icon: DocumentTextIcon, roles: ['buyer', 'supplier', 'admin'] },
        ],
    },
    {
        key: 'crm',
        label: 'CRM',
        roles: ['admin', 'marketing_manager'],
        icon: UsersIcon,
        items: [
            { label: 'Customers', href: route('crm.customers.index'), icon: UsersIcon, roles: ['admin', 'marketing_manager'] },
            { label: 'Purchase History', href: route('crm.purchases.index'), icon: ClockIcon, roles: ['admin', 'marketing_manager'] },
            { label: 'Segments', href: route('crm.segments.index'), icon: FunnelIcon, roles: ['admin', 'marketing_manager'] },
            { label: 'Leads', href: route('crm.leads.index'), icon: TargetIcon, roles: ['admin', 'marketing_manager'] },
            { label: 'Interactions', href: route('crm.interactions.index'), icon: ChatBubbleIcon, roles: ['admin', 'marketing_manager'] },
        ],
    },
    {
        key: 'social',
        label: 'Social Media Automation',
        roles: ['marketing_manager', 'admin'],
        icon: MegaphoneIcon,
        items: [
            { label: 'Social Campaigns', href: route('social.posts.index'), icon: MegaphoneIcon, roles: ['marketing_manager', 'admin'], permissions: ['manage_social_posts'] },
            { label: 'Social Calendar', href: route('social.calendar'), icon: CalendarDaysIcon, roles: ['marketing_manager', 'admin'], permissions: ['manage_social_posts'] },
            { label: 'Scheduled Posts', href: route('social.posts.scheduled'), icon: ClockIcon, roles: ['marketing_manager', 'admin'], permissions: ['manage_social_posts'] },
            { label: 'Social Accounts', href: route('social.accounts.index'), icon: IdentificationIcon, roles: ['marketing_manager', 'admin'], permissions: ['manage_social_accounts'] },
        ],
    },
    {
        key: 'marketing',
        label: 'Marketing Automation',
        roles: ['marketing_manager', 'admin'],
        icon: SparklesIcon,
        items: [
            { label: 'Email Campaigns', href: route('marketing.campaigns.index'), icon: MegaphoneIcon, roles: ['marketing_manager', 'admin'] },
            { label: 'Campaign Templates', href: route('marketing.templates.index'), icon: DocumentTextIcon, roles: ['marketing_manager', 'admin'] },
        ],
    },
    {
        key: 'workflow',
        label: 'Workflow Automation',
        roles: ['workflow_manager', 'marketing_manager', 'admin'],
        icon: WorkflowIcon,
        items: [
            { label: 'Automation Rules', href: route('workflow.rules.index'), icon: WorkflowIcon, roles: ['workflow_manager', 'marketing_manager', 'admin'] },
            { label: 'Workflow Logs', href: route('workflow.logs.index'), icon: ClipboardListIcon, roles: ['workflow_manager', 'marketing_manager', 'admin'] },
            { label: 'Failed Logs', href: route('workflow.logs.index', { status: 'failed' }), icon: AlertTriangleIcon, roles: ['workflow_manager', 'marketing_manager', 'admin'] },
        ],
    },
    {
        key: 'admin',
        label: 'Admin Panel',
        roles: ['admin'],
        icon: ShieldCheckIcon,
        items: [
            { label: 'Admin Dashboard', href: route('admin.dashboard'), icon: DashboardIcon, roles: ['admin'] },
            { label: 'Users', href: route('admin.users.index'), icon: UsersIcon, roles: ['admin'] },
            { label: 'Bulk Pricing & MOQ', href: route('admin.bulk-pricing.index'), icon: CalculatorIcon, roles: ['admin'] },
            { label: 'Module Settings', href: route('settings.modules.index'), icon: SlidersHorizontalIcon, roles: ['admin'] },
            { label: 'Audit Logs', href: route('admin.audit-logs'), icon: FileSearchIcon, roles: ['admin'] },
        ],
    },
    {
        key: 'support',
        label: 'Order & Support Automation',
        roles: ['buyer', 'supplier', 'admin', 'marketing_manager'],
        icon: LifeBuoyIcon,
        items: [
            { label: 'Support Tickets', href: route('support.tickets.index'), icon: LifeBuoyIcon, roles: ['buyer', 'supplier', 'admin'], permissions: ['manage_own_tickets', 'manage_tickets'] },
            { label: 'Support FAQ', href: route('support.faq.index'), icon: QuestionMarkCircleIcon, roles: ['buyer', 'supplier', 'admin', 'marketing_manager'] },
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

    const currentUrl = normalizedPathAndQuery(currentPath);
    const [currentUrlPath] = currentUrl.split('?');

    const splitHref = (href) => {
        const normalized = normalizedPathAndQuery(href);
        const [path, search = ''] = normalized.split('?');
        return {
            path,
            search: search ? `?${search}` : '',
        };
    };

    const hasMatchingQueryItem = (moduleItems) => moduleItems.some((item) => {
        const { search } = splitHref(item.href);
        return search && item.href === currentUrl;
    });

    const isActive = (href, moduleItems = []) => {
        const { path, search } = splitHref(href);

        if (search) {
            return currentUrl === href;
        }

        const matchesPath = currentUrlPath === path || (path !== '/' && currentUrlPath.startsWith(`${path}/`));

        return matchesPath && !hasMatchingQueryItem(moduleItems);
    };

    const toggleModule = (key) => {
        setOpenKeys((previous) => (
            previous.includes(key)
                ? previous.filter((entry) => entry !== key)
                : [...previous, key]
        ));
    };

    const activeModuleKey = modules.find((module) => module.items.some((item) => isActive(item.href, module.items)))?.key ?? null;

    const isOpen = (key) => openKeys.includes(key) || activeModuleKey === key;

    return (
        <aside className="flex h-full min-h-0 flex-col border-r border-slate-800 bg-slate-950">
            <div className="flex h-16 items-center border-b border-white/10 px-4">
                <Link href={route('dashboard')} className="flex items-center gap-3">
                    <img
                        src={assetHref('/images/project-logo.png')}
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
                            item={{ label: 'Dashboard', href: route('dashboard'), icon: DashboardIcon }}
                            active={isActive(route('dashboard'))}
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
                                <SidebarIcon icon={module.icon} size="module" active={open} />
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
                                            active={isActive(item.href, module.items)}
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
