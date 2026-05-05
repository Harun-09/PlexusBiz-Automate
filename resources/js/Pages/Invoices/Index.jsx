import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import EmptyState from '@/Components/EmptyState';
import PageHeader from '@/Components/PageHeader';
import { actionButtonClasses } from '@/Utils/pillStyles';
import { Head, Link, router, useForm } from '@inertiajs/react';
import { downloadPdf } from '@/Utils/pdf';

const statusTone = (status) => {
    const value = String(status || '').toLowerCase();

    if (value === 'paid') {
        return 'border-emerald-200 bg-emerald-50 text-emerald-700';
    }

    if (value === 'issued') {
        return 'border-blue-200 bg-blue-50 text-blue-700';
    }

    if (value === 'draft') {
        return 'border-amber-200 bg-amber-50 text-amber-700';
    }

    if (value === 'void') {
        return 'border-rose-200 bg-rose-50 text-rose-700';
    }

    return 'border-slate-200 bg-slate-50 text-slate-600';
};

const StatusPill = ({ status }) => (
    <span className={`inline-flex rounded-full border px-2.5 py-1 text-xs font-bold capitalize ${statusTone(status)}`}>
        {String(status || '-').replace(/_/g, ' ')}
    </span>
);

const formatMoney = (value, currency = 'BDT') => {
    const amount = Number(value || 0);

    return `${currency} ${amount.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })}`;
};

const formatDate = (value) => {
    if (!value) {
        return '-';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return '-';
    }

    return new Intl.DateTimeFormat('en-GB', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
    }).format(date);
};

const statusOptions = ['draft', 'issued', 'paid', 'void'];
const sortOptions = [
    { label: 'Newest first', value: '-issued_at' },
    { label: 'Oldest first', value: 'issued_at' },
    { label: 'Highest total', value: '-total' },
    { label: 'Lowest total', value: 'total' },
];

