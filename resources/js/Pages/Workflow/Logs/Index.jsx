import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const moduleConfig = {
    eyebrow: 'Workflow Automation',
    tag: 'Logs',
    theme: 'workflow',
    heroTitle: 'Execution ledger',
    heroCopy: 'Review success, failure, and runtime state for each automation event without needing to inspect the underlying jobs manually.',
    panelTitle: 'What this page covers',
    panelCopy: 'Logs show how the automation engine behaved in production so failures, skips, and successful runs stay easy to trace.',
    highlights: [
        {
            label: 'Outcome states',
            detail: 'Running, success, failed, and skipped states remain available for filtering.',
        },
        {
            label: 'Rule linkage',
            detail: 'Each entry shows which rule produced the event.',
        },
        {
            label: 'Payload snapshot',
            detail: 'The table keeps full payload and result JSON visible for proof and debugging.',
        },
    ],
    panelBullets: [
        {
            label: 'Recent runs',
            detail: 'The newest execution records appear first for quick triage.',
        },
        {
            label: 'Trigger payloads',
            detail: 'Execution history makes the originating event, result, and error easy to trace.',
        },
        {
            label: 'Failure review',
            detail: 'A failed row can immediately lead back to the related rule.',
        },
    ],
    actions: [
        {
            label: 'Inspect Rules',
            href: '/workflow/rules',
            variant: 'primary',
            permissions: ['manage_automation_rules'],
        },
        { label: 'Open Support FAQ', href: '/support/faq', variant: 'secondary' },
    ],
};

export default function Index(props) {
    return <ModuleWorkspacePage {...props} module={moduleConfig} />;
}
