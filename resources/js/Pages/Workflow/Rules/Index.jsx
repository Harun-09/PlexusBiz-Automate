import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const moduleConfig = {
    eyebrow: 'Workflow Automation',
    tag: 'Rules',
    theme: 'workflow',
    heroTitle: 'Automation rule engine',
    heroCopy: 'Define IF condition THEN action rules for orders, RFQs, tickets, and other event-driven processes.',
    panelTitle: 'What this page covers',
    panelCopy: 'Rules are the control layer for the workflow engine, so priority, runtime mode, and rule payload details remain visible together.',
    highlights: [
        {
            label: 'IF / THEN logic',
            detail: 'Conditions and actions are displayed side by side in the same row.',
        },
        {
            label: 'Runtime mode',
            detail: 'Sync and async execution stay visible to help with scheduling decisions.',
        },
        {
            label: 'Priority order',
            detail: 'Rule priority is surfaced so the engine can be reasoned about quickly.',
        },
    ],
    panelBullets: [
        {
            label: 'Event triggers',
            detail: 'Order placed, RFQ created, and similar events can drive automation here.',
        },
        {
            label: 'Action chains',
            detail: 'Multiple actions can execute from the same rule when needed.',
        },
        {
            label: 'Execution logs',
            detail: 'Run counts connect directly to the workflow log page.',
        },
    ],
    actions: [
        { label: 'Create Rule', href: '/workflow/rules/create', variant: 'primary' },
        { label: 'Open Logs', href: '/workflow/logs', variant: 'secondary' },
        { label: 'View Support FAQ', href: '/support/faq', variant: 'secondary' },
    ],
};

export default function Index(props) {
    return <ModuleWorkspacePage {...props} module={moduleConfig} />;
}
