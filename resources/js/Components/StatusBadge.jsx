import { statusBadgeClasses } from '@/Utils/pillStyles';

export default function StatusBadge({ status, children }) {
    const label = children || String(status || 'n/a').replace(/[_-]/g, ' ');

    return (
        <span className={`inline-flex rounded-full border px-2.5 py-1 text-xs font-bold capitalize ${statusBadgeClasses(status)}`}>
            {label}
        </span>
    );
}
