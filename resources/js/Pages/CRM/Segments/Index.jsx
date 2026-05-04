import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const moduleConfig = {
    eyebrow: 'CRM',
    tag: 'Segments',
    theme: 'crm',
    heroTitle: 'Customer segments',
    heroCopy: 'Maintain the basic audience buckets used for marketing targeting, customer prioritization, and follow-up planning.',
    panelTitle: 'What this page covers',
    panelCopy: 'Segments are computed from saved filters, so the page keeps both the rule summary and audience size visible.',
    highlights: [
        { label: 'Saved rules', detail: 'Filter criteria remain visible as a human-readable summary.' },
        { label: 'Audience size', detail: 'The live count is calculated from the current CRM dataset.' },
        { label: 'Status control', detail: 'Active and inactive segments stay easy to toggle and scan.' },
    ],
    panelBullets: [
        { label: 'Campaign targeting', detail: 'Segments feed marketing campaigns and customer outreach lists.' },
        { label: 'Priority accounts', detail: 'Use tags and lifecycle stage to isolate key wholesale accounts.' },
        { label: 'Live counts', detail: 'Audience totals are derived from the current segmentation service.' },
    ],
    actions: [
        { label: 'View Customers', href: '/crm/customers', variant: 'primary' },
        { label: 'Open Leads', href: '/crm/leads', variant: 'secondary' },
    ],
    tableTitle: 'Saved Segments',
};

export default function Index(props) {
    return <ModuleWorkspacePage {...props} module={moduleConfig} />;
}
