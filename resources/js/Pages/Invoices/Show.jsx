import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import PageHeader from '@/Components/PageHeader';
import StatusBadge from '@/Components/StatusBadge';
import { Head, Link } from '@inertiajs/react';

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
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
};

const summaryCard = (label, value, tone = 'slate') => {
    const toneClasses = {
        blue: 'border-blue-200 bg-blue-50 text-blue-700',
        emerald: 'border-emerald-200 bg-emerald-50 text-emerald-700',
        amber: 'border-amber-200 bg-amber-50 text-amber-700',
        rose: 'border-rose-200 bg-rose-50 text-rose-700',
        slate: 'border-slate-200 bg-slate-50 text-slate-700',
    };

    return (
        <div className={`rounded-xl border p-4 ${toneClasses[tone] || toneClasses.slate}`}>
            <p className="text-[11px] font-black uppercase tracking-wider opacity-80">{label}</p>
            <p className="mt-2 text-lg font-black text-slate-950">{value}</p>
        </div>
    );
};

export default function Show({ auth, invoice }) {
    const currency = invoice?.order?.currency || 'BDT';
    const buyer = invoice?.order?.buyer || null;
    const order = invoice?.order || null;
    const items = order?.items || [];
    const orderSubtotal = Number(invoice?.subtotal || order?.subtotal || 0);
    const orderTax = Number(invoice?.tax_total || order?.tax_total || 0);
    const discount = Number(order?.discount_total || 0);
    const total = Number(invoice?.total || order?.grand_total || 0);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <PageHeader
                    title={`Invoice ${invoice.invoice_number}`}
                    description={`Order ${order?.order_number || '-'} · ${formatDate(invoice.issued_at)}`}
                    actions={
                        <div className="flex flex-wrap gap-2">
                            <Link
                                href={route('invoices.index')}
                                className="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition hover:border-blue-200 hover:text-blue-800"
                            >
                                Back
                            </Link>
                            <a
                                href={route('invoices.preview', invoice.id)}
                                target="_blank"
                                rel="noreferrer"
                                className="inline-flex items-center justify-center rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-bold text-blue-700 transition hover:bg-blue-100"
                            >
                                Preview
                            </a>
                            <a
                                href={route('invoices.download', invoice.id)}
                                className="inline-flex items-center justify-center rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800"
                            >
                                Download PDF
                            </a>
                        </div>
                    }
                />
            }
        >
            <Head title={`Invoice ${invoice.invoice_number}`} />

            <section className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                {summaryCard('Invoice Status', <StatusBadge status={invoice.status}>{String(invoice.status || '-').replace(/_/g, ' ')}</StatusBadge>, 'blue')}
                {summaryCard('Invoice Total', formatMoney(total, currency), 'emerald')}
                {summaryCard('Due Date', formatDate(invoice.due_at), 'amber')}
                {summaryCard('Order Payment', <StatusBadge status={order?.payment_status}>{String(order?.payment_status || '-').replace(/_/g, ' ')}</StatusBadge>, 'slate')}
            </section>

            <div className="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
                <section className="space-y-6">
                    <div className="grid gap-4 md:grid-cols-2">
                        <div className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p className="text-[11px] font-black uppercase tracking-wider text-slate-400">Bill To</p>
                            <div className="mt-3 space-y-1 text-sm text-slate-600">
                                <p className="text-lg font-black text-slate-950">{buyer?.name || '-'}</p>
                                <p>{buyer?.email || '-'}</p>
                                <p>Customer ID: {buyer?.id ? `CUST-${buyer.id}` : '-'}</p>
                            </div>
                        </div>

                        <div className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p className="text-[11px] font-black uppercase tracking-wider text-slate-400">Invoice Details</p>
                            <div className="mt-3 space-y-2 text-sm text-slate-600">
                                <p><span className="font-bold text-slate-900">Invoice Number:</span> {invoice.invoice_number}</p>
                                <p><span className="font-bold text-slate-900">Order Number:</span> {order?.order_number || '-'}</p>
                                <p><span className="font-bold text-slate-900">Issued:</span> {formatDate(invoice.issued_at)}</p>
                                <p><span className="font-bold text-slate-900">Due:</span> {formatDate(invoice.due_at)}</p>
                                <p><span className="font-bold text-slate-900">Currency:</span> {currency}</p>
                            </div>
                        </div>
                    </div>

                    <div className="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                        <div className="flex flex-col gap-2 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 className="text-base font-black text-slate-950">Order Items</h2>
                                <p className="mt-1 text-sm text-slate-500">{items.length} item lines on this invoice.</p>
                            </div>
                        </div>

                        {items.length > 0 ? (
                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-slate-100 text-sm">
                                    <thead className="bg-slate-50/80">
                                        <tr>
                                            {['Product', 'Supplier', 'Qty', 'Unit Price', 'Line Total'].map((column) => (
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
                                        {items.map((item) => {
                                            const supplierName = item?.supplier?.company_name || item?.supplier?.user?.name || '-';

                                            return (
                                                <tr key={item.id} className="hover:bg-blue-50/30">
                                                    <td className="px-5 py-4">
                                                        <div className="font-semibold text-slate-900">{item.product_name}</div>
                                                        <div className="mt-1 font-mono text-xs text-slate-500">SKU: {item.sku || '-'}</div>
                                                    </td>
                                                    <td className="whitespace-nowrap px-5 py-4 text-slate-700">{supplierName}</td>
                                                    <td className="whitespace-nowrap px-5 py-4 text-slate-700">{item.quantity}</td>
                                                    <td className="whitespace-nowrap px-5 py-4 font-semibold text-slate-900">
                                                        {formatMoney(item.unit_price, currency)}
                                                    </td>
                                                    <td className="whitespace-nowrap px-5 py-4 font-black text-slate-950">
                                                        {formatMoney(item.total, currency)}
                                                    </td>
                                                </tr>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            </div>
                        ) : (
                            <div className="px-5 py-10">
                                <div className="rounded-xl border border-dashed border-slate-200 bg-slate-50 px-6 py-10 text-center text-sm text-slate-500">
                                    No line items found for this invoice.
                                </div>
                            </div>
                        )}
                    </div>
                </section>

                <aside className="space-y-6">
                    <div className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p className="text-[11px] font-black uppercase tracking-wider text-slate-400">Financial Summary</p>
                        <div className="mt-4 space-y-3 text-sm">
                            <div className="flex items-center justify-between text-slate-600">
                                <span>Subtotal</span>
                                <span className="font-semibold text-slate-900">{formatMoney(orderSubtotal, currency)}</span>
                            </div>
                            <div className="flex items-center justify-between text-slate-600">
                                <span>Tax</span>
                                <span className="font-semibold text-slate-900">{formatMoney(orderTax, currency)}</span>
                            </div>
                            <div className="flex items-center justify-between text-slate-600">
                                <span>Discount</span>
                                <span className="font-semibold text-slate-900">
                                    {discount > 0 ? `- ${formatMoney(discount, currency)}` : formatMoney(0, currency)}
                                </span>
                            </div>
                            <div className="flex items-center justify-between border-t border-slate-100 pt-3 text-base">
                                <span className="font-black text-slate-950">Total</span>
                                <span className="font-black text-slate-950">{formatMoney(total, currency)}</span>
                            </div>
                        </div>
                    </div>

                    <div className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p className="text-[11px] font-black uppercase tracking-wider text-slate-400">Order Summary</p>
                        <div className="mt-4 space-y-2 text-sm text-slate-600">
                            <p><span className="font-bold text-slate-900">Order Status:</span> {order?.status?.value || order?.status || '-'}</p>
                            <p><span className="font-bold text-slate-900">Payment Status:</span> {order?.payment_status || '-'}</p>
                            <p><span className="font-bold text-slate-900">Placed At:</span> {formatDate(order?.placed_at)}</p>
                            <p><span className="font-bold text-slate-900">Items:</span> {items.length}</p>
                        </div>
                    </div>
                </aside>
            </div>
        </AuthenticatedLayout>
    );
}
