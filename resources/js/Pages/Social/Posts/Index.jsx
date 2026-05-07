import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const moduleConfig = {
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
        { label: 'Schedule Post', href: route('social.posts.create'), variant: 'primary' },
        { label: 'Open Calendar', href: route('social.calendar'), variant: 'secondary' },
        { label: 'Review Accounts', href: route('social.accounts.index'), variant: 'secondary' },
    ],
};

export default function Index(props) {
    return <ModuleWorkspacePage {...props} module={moduleConfig} />;
}
