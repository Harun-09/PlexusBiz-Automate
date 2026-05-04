const toneForStatus = (status) => {
    const value = String(status || '').toLowerCase();

    if (['active', 'approved', 'completed', 'success', 'published', 'paid'].includes(value)) {
        return 'border-emerald-200 bg-emerald-50 text-emerald-700';
    }

    if (['failed', 'cancelled', 'rejected', 'suspended', 'inactive'].includes(value)) {
        return 'border-rose-200 bg-rose-50 text-rose-700';
    }

    if (['pending', 'processing', 'scheduled', 'running', 'draft', 'waiting_supplier'].includes(value)) {
        return 'border-amber-200 bg-amber-50 text-amber-700';
    }

    return 'border-slate-200 bg-slate-50 text-slate-600';
};

export default function StatusBadge({ status, children }) {
    const label = children || String(status || 'n/a').replace(/[_-]/g, ' ');

    return (
        <span className={`inline-flex rounded-full border px-2.5 py-1 text-xs font-bold capitalize ${toneForStatus(status)}`}>
            {label}
        </span>
    );
}
