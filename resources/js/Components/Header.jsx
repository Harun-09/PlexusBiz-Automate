import Dropdown from '@/Components/Dropdown';
import NotificationBell from '@/Components/NotificationBell';

const pathToBreadcrumbs = (path) => {
    const parts = path.split('/').filter(Boolean);

    if (parts.length === 0) {
        return ['Dashboard'];
    }

    return parts.map((part) => part.replace(/[-_]/g, ' ').replace(/\b\w/g, (letter) => letter.toUpperCase()));
};

export default function Header({ user, header, currentPath, onOpenSidebar }) {
    const breadcrumbs = pathToBreadcrumbs(currentPath);
    const showBreadcrumbs = breadcrumbs.length > 1;
    const roleLabel = (user?.roles || [])
        .map((role) => String(role).replace(/_/g, ' '))
        .filter(Boolean)
        .join(', ') || 'workspace';

    return (
        <header className="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
            <div className="flex min-h-16 items-center gap-3 px-4 sm:px-6">
                <button
                    type="button"
                    onClick={onOpenSidebar}
                    className="grid h-10 w-10 place-items-center rounded-lg border border-slate-200 text-slate-600 lg:hidden"
                    aria-label="Open navigation"
                >
                    <svg aria-hidden="true" viewBox="0 0 24 24" className="h-5 w-5 fill-none stroke-current stroke-2">
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div className="min-w-0 flex-1">
                    {showBreadcrumbs ? (
                        <div className="flex flex-wrap items-center gap-1 text-xs font-semibold text-slate-500">
                            {breadcrumbs.map((crumb, index) => (
                                <span key={`${crumb}-${index}`} className="inline-flex items-center gap-1">
                                    {index > 0 ? <span className="text-slate-300">/</span> : null}
                                    {crumb}
                                </span>
                            ))}
                        </div>
                    ) : null}
                    <div className={showBreadcrumbs ? 'mt-1 min-w-0' : 'min-w-0'}>{header}</div>
                </div>

                <div className="flex items-center gap-5">
                    <NotificationBell />

                    <Dropdown>
                        <Dropdown.Trigger>
                            <button
                                type="button"
                                className="flex h-10 items-center gap-2 rounded-lg border border-slate-200 bg-white px-2.5 text-left text-sm transition hover:border-blue-200"
                            >
                                <span className="grid h-7 w-7 place-items-center rounded-md bg-slate-950 text-xs font-black text-white">
                                    {user?.name?.slice(0, 1) || 'U'}
                                </span>
                                <span className="hidden leading-tight md:block">
                                    <span className="block max-w-32 truncate font-bold text-slate-900">{user?.name}</span>
                                    <span className="block max-w-32 truncate text-xs text-slate-500">{roleLabel}</span>
                                </span>
                            </button>
                        </Dropdown.Trigger>

                        <Dropdown.Content align="right" width="48">
                            <Dropdown.Link href={route('profile.edit')}>Profile</Dropdown.Link>
                            <Dropdown.Link href={route('logout')} method="post" as="button">
                                Log Out
                            </Dropdown.Link>
                        </Dropdown.Content>
                    </Dropdown>
                </div>
            </div>
        </header>
    );
}
