import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const moduleConfig = {
    eyebrow: 'CRM',
    tag: 'Purchases',
    theme: 'crm',
    heroTitle: 'Purchase history',
    heroCopy: 'Track customer orders, invoice links, and payment state so account managers can reason about buying behavior quickly.',
    panelTitle: 'What this page covers',
    panelCopy: 'This page ties CRM profiles back to commerce by showing order totals, invoices, and fulfillment status in a single list.',
    highlights: [
        { label: 'Order context', detail: 'Buyer, customer, and order number stay side by side for quick lookup.' },
        { label: 'Payment state', detail: 'Pending, completed, and other payment states stay filterable.' },
        { label: 'Invoice link', detail: 'Each row shows the invoice number when one exists.' },
    ],
    panelBullets: [
        { label: 'Cross-module data', detail: 'CRM and commerce remain linked through the same order row.' },
        { label: 'Revenue tracking', detail: 'Completed order values make it easy to spot high-value customers.' },
        { label: 'Action ready', detail: 'Invoice actions still work from the same backend payload.' },
    ],
    actions: [
        { label: 'Back to Customers', href: '/crm/customers', variant: 'primary' },
        { label: 'View Segments', href: '/crm/segments', variant: 'secondary' },
    ],
    tableTitle: 'Order History',
};

export default function Index(props) {
    return <ModuleWorkspacePage {...props} module={moduleConfig} />;
}
