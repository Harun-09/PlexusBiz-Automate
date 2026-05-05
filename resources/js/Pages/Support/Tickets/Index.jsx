import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const moduleConfig = {
    eyebrow: 'Order & Support Automation',
    tag: 'Tickets',
    theme: 'support',
    heroTitle: 'Support command board',
    heroCopy: 'Track buyer and supplier tickets, status, priority, and the latest update without mixing support work into other modules.',
    panelTitle: 'What this page covers',
    panelCopy: 'Tickets stay focused on customer and supplier support flows so auto responses and notification rules have a clean home.',
    highlights: [
        {
            label: 'Ticket routing',
            detail: 'Buyer and supplier requests stay visible in the same operational view.',
        },
        {
            label: 'Priority handling',
            detail: 'High, medium, and low priorities remain easy to sort and scan.',
        },
        {
            label: 'Last update',
            detail: 'The most recent message time keeps active threads obvious.',
        },
    ],
    panelBullets: [
        {
            label: 'Auto replies',
            detail: 'This module is the entry point for templated response automation.',
        },
        {
            label: 'Supplier alerts',
            detail: 'Tickets can drive supplier notifications when order issues appear.',
        },
        {
            label: 'Status flow',
            detail: 'Open, pending, waiting supplier, resolved, and closed states stay filterable.',
        },
    ],
    actions: [
        { label: 'Create Ticket', href: route('support.tickets.create'), variant: 'primary' },
        { label: 'Open FAQ', href: '/support/faq', variant: 'primary' },
        { label: 'Check CRM Customers', href: '/crm/customers', variant: 'secondary' },
    ],
};

export default function Index(props) {
    return <ModuleWorkspacePage {...props} module={moduleConfig} />;
}
