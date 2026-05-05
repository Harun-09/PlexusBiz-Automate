import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const moduleConfig = {
    eyebrow: 'E-Commerce',
    tag: 'Fulfillment',
    theme: 'workflow',
    heroTitle: 'Supplier order fulfillment',
    heroCopy: 'Approved suppliers and admins can move supplier order lines through confirmation, shipping, and completion.',
    panelTitle: 'What this page covers',
    panelCopy: 'This screen keeps supplier-specific fulfillment rows separate from the buyer order queue so status actions stay focused.',
    highlights: [
        { label: 'Status actions', detail: 'Pending orders can be confirmed, then shipped, and completed from the same queue.' },
        { label: 'Supplier scope', detail: 'Suppliers only see their own fulfillment rows while admins keep platform-wide visibility.' },
        { label: 'Operational timing', detail: 'Placed, confirmed, shipped, and completed timestamps stay visible for review.' },
    ],
    panelBullets: [
        { label: 'Approval gating', detail: 'Only approved suppliers can act on their own supplier orders.' },
        { label: 'Order flow', detail: 'Use this queue after checkout when items are split by supplier.' },
        { label: 'Admin oversight', detail: 'Admins can update any supplier order without leaving the commerce workspace.' },
    ],
    actions: [
        { label: 'Orders', href: '/commerce/orders', variant: 'primary' },
        { label: 'Products', href: '/commerce/products', variant: 'secondary' },
    ],
    tableTitle: 'Supplier fulfillment queue',
    searchPlaceholder: 'Search supplier order or order number',
    emptyTitle: 'No supplier orders found',
};

export default function SupplierOrdersIndex(props) {
    return <ModuleWorkspacePage {...props} module={moduleConfig} />;
}
