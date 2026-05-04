import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const moduleConfig = {
    eyebrow: 'CRM',
    tag: 'Customers',
    theme: 'crm',
    heroTitle: 'Customer registry',
    heroCopy: 'Register customers, profile their account data, and keep lifecycle stage, spending, and order history visible in one place.',
    panelTitle: 'What this page covers',
    panelCopy: 'This view is the CRM entry point for customer registration and account profiling, with direct access to the detailed customer record.',
    highlights: [
        { label: 'Profiles', detail: 'Contact name, company name, and linked user account stay visible together.' },
        { label: 'Lifecycle', detail: 'Active, inactive, customer, and repeat-customer states remain filterable.' },
        { label: 'Revenue view', detail: 'Order totals and last purchase timing help identify high-value accounts.' },
    ],
    panelBullets: [
        { label: 'Customer detail', detail: 'Open a profile to inspect leads, interactions, and recent commerce activity.' },
        { label: 'Segmentation ready', detail: 'Tags and lifecycle stage feed into the basic segmentation rules.' },
        { label: 'Account context', detail: 'Each row keeps email, phone, and order count available for support and marketing.' },
    ],
    actions: [
        { label: 'Purchase History', href: '/crm/purchases', variant: 'primary' },
        { label: 'Lead Management', href: '/crm/leads', variant: 'secondary' },
    ],
    tableTitle: 'Customer Profiles',
};

export default function Index(props) {
    return <ModuleWorkspacePage {...props} module={moduleConfig} />;
}
