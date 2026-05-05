import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const moduleConfig = {
    eyebrow: 'Marketing Automation',
    tag: 'Email Campaigns',
    theme: 'marketing',
    heroTitle: 'Email campaign command center',
    heroCopy: 'Keep email campaign status, templates, recipients, and delivery logs in one place so trigger-based marketing stays observable.',
    panelTitle: 'What this page covers',
    panelCopy: 'This module is for email campaign operations: template linkage, delivery bookkeeping, and enough visibility to manage basic automation safely.',
    highlights: [
        {
            label: 'Template linkage',
            detail: 'Each campaign shows how many email templates are attached to it.',
        },
        {
            label: 'Recipient scope',
            detail: 'Recipient totals and logs remain visible for delivery checks.',
        },
        {
            label: 'Lifecycle status',
            detail: 'Draft, scheduled, and active campaign states stay filterable.',
        },
    ],
    panelBullets: [
        {
            label: 'Trigger campaigns',
            detail: 'Use campaign records as the backbone for welcome, reminder, and confirmation email flows.',
        },
        {
            label: 'Delivery logs',
            detail: 'Log counts help confirm which campaigns actually ran.',
        },
        {
            label: 'Template reuse',
            detail: 'Campaign templates remain available as reusable marketing assets.',
        },
    ],
    actions: [
        { label: 'Create Campaign', href: '/marketing/campaigns/create', variant: 'primary' },
        { label: 'View Templates', href: '/marketing/templates', variant: 'secondary' },
        {
            label: 'Inspect Workflow Rules',
            href: '/workflow/rules',
            variant: 'secondary',
            permissions: ['manage_automation_rules'],
        },
    ],
};

export default function Index(props) {
    return <ModuleWorkspacePage {...props} module={moduleConfig} />;
}
