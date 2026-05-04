import InputError from '@/Components/InputError';

export default function FormField({ label, error, children, hint = null }) {
    return (
        <label className="block">
            <span className="text-sm font-bold text-slate-700">{label}</span>
            <div className="mt-1">{children}</div>
            {hint ? <p className="mt-1 text-xs text-slate-500">{hint}</p> : null}
            <InputError message={error} className="mt-2" />
        </label>
    );
}
