import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const moduleConfig = {
    eyebrow: 'Admin Panel',
    tag: 'Audit',
    theme: 'workflow',
    heroTitle: 'Audit trail',
    heroCopy: 'Review critical admin and automation changes with actor, subject, and request context in a dedicated screen.',
    panelTitle: 'What this page covers',
    panelCopy: 'Search module keys, actions, and actor details without mixing the audit trail into the admin home screen.',
    highlights: [
        {
            label: 'Actor context',
            detail: 'Every row keeps the user or system actor visible for traceability.',
        },
        {
            label: 'Request context',
            detail: 'IP, subject, and description stay attached to the change record.',
        },
        {
            label: 'Searchable trail',
            detail: 'Search terms flow into module key, action, subject, and actor matching.',
        },
    ],
    panelBullets: [
        {
            label: 'Admin changes',
            detail: 'Surface account, supplier, and setting updates as part of the same trace.',
        },
        {
            label: 'Workflow changes',
            detail: 'Keep automation rule edits and execution events easy to inspect.',
        },
        {
            label: 'Live filters',
            detail: 'Search and status filters remain synced with the backend query.',
        },
    ],
    actions: [
        { label: 'Admin Control', href: '/admin', variant: 'primary' },
        { label: 'Workflow Logs', href: '/workflow/logs', variant: 'secondary' },
    ],
    tableTitle: 'Audit Events',
    searchPlaceholder: 'Search audit logs',
    emptyTitle: 'No audit logs yet',
};

export default function Index(props) {
    return <ModuleWorkspacePage {...props} module={moduleConfig} />;
}
