import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const moduleConfig = {
    eyebrow: 'CRM',
    tag: 'Interactions',
    theme: 'crm',
    heroTitle: 'Interaction timeline',
    heroCopy: 'Bring messages, support tickets, orders, RFQ events, and internal notes together so every customer touchpoint is traceable.',
    panelTitle: 'What this page covers',
    panelCopy: 'This is the CRM activity stream for the current account base, designed to help support, sales, and marketing read the same history.',
    highlights: [
        { label: 'Activity types', detail: 'Message, support ticket, order, RFQ, and note events remain in one timeline.' },
        { label: 'Actor tracking', detail: 'The user or system actor for each event is preserved.' },
        { label: 'Related record', detail: 'Interactions can still be traced back to the related commerce item.' },
    ],
    panelBullets: [
        { label: 'Support ready', detail: 'A single history makes customer support easier to answer quickly.' },
        { label: 'Sales follow-up', detail: 'Open interactions can drive reminders and lead conversion work.' },
        { label: 'Event driven', detail: 'CRM activity can feed workflow automation later.' },
    ],
    actions: [
        { label: 'View Customers', href: '/crm/customers', variant: 'primary' },
        { label: 'View Leads', href: '/crm/leads', variant: 'secondary' },
    ],
    tableTitle: 'Activity History',
};

export default function Index(props) {
    return <ModuleWorkspacePage {...props} module={moduleConfig} />;
}
