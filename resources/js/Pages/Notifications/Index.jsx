import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const moduleConfig = {
    eyebrow: 'Automation',
    tag: 'Messages',
    theme: 'workflow',
    heroTitle: 'Notification inbox',
    heroCopy: 'Review in-app, email, SMS, and system messages without burying them inside another workspace.',
    panelTitle: 'What this page covers',
    panelCopy: 'This route keeps the notification feed separate from dashboards and other operational tables.',
    highlights: [
        { label: 'Channel visibility', detail: 'In-app, email, SMS, and system messages stay separated in one list.' },
        { label: 'Sender context', detail: 'From and to fields remain visible for message tracing.' },
        { label: 'Unread state', detail: 'Unread counts and message status stay available for review.' },
    ],
    panelBullets: [
        { label: 'Inbox scope', detail: 'Use this feed to inspect customer and supplier notifications.' },
        { label: 'Automation output', detail: 'Workflow and support automation can surface messages here.' },
        { label: 'Searchable feed', detail: 'Subject, body, and channel search stay linked to the current route.' },
    ],
    actions: [
        { label: 'Support Tickets', href: '/support/tickets', variant: 'primary' },
        { label: 'CRM Interactions', href: '/crm/interactions', variant: 'secondary' },
    ],
    tableTitle: 'Messages',
    searchPlaceholder: 'Search subject or body',
    emptyTitle: 'No notifications found',
};

export default function Index(props) {
    return <ModuleWorkspacePage {...props} module={moduleConfig} />;
}
