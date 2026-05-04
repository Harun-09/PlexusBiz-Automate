export default function EmptyState({ title = 'No records found', description = 'Try adjusting your filters or create a new record.' }) {
    return (
        <div className="rounded-lg border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
            <div className="mx-auto grid h-12 w-12 place-items-center rounded-lg bg-slate-100 text-slate-500">
                <svg aria-hidden="true" viewBox="0 0 24 24" className="h-6 w-6 fill-none stroke-current stroke-2">
                    <path d="M4 6h16M4 12h16M4 18h10" />
                </svg>
            </div>
            <h3 className="mt-4 text-base font-black text-slate-950">{title}</h3>
            <p className="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">{description}</p>
        </div>
    );
}
