import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const moduleConfig = {
    eyebrow: 'CRM',
    tag: 'Leads',
    theme: 'crm',
    heroTitle: 'Lead management',
    heroCopy: 'Track prospect ownership, follow-up timing, and pipeline value so marketing and sales work from the same record.',
    panelTitle: 'What this page covers',
    panelCopy: 'Lead records stay close to the customer data so qualification, follow-up, and conversion work remain coordinated.',
    highlights: [
        { label: 'Assignment', detail: 'Assigned owner and follow-up date remain easy to scan.' },
        { label: 'Pipeline value', detail: 'Lead values remain visible for rough forecasting.' },
        { label: 'Source tracking', detail: 'Lead origin and contact details are kept in the same list.' },
    ],
    panelBullets: [
        { label: 'Qualification', detail: 'Qualified, converted, and open leads stay filterable.' },
        { label: 'Follow-up', detail: 'The next touchpoint date is visible for team scheduling.' },
        { label: 'Ownership', detail: 'Marketing or sales can quickly see which user owns each lead.' },
    ],
    actions: [
        { label: 'Create Lead', href: '/crm/leads/create', variant: 'primary' },
        { label: 'Open Interactions', href: '/crm/interactions', variant: 'secondary' },
        { label: 'Customer Profiles', href: '/crm/customers', variant: 'secondary' },
    ],
    tableTitle: 'Lead Pipeline',
};

export default function Index(props) {
    return <ModuleWorkspacePage {...props} module={moduleConfig} />;
}
