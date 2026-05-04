import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const moduleConfig = {
    eyebrow: 'Workspace',
    tag: 'Generic',
    theme: 'slate',
    heroTitle: 'Shared operational tables',
    heroCopy: 'A reusable table shell for workspace and CRM lists that need filters, metrics, and object-aware row rendering.',
    panelTitle: 'Shared table behavior',
    panelCopy: 'Search and status filters are preserved, backend links still render as actions, and the same data contract powers CRM and utility pages.',
    highlights: [
        {
            label: 'Filter aware',
            detail: 'Search terms and status chips flow directly into the backend query.',
        },
        {
            label: 'Object aware',
            detail: 'Payment summaries, actions, links, and status values render without custom table code.',
        },
        {
            label: 'Reusable shell',
            detail: 'The same layout can power different modules without duplicating page markup.',
        },
    ],
    panelBullets: [
        {
            label: 'Table rows',
            detail: 'Records are limited and rendered in a consistent data grid.',
        },
        {
            label: 'Filter controls',
            detail: 'Search and status filters stay in sync with the current route.',
        },
        {
            label: 'Live actions',
            detail: 'Any action/link object continues to work from the backend payload.',
        },
    ],
};

export default function WorkspaceIndex(props) {
    return <ModuleWorkspacePage {...props} module={moduleConfig} />;
}
