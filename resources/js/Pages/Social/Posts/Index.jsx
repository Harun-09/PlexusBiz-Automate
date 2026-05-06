import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const campaignModuleConfig = {
    eyebrow: 'Social Media Automation',
    tag: 'Social Campaigns',
    theme: 'social',
    heroTitle: 'Social campaign command center',
    heroCopy: 'Queue Facebook and Instagram posts, manage the basic campaign link, and keep lightweight engagement tracking close to the content timeline.',
    panelTitle: 'What this page covers',
    panelCopy: 'This page keeps campaign links, publishing, platform coverage, and engagement placeholders in one focused view so the calendar can stay clean.',
    highlights: [
        {
            label: 'Scheduled posts',
            detail: 'Upcoming content stays visible with the posting time and platform attached.',
        },
        {
            label: 'Platform coverage',
            detail: 'Facebook and Instagram entries render side by side for quick review.',
        },
        {
            label: 'Engagement tracking',
            detail: 'Likes, comments, shares, reach, and clicks remain available in the row data.',
        },
    ],
    panelBullets: [
        {
            label: 'Calendar sync',
            detail: 'Use the calendar page to inspect timing while this page handles the list view.',
        },
        {
            label: 'Account links',
            detail: 'Each post stays tied to its social account and campaign when present.',
        },
        {
            label: 'Status flow',
            detail: 'Draft, scheduled, published, and failed states stay filterable.',
        },
    ],
    actions: [
        { label: 'Schedule Post', href: '/social/posts/create', variant: 'primary' },
        { label: 'Open Calendar', href: '/social/calendar', variant: 'secondary' },
        { label: 'Review Accounts', href: '/social/accounts', variant: 'secondary' },
    ],
};

const scheduledModuleConfig = {
    ...campaignModuleConfig,
    tag: 'Scheduled Posts',
    heroTitle: 'Scheduled post queue',
    heroCopy: 'Review the posts queued for future publishing across Facebook and Instagram.',
    panelTitle: 'What this queue covers',
    panelCopy: 'This view focuses on upcoming posts so the publishing window is easy to scan and manage.',
    highlights: [
        {
            label: 'Queue focus',
            detail: 'Only future-dated posts are emphasized in this route.',
        },
        {
            label: 'Platform coverage',
            detail: 'Facebook and Instagram entries remain side by side for quick review.',
        },
        {
            label: 'Publishing status',
            detail: 'Scheduled, published, draft, and failed states remain available in the row data.',
        },
    ],
    panelBullets: [
        {
            label: 'Time first',
            detail: 'The scheduled timestamp is the main signal when reviewing the queue.',
        },
        {
            label: 'Account links',
            detail: 'Each queued post stays tied to its social account and campaign when present.',
        },
        {
            label: 'Calendar sync',
            detail: 'Use the calendar page to inspect the same items on a monthly timeline.',
        },
    ],
};

const moduleConfigByVariant = {
    campaigns: campaignModuleConfig,
    scheduled: scheduledModuleConfig,
};

export default function Index(props) {
    const moduleConfig = moduleConfigByVariant[props.variant] || campaignModuleConfig;

    return <ModuleWorkspacePage {...props} module={moduleConfig} />;
}
