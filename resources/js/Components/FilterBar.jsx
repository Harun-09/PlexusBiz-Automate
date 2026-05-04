export default function FilterBar({ search, onSearchChange, status, onStatusChange, statuses = [], onSubmit, onReset }) {
    return (
        <form onSubmit={onSubmit} className="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <div className="grid gap-3 md:grid-cols-[minmax(0,1fr)_220px_auto] md:items-end">
                <label className="block">
                    <span className="text-sm font-bold text-slate-700">Search</span>
                    <input
                        type="search"
                        value={search}
                        onChange={(event) => onSearchChange?.(event.target.value)}
                        className="mt-1 h-10 w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                </label>
                <label className="block">
                    <span className="text-sm font-bold text-slate-700">Status</span>
                    <select
                        value={status}
                        onChange={(event) => onStatusChange?.(event.target.value)}
                        className="mt-1 h-10 w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">All</option>
                        {statuses.map((item) => <option key={item} value={item}>{item}</option>)}
                    </select>
                </label>
                <div className="flex gap-2">
                    <button type="submit" className="h-10 rounded-lg bg-slate-950 px-4 text-sm font-bold text-white">Apply</button>
                    <button type="button" onClick={onReset} className="h-10 rounded-lg border border-slate-200 px-4 text-sm font-bold text-slate-700">Reset</button>
                </div>
            </div>
        </form>
    );
}
