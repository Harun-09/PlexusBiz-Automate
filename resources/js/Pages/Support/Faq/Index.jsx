import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const moduleConfig = {
    eyebrow: 'Order & Support Automation',
    tag: 'FAQ',
    theme: 'support',
    heroTitle: 'Chatbot-ready knowledge base',
    heroCopy: 'Keep prioritized answers, keywords, and the active/inactive state visible so self-service and auto-response flows stay useful.',
    panelTitle: 'What this page covers',
    panelCopy: 'FAQs should be short, searchable, and ready to power future chatbot or auto-reply experiences.',
    highlights: [
        {
            label: 'Priority order',
            detail: 'Higher-priority answers stay at the top for support and bot lookups.',
        },
        {
            label: 'Keyword matching',
            detail: 'Keywords remain visible to help users and bots find the right answer.',
        },
        {
            label: 'Status control',
            detail: 'Active and inactive FAQ rows stay filterable for content maintenance.',
        },
    ],
    panelBullets: [
        {
            label: 'Chatbot-ready',
            detail: 'FAQ content can be reused by a lightweight assistant or response bot later.',
        },
        {
            label: 'Buyer support',
            detail: 'Frequently asked support topics stay close to the ticketing workflow.',
        },
        {
            label: 'Content review',
            detail: 'Inactive entries can be retired without losing the record history.',
        },
    ],
    actions: [
        { label: 'Open Tickets', href: '/support/tickets', variant: 'primary' },
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
