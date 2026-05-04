export default function JsonEditor({ value, onChange, rows = 10, error = null }) {
    return (
        <div>
            <textarea
                value={value}
                onChange={(event) => onChange?.(event.target.value)}
                rows={rows}
                spellCheck="false"
                className="w-full rounded-lg border-slate-200 bg-slate-950 font-mono text-sm text-slate-50 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            />
            {error ? <p className="mt-2 text-sm font-semibold text-rose-600">{error}</p> : null}
        </div>
    );
}
