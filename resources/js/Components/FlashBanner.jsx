export default function FlashBanner({ message, type = 'success', className = '' }) {
    if (!message) {
        return null;
    }

    const toneClasses =
        type === 'error'
            ? 'border-rose-200 bg-rose-50 text-rose-800'
            : 'border-emerald-200 bg-emerald-50 text-emerald-800';

    return (
        <div className={`rounded-2xl border px-4 py-3 text-sm font-medium shadow-sm ${toneClasses} ${className}`}>
            {message}
        </div>
    );
}
