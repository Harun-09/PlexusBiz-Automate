function SvgIcon({ className = 'h-4 w-4', viewBox = '0 0 24 24', children }) {
    return (
        <svg
            className={className}
            viewBox={viewBox}
            fill="none"
            stroke="currentColor"
            strokeWidth="1.8"
            strokeLinecap="round"
            strokeLinejoin="round"
            aria-hidden="true"
        >
            {children}
        </svg>
    );
}

export function DashboardIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <rect x="3.5" y="3.5" width="7" height="7" rx="1.5" />
            <rect x="13.5" y="3.5" width="7" height="7" rx="1.5" />
            <rect x="3.5" y="13.5" width="7" height="7" rx="1.5" />
            <rect x="13.5" y="13.5" width="7" height="7" rx="1.5" />
        </SvgIcon>
    );
}

export function StorefrontIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <path d="M3.5 8.5 5 4.5h14l1.5 4" />
            <path d="M4 9h16v10.5H4z" />
            <path d="M9 19.5v-6h6v6" />
        </SvgIcon>
    );
}

export function UsersIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <circle cx="9" cy="8.5" r="2.75" />
            <circle cx="16.25" cy="10" r="2.25" />
            <path d="M4.75 19.25c.8-2.6 3.1-4.5 5.75-4.5s4.95 1.9 5.75 4.5" />
            <path d="M13.5 19.25c.45-1.6 1.7-2.9 3.25-3.45" />
        </SvgIcon>
    );
}

export function MegaphoneIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <path d="M3.5 11.5 18.5 6.75V16.5L3.5 11.75Z" />
            <path d="M6.5 12.5V17a2.5 2.5 0 0 0 5 0" />
            <path d="M18.5 8.5h2v4h-2" />
        </SvgIcon>
    );
}

export function SparklesIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <path d="M12 3.5 13.75 8l4.5 1.75L13.75 11.5 12 16l-1.75-4.5L5.75 9.75 10.25 8Z" />
            <path d="m18 13 1 2.5 2.5 1-2.5 1-1 2.5-1-2.5-2.5-1 2.5-1Z" />
        </SvgIcon>
    );
}

export function WorkflowIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <path d="M7 7h9.5" />
            <path d="m13.5 3.5 3.5 3.5-3.5 3.5" />
            <path d="M17 17H7.5" />
            <path d="m10.5 20.5-3.5-3.5 3.5-3.5" />
        </SvgIcon>
    );
}

export function ShieldCheckIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <path d="M12 3.5 19 6v5.5c0 4.75-3 7.9-7 9-4-1.1-7-4.25-7-9V6l7-2.5Z" />
            <path d="m9.25 12.25 1.75 1.75 3.75-4" />
        </SvgIcon>
    );
}

export function LifeBuoyIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <circle cx="12" cy="12" r="8" />
            <circle cx="12" cy="12" r="3.25" />
            <path d="m7.1 7.1 2.35 2.35" />
            <path d="m16.9 7.1-2.35 2.35" />
            <path d="m7.1 16.9 2.35-2.35" />
            <path d="m16.9 16.9-2.35-2.35" />
        </SvgIcon>
    );
}

export function ShoppingCartIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <circle cx="9" cy="19.25" r="1.25" />
            <circle cx="17" cy="19.25" r="1.25" />
            <path d="M3.5 4.75h2.5l1.9 9.25a1.5 1.5 0 0 0 1.45 1.2h7.5a1.5 1.5 0 0 0 1.45-1.1l1.45-6.1H7.4" />
        </SvgIcon>
    );
}

export function TagIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <path d="M10.25 3.5H6.75a1.25 1.25 0 0 0-1.25 1.25v3.5l8 8 5-5-8.25-7.75Z" />
            <circle cx="7.75" cy="7.75" r="0.9" />
        </SvgIcon>
    );
}

export function TruckIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <path d="M3.5 8h10.5v7.5H3.5z" />
            <path d="M14 10h3.5l2.5 2.5v3H14" />
            <circle cx="7.5" cy="18.25" r="1.25" />
            <circle cx="17.25" cy="18.25" r="1.25" />
        </SvgIcon>
    );
}

export function CubeIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <path d="m12 3.5 7 3.75v9.5l-7 3.75-7-3.75v-9.5L12 3.5Z" />
            <path d="M5 7.25 12 11l7-3.75" />
            <path d="M12 11v9.5" />
        </SvgIcon>
    );
}

