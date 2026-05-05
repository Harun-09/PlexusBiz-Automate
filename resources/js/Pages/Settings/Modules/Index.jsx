import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import PageHeader from '@/Components/PageHeader';
import { Head, useForm } from '@inertiajs/react';

const summaryTone = {
    total: 'border-slate-200 bg-white text-slate-900',
    enabled: 'border-emerald-200 bg-emerald-50 text-emerald-900',
    disabled: 'border-rose-200 bg-rose-50 text-rose-900',
    locked: 'border-amber-200 bg-amber-50 text-amber-900',
    overrides: 'border-blue-200 bg-blue-50 text-blue-900',
};

function SummaryCard({ label, value, tone }) {
    return (
        <section className={`rounded-lg border p-5 shadow-sm ${summaryTone[tone] ?? summaryTone.total}`}>
            <p className="text-xs font-bold uppercase opacity-70">{label}</p>
            <p className="mt-2 text-3xl font-extrabold">{value}</p>
        </section>
    );
}

function ModuleRow({ module, checked, onToggle }) {
    return (
        <section className="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div className="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div className="max-w-3xl">
                    <div className="flex flex-wrap items-center gap-2">
                        <span className="rounded-md bg-slate-100 px-3 py-1 text-[11px] font-bold uppercase text-slate-600">
                            {module.key}
                        </span>
                        <span
                            className={`rounded-md px-3 py-1 text-[11px] font-bold uppercase ${
                                module.locked ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700'
                            }`}
                        >
                            {module.locked ? 'Locked' : 'Toggleable'}
                        </span>
                        <span
                            className={`rounded-md px-3 py-1 text-[11px] font-bold uppercase ${
                                checked ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'
                            }`}
                        >
                            {checked ? 'Enabled' : 'Disabled'}
                        </span>
                    </div>

                    <h3 className="mt-3 text-lg font-extrabold text-slate-950">{module.name}</h3>
                    <p className="mt-2 text-sm leading-6 text-slate-600">{module.description}</p>

                    <div className="mt-4 flex flex-wrap gap-2">
                        <span className="rounded-md bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                            {module.source}
                        </span>
                        {module.override && (
                            <span className="rounded-md bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                Database override
                            </span>
                        )}
                    </div>
                </div>

                <div className="flex items-center gap-4 lg:min-w-[220px] lg:justify-end">
                    <div className="text-right">
                        <p className="text-xs font-bold uppercase text-slate-400">State</p>
                        <p className="mt-1 text-sm font-semibold text-slate-900">{checked ? 'Enabled' : 'Disabled'}</p>
                    </div>

                    <button
                        type="button"
                        onClick={() => {
                            if (! module.locked) {
                                onToggle(module.key);
                            }
                        }}
                        disabled={module.locked}
                        className={`relative inline-flex h-9 w-16 items-center rounded-full transition ${
                            checked ? 'bg-emerald-500' : 'bg-slate-300'
                        } ${module.locked ? 'cursor-not-allowed opacity-60' : 'hover:opacity-90'}`}
                        aria-pressed={checked}
                        aria-label={`Toggle ${module.name}`}
                    >
                        <span
                            className={`inline-block h-7 w-7 rounded-full bg-white shadow transition ${
                                checked ? 'translate-x-8' : 'translate-x-1'
                            }`}
                        />
                    </button>
                </div>
            </div>
        </section>
    );
}

export default function ModuleSettingsIndex({ auth, modules, summary, flash }) {
    const { data, setData, patch, processing } = useForm({
        modules: Object.fromEntries(modules.map((module) => [module.key, module.enabled])),
    });

    const toggleModule = (key) => {
        setData('modules', {
            ...data.modules,
            [key]: ! data.modules[key],
        });
    };

    const submit = (event) => {
        event.preventDefault();

        patch('/settings/modules', {
            preserveScroll: true,
        });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <PageHeader
                    eyebrow="Platform control"
                    title="Module Settings"
                    description="Toggle modules from the database. Locked modules stay enabled because this control panel depends on them."
                />
            }
        >
            <Head title="Module Settings" />

            {flash?.success && (
                <div className="mt-4">
                    <div className="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-medium text-emerald-800">
                        {flash.success}
                    </div>
                </div>
            )}

            <div className="space-y-6">
                    <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                        <SummaryCard label="Total Modules" value={summary.total} tone="total" />
                        <SummaryCard label="Enabled" value={summary.enabled} tone="enabled" />
                        <SummaryCard label="Disabled" value={summary.disabled} tone="disabled" />
                        <SummaryCard label="Locked" value={summary.locked} tone="locked" />
                        <SummaryCard label="Overrides" value={summary.overrides} tone="overrides" />
                    </div>

                    <form onSubmit={submit} className="space-y-4">
                        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 className="text-base font-extrabold text-slate-950">Domain Modules</h3>
                                <p className="mt-1 text-sm text-slate-600">Save changes to update the live module registry.</p>
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex w-full items-center justify-center rounded-lg bg-slate-950 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
                            >
                                {processing ? 'Saving...' : 'Save Changes'}
                            </button>
                        </div>

                        <div className="space-y-4">
                            {modules.map((module) => (
                                <ModuleRow
                                    key={module.key}
                                    module={module}
                                    checked={!! data.modules[module.key]}
                                    onToggle={toggleModule}
                                />
                            ))}
                        </div>
                    </form>
            </div>
        </AuthenticatedLayout>
    );
}
