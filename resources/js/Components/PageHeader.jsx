export default function PageHeader({ eyebrow, title, description, actions = null, compact = false }) {
    return (
        <div className={compact ? 'flex items-center justify-between gap-4' : 'flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between'}>
            <div className={compact ? 'min-w-0' : 'min-w-0 max-w-3xl'}>
                <h1 className={compact ? 'text-2xl font-extrabold text-slate-950 sm:text-3xl' : 'text-2xl font-extrabold text-slate-950 sm:text-3xl'}>
                    {title}
                </h1>
            </div>
            {actions ? <div className="flex shrink-0 flex-wrap items-center gap-2">{actions}</div> : null}
        </div>
    );
}
