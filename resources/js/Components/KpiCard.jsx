const toneClasses = {
    blue: 'border-blue-100 bg-blue-50 text-blue-800',
    emerald: 'border-emerald-100 bg-emerald-50 text-emerald-800',
    amber: 'border-amber-100 bg-amber-50 text-amber-800',
    rose: 'border-rose-100 bg-rose-50 text-rose-800',
    slate: 'border-slate-200 bg-white text-slate-900',
};

export default function KpiCard({ label, value, description, tone = 'slate' }) {
    const toneClass = toneClasses[tone] || toneClasses.slate;

    return (
        <section className={`rounded-lg border p-5 shadow-sm ${toneClass}`}>
            <div className="flex items-start justify-between gap-3">
                <p className="text-xs font-black uppercase tracking-wider opacity-70">{label}</p>
                <span className="rounded-full bg-white/70 px-2 py-0.5 text-[10px] font-black uppercase tracking-wide">Live</span>
            </div>
            <p className="mt-3 text-3xl font-black tracking-tight">{value}</p>
            {description ? <p className="mt-3 text-sm leading-6 opacity-80">{description}</p> : null}
        </section>
    );
}
