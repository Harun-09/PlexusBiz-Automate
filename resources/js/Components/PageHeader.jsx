export default function PageHeader({ eyebrow, title, description, actions = null, compact = false }) {
    return (
        <div className={compact ? 'flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between' : 'flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between'}>
            <div className={compact ? 'min-w-0' : 'min-w-0 max-w-3xl'}>
                {eyebrow ? (
                    <p className="mb-2 inline-flex rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-[10px] font-black uppercase tracking-[0.28em] text-blue-700">
                        {eyebrow}
                    </p>
                ) : null}
                <h1 className={compact ? 'text-2xl font-extrabold tracking-[-0.04em] text-slate-950 sm:text-3xl' : 'text-2xl font-extrabold tracking-[-0.04em] text-slate-950 sm:text-3xl'}>
                    {title}
                </h1>
                {description ? (
                    <p className={compact ? 'mt-2 max-w-3xl text-sm leading-6 text-slate-600' : 'mt-2 max-w-3xl text-sm leading-6 text-slate-600'}>
                        {description}
                    </p>
                ) : null}
            </div>
            {actions ? <div className="flex shrink-0 flex-wrap items-center gap-2">{actions}</div> : null}
        </div>
    );
}
