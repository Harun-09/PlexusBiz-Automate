import EmptyState from '@/Components/EmptyState';
import StatusBadge from '@/Components/StatusBadge';
import { Link } from '@inertiajs/react';

const renderValue = (column, value) => {
    if (value === null || value === undefined || value === '') {
        return <span className="text-slate-400">-</span>;
    }

    if (String(column).toLowerCase().includes('status')) {
        return <StatusBadge status={value} />;
    }

    if (String(column).toLowerCase() === 'action' && typeof value === 'string' && value.startsWith('/')) {
        return (
            <Link href={value} className="font-bold text-blue-800 hover:text-blue-950">
                Open
            </Link>
        );
    }

    if (Array.isArray(value)) {
        return value.join(', ');
    }

    return String(value);
};

export default function DataTable({ columns = [], rows = [], emptyTitle, emptyDescription, loading = false, error = null }) {
    if (error) {
        return (
            <div className="rounded-lg border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700">
                {error}
            </div>
        );
    }

    if (loading) {
        return (
            <div className="rounded-lg border border-slate-200 bg-white p-5">
                <div className="h-4 w-40 animate-pulse rounded bg-slate-200" />
                <div className="mt-4 space-y-3">
                    {[0, 1, 2].map((item) => <div key={item} className="h-10 animate-pulse rounded bg-slate-100" />)}
                </div>
            </div>
        );
    }

    if (rows.length === 0) {
        return <EmptyState title={emptyTitle} description={emptyDescription} />;
    }

    return (
        <div className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-slate-200 text-sm">
                    <thead className="bg-slate-50">
                        <tr>
                            {columns.map((column) => (
                                <th key={column} className="whitespace-nowrap px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-slate-500">
                                    {column}
                                </th>
                            ))}
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-100">
                        {rows.map((row, index) => (
                            <tr key={index} className="hover:bg-slate-50">
                                {columns.map((column) => (
                                    <td key={column} className="whitespace-nowrap px-5 py-4 text-slate-700">
                                        {renderValue(column, row[column])}
                                    </td>
                                ))}
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
