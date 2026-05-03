import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, useForm } from '@inertiajs/react';

const statusTone = (value) => {
    const normalized = String(value || '').toLowerCase();

    if (['active', 'approved', 'confirmed', 'completed', 'sent', 'success', 'published'].includes(normalized)) {
        return 'border-emerald-200 bg-emerald-50 text-emerald-700';
    }

    if (['failed', 'cancelled', 'rejected', 'suspended'].includes(normalized)) {
        return 'border-rose-200 bg-rose-50 text-rose-700';
    }

    if (['pending', 'scheduled', 'running', 'draft', 'waiting_supplier'].includes(normalized)) {
        return 'border-amber-200 bg-amber-50 text-amber-700';
    }

    return 'border-gray-200 bg-gray-50 text-gray-700';
};

export default function WorkspaceIndex({ auth, workspace }) {
    const filters = workspace.filters || null;
    const { data, setData } = useForm({
        search: filters?.search || '',
        status: filters?.status || '',
    });

    const submitFilters = (event) => {
        event.preventDefault();

        router.get(
            window.location.pathname,
            Object.fromEntries(Object.entries(data).filter(([, value]) => value !== '')),
            {
                preserveScroll: true,
                preserveState: true,
                replace: true,
            },
        );
    };

    const resetFilters = () => {
        setData({ search: '', status: '' });

        router.get(window.location.pathname, {}, {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div>
                    <h2 className="text-xl font-semibold leading-tight text-gray-950">{workspace.title}</h2>
                    <p className="mt-1 text-sm text-gray-600">{workspace.description}</p>
                </div>
            }
        >
            <Head title={workspace.title} />

            <div className="py-8">
                <div className="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                    {workspace.metrics.length > 0 && (
                        <section className="grid gap-4 md:grid-cols-4">
                            {workspace.metrics.map((metric) => (
                                <div key={metric.label} className="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                                    <p className="text-sm font-medium text-gray-500">{metric.label}</p>
                                    <p className="mt-2 text-2xl font-semibold text-gray-950">{metric.value}</p>
                                </div>
                            ))}
                        </section>
                    )}

                    {filters && (
                        <form onSubmit={submitFilters} className="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                            <div className="grid gap-4 md:grid-cols-[minmax(0,1fr)_220px_auto] md:items-end">
                                <div>
                                    <label htmlFor="workspace-search" className="block text-sm font-medium text-gray-700">
                                        Search
                                    </label>
                                    <input
                                        id="workspace-search"
                                        type="search"
                                        value={data.search}
                                        onChange={(event) => setData('search', event.target.value)}
                                        className="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>

                                {filters.statuses.length > 0 && (
                                    <div>
                                        <label htmlFor="workspace-status" className="block text-sm font-medium text-gray-700">
                                            Status
                                        </label>
                                        <select
                                            id="workspace-status"
                                            value={data.status}
                                            onChange={(event) => setData('status', event.target.value)}
                                            className="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                            <option value="">All</option>
                                            {filters.statuses.map((status) => (
                                                <option key={status} value={status}>
                                                    {status}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                )}

                                <div className="flex gap-2">
                                    <button
                                        type="submit"
                                        className="inline-flex h-10 items-center justify-center rounded-md bg-gray-950 px-4 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                                    >
                                        Apply
                                    </button>
                                    <button
                                        type="button"
                                        onClick={resetFilters}
                                        className="inline-flex h-10 items-center justify-center rounded-md border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                                    >
                                        Reset
                                    </button>
                                </div>
                            </div>
                        </form>
                    )}

                    <section className="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                        <div className="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-5 py-4">
                            <div>
                                <h3 className="text-base font-semibold text-gray-950">Records</h3>
                                <p className="mt-1 text-sm text-gray-500">{workspace.rows.length} visible rows</p>
                            </div>
                        </div>

                        {workspace.rows.length === 0 ? (
                            <div className="px-5 py-10 text-sm text-gray-500">{workspace.emptyState}</div>
                        ) : (
                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            {workspace.columns.map((column) => (
                                                <th key={column} className="whitespace-nowrap px-5 py-3 text-left font-semibold text-gray-600">
                                                    {column}
                                                </th>
                                            ))}
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-100 bg-white">
                                        {workspace.rows.map((row, index) => (
                                            <tr key={index} className="hover:bg-gray-50">
                                                {workspace.columns.map((column) => {
                                                    const value = row[column] ?? '';
                                                    const isStatus = column.toLowerCase().includes('status') || column.toLowerCase() === 'stage';
                                                    const wraps = ['content', 'error'].includes(column.toLowerCase());

                                                    return (
                                                        <td key={column} className={`${wraps ? 'max-w-xl whitespace-normal' : 'whitespace-nowrap'} px-5 py-4 text-gray-700`}>
                                                            {isStatus ? (
                                                                <span className={`inline-flex rounded-full border px-2.5 py-1 text-xs font-medium ${statusTone(value)}`}>
                                                                    {value}
                                                                </span>
                                                            ) : (
                                                                value
                                                            )}
                                                        </td>
                                                    );
                                                })}
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        )}
                    </section>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
