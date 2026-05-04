import { Link } from '@inertiajs/react';

export default function NotificationBell({ count = 0 }) {
    return (
        <Link
            href="/notifications"
            aria-label="Notifications"
            className="relative grid h-10 w-10 place-items-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:border-blue-200 hover:text-blue-800"
        >
            <svg aria-hidden="true" viewBox="0 0 24 24" className="h-5 w-5 fill-none stroke-current stroke-2">
                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" />
                <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
            {count > 0 ? (
                <span className="absolute -right-1 -top-1 min-w-[1.1rem] rounded-full bg-rose-600 px-1.5 text-center text-[10px] font-black leading-5 text-white">
                    {count > 9 ? '9+' : count}
                </span>
            ) : null}
        </Link>
    );
}
