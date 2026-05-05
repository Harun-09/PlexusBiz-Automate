import { Link } from '@inertiajs/react';

export default function SidebarItem({ item, active = false, dense = false, onNavigate = null }) {
    const classes = [
        'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition',
        dense ? 'py-1.5 text-xs' : '',
        active
            ? 'bg-white text-slate-950 shadow-sm'
            : 'text-slate-300 hover:bg-white/10 hover:text-white',
    ].filter(Boolean).join(' ');

    return (
        <Link href={item.href} className={classes} onClick={onNavigate}>
            <span
                className={[
                    'grid h-8 w-8 shrink-0 place-items-center rounded-md border text-xs font-black',
                    active ? 'border-blue-200 bg-blue-50 text-blue-800' : 'border-white/10 bg-white/10 text-slate-300',
                ].join(' ')}
                aria-hidden="true"
            >
                {item.icon || item.label.slice(0, 1)}
            </span>
            <span className="min-w-0 flex-1 truncate">{item.label}</span>
            {item.badge ? (
                <span className="rounded-md bg-white/10 px-2 py-0.5 text-[10px] font-bold uppercase text-slate-300">
                    {item.badge}
                </span>
            ) : null}
        </Link>
    );
}
