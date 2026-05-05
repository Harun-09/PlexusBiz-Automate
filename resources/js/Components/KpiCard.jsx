const toneClasses = {
    blue: { border: 'border-blue-100', bar: 'bg-blue-600', text: 'text-blue-700' },
    emerald: { border: 'border-emerald-100', bar: 'bg-emerald-600', text: 'text-emerald-700' },
    amber: { border: 'border-amber-100', bar: 'bg-amber-500', text: 'text-amber-700' },
    rose: { border: 'border-rose-100', bar: 'bg-rose-600', text: 'text-rose-700' },
    slate: { border: 'border-slate-200', bar: 'bg-slate-900', text: 'text-slate-700' },
};

export default function KpiCard({ label, value, description, tone = 'slate' }) {
    const toneClass = toneClasses[tone] || toneClasses.slate;

    return (
        <section className={`group relative overflow-hidden rounded-2xl border bg-gradient-to-br from-white via-white to-slate-50 p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-lg ${toneClass.border}`}>
            <div className={`absolute inset-x-0 top-0 h-1.5 ${toneClass.bar}`} />
            <div className="absolute inset-x-0 top-0 h-24 bg-gradient-to-b from-slate-50/70 to-transparent opacity-0 transition group-hover:opacity-100" />
            <div className="flex items-start justify-between gap-3">
                <p className="text-xs font-bold uppercase text-slate-500">{label}</p>
                <span className={`rounded-md border border-slate-200 bg-slate-50 px-2 py-0.5 text-[10px] font-bold uppercase ${toneClass.text}`}>
                    Live
                </span>
            </div>
            <p className="mt-4 text-3xl font-extrabold text-slate-950">{value}</p>
            {description ? <p className="mt-3 text-sm leading-6 text-slate-600">{description}</p> : null}
        </section>
    );
}
