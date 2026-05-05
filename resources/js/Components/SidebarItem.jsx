import { Link } from '@inertiajs/react';

export function SidebarIcon({ icon, active = false, size = 'item' }) {
    const Icon = typeof icon === 'function' ? icon : null;

    const badgeClasses = [
        'grid shrink-0 place-items-center rounded-md border',
        size === 'module' ? 'h-10 w-10 text-sm' : 'h-8 w-8 text-xs',
        active
            ? 'border-blue-200 bg-blue-50 text-blue-800'
            : 'border-white/10 bg-white/10 text-slate-300',
    ].join(' ');

    return (
        <span className={badgeClasses} aria-hidden="true">
            {Icon ? (
                <Icon className={size === 'module' ? 'h-5 w-5' : 'h-4 w-4'} />
            ) : (
                <span className={size === 'module' ? 'text-[11px] font-black tracking-[0.16em]' : 'text-[10px] font-black tracking-[0.14em]'}>
                    {icon}
                </span>
            )}
        </span>
    );
}

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
            <SidebarIcon icon={item.icon || item.label.slice(0, 1)} active={active} />
            <span className="min-w-0 flex-1 truncate">{item.label}</span>
            {item.badge ? (
                <span className="rounded-md bg-white/10 px-2 py-0.5 text-[10px] font-bold uppercase text-slate-300">
                    {item.badge}
                </span>
            ) : null}
        </Link>
    );
}
