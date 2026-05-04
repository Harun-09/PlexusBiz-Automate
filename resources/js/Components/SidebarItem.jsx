import { Link } from '@inertiajs/react';

export default function SidebarItem({ item, active = false, dense = false, onNavigate = null }) {
    const classes = [
        'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition',
        dense ? 'py-1.5 text-xs' : '',
        active
            ? 'bg-blue-50 text-blue-800 ring-1 ring-blue-100'
            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950',
    ].filter(Boolean).join(' ');

    return (
        <Link href={item.href} className={classes} onClick={onNavigate}>
            <span
                className={[
                    'grid h-8 w-8 shrink-0 place-items-center rounded-lg border text-xs font-black',
                    active ? 'border-blue-200 bg-white text-blue-800' : 'border-slate-200 bg-white text-slate-500',
                ].join(' ')}
                aria-hidden="true"
            >
                {item.icon || item.label.slice(0, 1)}
            </span>
            <span className="min-w-0 flex-1 truncate">{item.label}</span>
            {item.badge ? (
                <span className="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                    {item.badge}
                </span>
            ) : null}
        </Link>
    );
}
