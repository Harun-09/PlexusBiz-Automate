export default function PageHeader({ eyebrow, title, description, actions = null }) {
    return (
        <div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div className="min-w-0">
                {eyebrow ? <p className="text-[11px] font-black uppercase tracking-wider text-slate-500">{eyebrow}</p> : null}
                <h1 className="mt-1 text-2xl font-black tracking-tight text-slate-950">{title}</h1>
                {description ? <p className="mt-2 max-w-3xl text-sm leading-6 text-slate-600">{description}</p> : null}
            </div>
            {actions ? <div className="flex shrink-0 flex-wrap gap-2">{actions}</div> : null}
        </div>
    );
}