export function PlusSquareIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <rect x="4" y="4" width="16" height="16" rx="3" />
            <path d="M12 8.25v7.5" />
            <path d="M8.25 12h7.5" />
        </SvgIcon>
    );
}

export function ClipboardListIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <rect x="6.5" y="3.5" width="11" height="17" rx="2" />
            <path d="M9 3.5h5v3H9z" />
            <path d="M9 10h6" />
            <path d="M9 13h6" />
            <path d="M9 16h4" />
        </SvgIcon>
    );
}

export function DocumentTextIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <path d="M7 3.5h7l4 4V20.5H7z" />
            <path d="M14 3.5V7.5H18" />
            <path d="M9 11h6" />
            <path d="M9 14h6" />
            <path d="M9 17h4" />
        </SvgIcon>
    );
}

export function ClockIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <circle cx="12" cy="12" r="8" />
            <path d="M12 8.5v4.25l2.75 1.75" />
        </SvgIcon>
    );
}

export function CalendarDaysIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <rect x="4" y="5" width="16" height="15" rx="2" />
            <path d="M8 3.5v3" />
            <path d="M16 3.5v3" />
            <path d="M4 9.5h16" />
        </SvgIcon>
    );
}

export function IdentificationIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <rect x="4" y="4" width="16" height="16" rx="3" />
            <circle cx="10" cy="10" r="2.25" />
            <path d="M7.5 16.5c.95-1.8 2.35-2.75 3.5-2.75s2.55.95 3.5 2.75" />
            <path d="M15 9h2" />
            <path d="M15 12h2" />
            <path d="M15 15h2" />
        </SvgIcon>
    );
}

export function FunnelIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <path d="M4 5.5h16L14 13v5l-4 1.75V13L4 5.5Z" />
        </SvgIcon>
    );
}

export function ChatBubbleIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <path d="M4.5 6.5h15v8.5h-8l-4.5 3.5v-3.5h-2.5z" />
            <path d="M8 10h8" />
            <path d="M8 12.75h5" />
        </SvgIcon>
    );
}

export function TargetIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <circle cx="12" cy="12" r="8" />
            <circle cx="12" cy="12" r="3" />
            <path d="M12 4v2" />
            <path d="M20 12h-2" />
            <path d="M12 20v-2" />
            <path d="M4 12h2" />
        </SvgIcon>
    );
}

export function ChartBarIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <path d="M4 20.5h16" />
            <rect x="6" y="12" width="2.5" height="6.5" rx="1" />
            <rect x="10.75" y="8.5" width="2.5" height="10" rx="1" />
            <rect x="15.5" y="5.5" width="2.5" height="13" rx="1" />
        </SvgIcon>
    );
}

export function SlidersHorizontalIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <path d="M4 7h8" />
            <path d="M16 7h4" />
            <circle cx="14" cy="7" r="2" />
            <path d="M4 12h4" />
            <path d="M12 12h8" />
            <circle cx="10" cy="12" r="2" />
            <path d="M4 17h10" />
            <path d="M18 17h2" />
            <circle cx="16" cy="17" r="2" />
        </SvgIcon>
    );
}

export function AlertTriangleIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <path d="m12 4.5 8 14h-16l8-14Z" />
            <path d="M12 9v4.5" />
            <path d="M12 16.25h.01" />
        </SvgIcon>
    );
}

export function QuestionMarkCircleIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <circle cx="12" cy="12" r="8" />
            <path d="M9.5 9.75a2.75 2.75 0 1 1 3.5 2.65c-.95.35-1.5 1.05-1.5 2.1" />
            <path d="M12 17.25h.01" />
        </SvgIcon>
    );
}

export function CalculatorIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <rect x="5" y="3.5" width="14" height="17" rx="2" />
            <path d="M8 7h8" />
            <path d="M8 11h2" />
            <path d="M12 11h2" />
            <path d="M16 11h0" />
            <path d="M8 15h2" />
            <path d="M12 15h2" />
            <path d="M16 15h0" />
            <path d="M8 18h2" />
            <path d="M12 18h2" />
        </SvgIcon>
    );
}

export function FileSearchIcon({ className = 'h-4 w-4' }) {
    return (
        <SvgIcon className={className}>
            <path d="M7 3.5h7l4 4V20.5H7z" />
            <path d="M14 3.5V7.5H18" />
            <circle cx="11" cy="13" r="2.5" />
            <path d="m13 15 1.5 1.5" />
        </SvgIcon>
    );
}
