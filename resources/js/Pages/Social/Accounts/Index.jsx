import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const moduleConfig = {
    eyebrow: 'Social Media Automation',
    tag: 'Accounts',
    theme: 'social',
    heroTitle: 'Account registry',
    heroCopy: 'Create and review publishing accounts, active or inactive state, and the platform split without leaving the automation module.',
    panelTitle: 'What this page covers',
    panelCopy: 'The list is meant to keep publishing identities visible so add, edit, and mock API modes remain easy to audit.',
    highlights: [
        {
            label: 'Active state',
            detail: 'Active and inactive accounts stay filterable for quick operations checks.',
        },
        {
            label: 'Platform mix',
            detail: 'Facebook and Instagram accounts can be compared in the same grid.',
        },
        {
            label: 'Post volume',
            detail: 'The post count shows how much content each account is carrying.',
        },
    ],
    panelBullets: [
        {
            label: 'Mock mode',
            detail: 'Registry metadata can still capture whether the account is live or mocked.',
        },
        {
            label: 'Publishing health',
            detail: 'Account status should match the current delivery state before scheduling posts.',
        },
        {
            label: 'Content routing',
            detail: 'Posts stay tied to the correct account and platform in the row data.',
        },
    ],
    actions: [
        { label: 'Add Account', href: '/social/accounts/create', variant: 'primary' },
        { label: 'Open Calendar', href: '/social/calendar', variant: 'secondary' },
        { label: 'Review Posts', href: '/social/posts', variant: 'secondary' },
    ],
};

export default function Index(props) {
    return <ModuleWorkspacePage {...props} module={moduleConfig} />;
}
