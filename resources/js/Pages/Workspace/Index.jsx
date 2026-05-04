import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

export default function WorkspaceIndex(props) {
    const workspace = props.workspace || {};
    const statusCount = workspace.filters?.statuses?.length || 0;
    const rowCount = Array.isArray(workspace.rows) ? workspace.rows.length : 0;

    const moduleConfig = {
        eyebrow: 'Workspace',
        tag: rowCount > 0 ? `${rowCount} rows` : 'Generic list',
        theme: 'slate',
        heroTitle: workspace.title || 'Operational table',
        heroCopy: workspace.description || 'A reusable table shell for backend lists that need filters, metrics, and action-aware rows.',
        panelTitle: workspace.title ? `${workspace.title} behavior` : 'Shared table behavior',
        panelCopy: workspace.description
            ? `This view uses the same backend contract as other list pages, but the title and summary stay specific to ${workspace.title.toLowerCase()}.`
            : 'Search and status filters are preserved, backend links still render as actions, and the same data contract powers CRM and utility pages.',
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
                detail: `${rowCount} records are rendered in a consistent data grid.`,
            },
            {
                label: 'Filter controls',
                detail: statusCount > 0
                    ? `${statusCount} status chips stay in sync with the current route.`
                    : 'Search controls stay in sync with the current route.',
            },
            {
                label: 'Live actions',
                detail: 'Any action/link object continues to work from the backend payload.',
            },
        ],
    };

    return <ModuleWorkspacePage {...props} module={moduleConfig} />;
}
