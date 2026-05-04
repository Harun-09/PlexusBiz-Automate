import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const moduleConfig = {
    eyebrow: 'Admin Panel',
    tag: 'Customers',
    theme: 'crm',
    heroTitle: 'Customer registry',
    heroCopy: 'Admin overview of customer accounts, lifecycle stage, and order activity in a dedicated administrative list.',
    panelTitle: 'What this page covers',
    panelCopy: 'This is the administrative customer list, separate from the CRM team view that handles lifecycle and interaction work.',
    highlights: [
        { label: 'Profiles', detail: 'Contact name, company name, and linked email stay visible together.' },
        { label: 'Lifecycle', detail: 'Active, inactive, and repeat-customer states remain filterable.' },
        { label: 'Commerce context', detail: 'Order count, total spent, and last order timing stay visible.' },
    ],
    panelBullets: [
        { label: 'Admin scope', detail: 'This page is for broad account oversight rather than CRM workflow tasks.' },
        { label: 'Support ready', detail: 'Customer records remain easy to cross-check during support and order review.' },
        { label: 'Searchable list', detail: 'Name, company, and email filters flow directly into the query.' },
    ],
    actions: [
        { label: 'CRM Customers', href: '/crm/customers', variant: 'primary' },
        { label: 'Purchase History', href: '/crm/purchases', variant: 'secondary' },
    ],
    tableTitle: 'Customer Accounts',
    searchPlaceholder: 'Search customer accounts',
    emptyTitle: 'No customer accounts found',
};

export default function Index(props) {
    return <ModuleWorkspacePage {...props} module={moduleConfig} />;
}