export default function Index({ auth, invoices, filters, flash }) {
    const { data, setData } = useForm({
        search: filters?.search || '',
        status: filters?.status || '',
        sort: filters?.sort || '-issued_at',
    });

    const submitFilters = (event) => {
        event.preventDefault();

        const params = Object.fromEntries(Object.entries(data).filter(([, value]) => value !== ''));

        router.get(route('invoices.index'), params, {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        });
    };

    const resetFilters = () => {
        setData({
            search: '',
            status: '',
            sort: '-issued_at',
        });

        router.get(route('invoices.index'), {}, {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        });
    };

    const handleDownload = async (invoice) => {
        await downloadPdf(route('invoices.download', invoice.id), `invoice-${invoice.invoice_number}.pdf`);
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <PageHeader
                    title="Invoices"
                    description="Search invoice numbers, review billing status, and open the full record when needed."
                />
            }
        >
            <Head title="Invoices" />

            {flash?.success ? (
                <div className="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-700">
                    {flash.success}
                </div>
            ) : null}
            {flash?.error ? (
                <div className="rounded-lg border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700">
                    {flash.error}
                </div>
            ) : null}

            <section className="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <form onSubmit={submitFilters} className="grid gap-4 lg:grid-cols-[minmax(0,1.2fr)_220px_220px_auto] lg:items-end">
                    <div>
                        <label htmlFor="invoice-search" className="block text-xs font-bold uppercase tracking-wider text-slate-500">
                            Search
                        </label>
                        <input
                            id="invoice-search"
                            type="search"
                            value={data.search}
                            onChange={(event) => setData('search', event.target.value)}
                            placeholder="Invoice number or order number"
                            className="mt-1.5 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        />
                    </div>

                    <div>
                        <label htmlFor="invoice-status" className="block text-xs font-bold uppercase tracking-wider text-slate-500">
                            Status
                        </label>
                        <select
                            id="invoice-status"
                            value={data.status}
                            onChange={(event) => setData('status', event.target.value)}
                            className="mt-1.5 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All</option>
                            {statusOptions.map((status) => (
                                <option key={status} value={status}>
                                    {status.replace(/_/g, ' ')}
                                </option>
                            ))}
                        </select>
                    </div>

                    <div>
                        <label htmlFor="invoice-sort" className="block text-xs font-bold uppercase tracking-wider text-slate-500">
                            Sort
                        </label>
                        <select
                            id="invoice-sort"
                            value={data.sort}
                            onChange={(event) => setData('sort', event.target.value)}
                            className="mt-1.5 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                        >
                            {sortOptions.map((option) => (
                                <option key={option.value} value={option.value}>
                                    {option.label}
                                </option>
                            ))}
                        </select>
                    </div>

                    <div className="flex flex-col gap-2 sm:flex-row">
                        <button
                            type="submit"
                            className="inline-flex h-10 w-full items-center justify-center rounded-xl bg-slate-950 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 sm:w-auto"
                        >
                            Apply
                        </button>
                        <button
                            type="button"
                            onClick={resetFilters}
                            className="inline-flex h-10 w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 sm:w-auto"
                        >
                            Reset
                        </button>
                    </div>
                </form>
            </section>

            <section className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div className="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 className="text-base font-black text-slate-950">Invoice Records</h3>
                        <p className="mt-1 text-sm text-slate-500">
                            {invoices.total} total invoices, {invoices.data.length} shown on this page.
                        </p>
                    </div>
                </div>

                {invoices.data.length === 0 ? (
                    <div className="px-5 py-10">
                        <EmptyState
                            title="No invoices found"
                            description="Try another search term or change the filters to reveal matching billing records."
                        />
                    </div>
                ) : (
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-slate-100 text-sm">
                            <thead className="bg-slate-50/80">
                                <tr>
                                    {['Invoice', 'Order', 'Buyer', 'Status', 'Issued', 'Due', 'Total', 'Actions'].map((column) => (
                                        <th
                                            key={column}
                                            className="whitespace-nowrap px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                        >
                                            {column}
                                        </th>
                                    ))}
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-50 bg-white">
                                {invoices.data.map((invoice) => {
                                    const currency = invoice?.order?.currency || 'BDT';

                                    return (
                                        <tr key={invoice.id} className="transition hover:bg-blue-50/30">
                                            <td className="whitespace-nowrap px-5 py-4">
                                                <div className="font-mono text-xs font-semibold text-slate-500">{invoice.invoice_number}</div>
                                                <div className="mt-1 text-sm font-bold text-slate-950">#{invoice.id}</div>
                                            </td>
                                            <td className="whitespace-nowrap px-5 py-4">
                                                <div className="font-semibold text-slate-900">{invoice?.order?.order_number || '-'}</div>
                                                <div className="mt-1 text-xs text-slate-500">Order reference</div>
                                            </td>
                                            <td className="whitespace-nowrap px-5 py-4">
                                                <div className="font-semibold text-slate-900">{invoice?.order?.buyer?.name || '-'}</div>
                                                <div className="mt-1 text-xs text-slate-500">{invoice?.order?.buyer?.email || '-'}</div>
                                            </td>
                                            <td className="whitespace-nowrap px-5 py-4">
                                                <StatusPill status={invoice.status} />
                                            </td>
                                            <td className="whitespace-nowrap px-5 py-4 text-slate-700">{formatDate(invoice.issued_at)}</td>
                                            <td className="whitespace-nowrap px-5 py-4 text-slate-700">{formatDate(invoice.due_at)}</td>
                                            <td className="whitespace-nowrap px-5 py-4 font-bold text-slate-900">
                                                {formatMoney(invoice.total, currency)}
                                            </td>
                                            <td className="whitespace-nowrap px-5 py-4">
                                                <div className="flex flex-wrap gap-2">
                                                    <Link
                                                        href={route('invoices.show', invoice.id)}
                                                        className={`inline-flex items-center justify-center rounded-lg border px-3 py-1.5 text-xs font-bold transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('secondary')}`}
                                                    >
                                                        Preview
                                                    </Link>
                                                    <button
                                                        type="button"
                                                        onClick={() => handleDownload(invoice)}
                                                        className={`inline-flex items-center justify-center rounded-lg border px-3 py-1.5 text-xs font-bold transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('primary')}`}
                                                    >
                                                        Download
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>
                )}

                {invoices.last_page > 1 ? (
                    <div className="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <p className="text-sm text-slate-500">
                            Page {invoices.current_page} of {invoices.last_page}
                        </p>
                        <div className="flex flex-wrap gap-2">
                            {invoices.links
                                .filter((link) => link.url)
                                .map((link, index) => (
                                    <Link
                                        key={`${link.label}-${index}`}
                                        href={link.url}
                                        preserveScroll
                                        preserveState
                                        className={`inline-flex h-9 min-w-[36px] items-center justify-center rounded-lg border px-3 text-sm font-medium transition ${
                                            link.active
                                                ? 'border-blue-600 bg-blue-600 text-white shadow-sm'
                                                : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'
                                        }`}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ))}
                        </div>
                    </div>
                ) : null}
            </section>
        </AuthenticatedLayout>
    );
}
