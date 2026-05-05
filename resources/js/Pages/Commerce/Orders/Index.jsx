import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const moduleConfig = {
    eyebrow: 'E-Commerce',
    tag: 'Orders',
    theme: 'workflow',
    heroTitle: 'Order management',
    heroCopy: 'Track buyer, supplier, payment, and fulfillment state from cart to checkout to confirmation.',
    panelTitle: 'What this page covers',
    panelCopy: 'The order list keeps payment action objects and order state visible per role without blending it into other modules. Buyer checkout automatically creates supplier fulfillment rows behind the scenes.',
    highlights: [
        { label: 'Cart to checkout', detail: 'Orders stay visible from placement through the post-checkout flow.' },
        { label: 'Payment state', detail: 'Payment summary and action objects continue to render from backend payloads.' },
        { label: 'Role scope', detail: 'Buyer, supplier, and admin views stay aligned with the current user role.' },
    ],
    panelBullets: [
        { label: 'Confirmation flow', detail: 'Buyer orders are confirmed at checkout, then split into supplier fulfillment rows automatically.' },
        { label: 'Receipt access', detail: 'Paid orders can still expose the receipt or continue payment action.' },
        { label: 'Support overlap', detail: 'Problem orders can be routed to support without leaving the workspace.' },
    ],
    actions: [
        { label: 'Marketplace Catalog', href: '/marketplace', variant: 'primary' },
        { label: 'Support Tickets', href: '/support/tickets', variant: 'secondary' },
    ],
    tableTitle: 'Order queue',
    searchPlaceholder: 'Search order number',
    emptyTitle: 'No orders found',
};

export default function Index(props) {
    return <ModuleWorkspacePage {...props} module={moduleConfig} />;
}
