import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const moduleConfig = {
    eyebrow: 'Marketplace',
    tag: 'B2B',
    theme: 'slate',
    heroTitle: 'Marketplace directory',
    heroCopy: 'Browse live B2B products with supplier, MOQ, and available stock context in a buyer-facing screen.',
    panelTitle: 'What this page covers',
    panelCopy: 'This is the live catalog view for buyers and suppliers, separate from the admin inventory page.',
    highlights: [
        { label: 'Live catalog', detail: 'Active products are shown with the current supplier and price context.' },
        { label: 'MOQ visibility', detail: 'Bulk order thresholds stay visible while browsing the catalog.' },
        { label: 'Stock snapshot', detail: 'Available stock helps buyers judge quantity before checkout.' },
    ],
    panelBullets: [
        { label: 'Bulk orders', detail: 'Jump to the bulk-order path when large quantities need a dedicated flow.' },
        { label: 'MOQ pricing', detail: 'Switch to the pricing tier view when quantity-based pricing matters.' },
        { label: 'Catalog search', detail: 'Search by product name or supplier and keep results synced to the route.' },
    ],
    actions: [
        { label: 'Bulk Orders', href: '/products/bulk-orders', variant: 'primary' },
        { label: 'MOQ Pricing', href: '/products/moq-pricing', variant: 'secondary' },
    ],
    tableTitle: 'Active catalog',
    searchPlaceholder: 'Search products or suppliers',
    emptyTitle: 'No marketplace products found',
};

export default function Index(props) {
    return <ModuleWorkspacePage {...props} module={moduleConfig} />;
}
