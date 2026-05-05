import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const moduleConfig = {
    eyebrow: 'Marketing Automation',
    tag: 'Templates',
    theme: 'marketing',
    heroTitle: 'Template library',
    heroCopy: 'Maintain reusable email templates that can be reused by triggered campaigns and scheduled sends.',
    panelTitle: 'What this page covers',
    panelCopy: 'Templates are the reusable content layer for marketing automation, so the page keeps subject, body, variables, and email channel visible together.',
    highlights: [
        {
            label: 'Email coverage',
            detail: 'Email templates stay in the same view for easy comparison.',
        },
        {
            label: 'Variables',
            detail: 'Placeholder variables remain visible so template readiness is obvious.',
        },
        {
            label: 'Campaign fallback',
            detail: 'Standalone templates and campaign-linked templates both render cleanly.',
        },
    ],
    panelBullets: [
        {
            label: 'Template reuse',
            detail: 'The same asset can feed multiple campaign flows or trigger rules.',
        },
        {
            label: 'Subject clarity',
            detail: 'Email subjects remain visible alongside the body preview and key data.',
        },
        {
            label: 'Inactive rows',
            detail: 'Inactive templates stay filterable for content cleanup and audits.',
        },
    ],
    actions: [
        { label: 'Create Template', href: '/marketing/templates/create', variant: 'primary' },
        { label: 'Back to Email Campaigns', href: '/marketing/campaigns', variant: 'secondary' },
        {
            label: 'Open Workflow Rules',
            href: '/workflow/rules',
            variant: 'secondary',
            permissions: ['manage_automation_rules'],
        },
    ],
};

export default function Index(props) {
    return <ModuleWorkspacePage {...props} module={moduleConfig} />;
}
