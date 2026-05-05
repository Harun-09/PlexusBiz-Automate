import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { actionButtonClasses, statusBadgeClasses } from '@/Utils/pillStyles';
import { Head, Link, router, useForm } from '@inertiajs/react';
import { useState } from 'react';

const StatusPill = ({ status }) => (
    <span className={`inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold capitalize ${statusBadgeClasses(status)}`}>
        {status}
    </span>
);

export default function ProductsIndex({ auth, products, filters, statuses, suppliers, flash }) {
    const [showDeleteModal, setShowDeleteModal] = useState(null);
    const { data, setData } = useForm({
        search: filters.search || '',
        status: filters.status || '',
        supplier: filters.supplier || '',
    });

    const submitFilters = (e) => {
        e.preventDefault();
        router.get('/admin/products', Object.fromEntries(Object.entries(data).filter(([, v]) => v !== '')), {
            preserveState: true, preserveScroll: true, replace: true,
        });
    };

    const resetFilters = () => {
        setData({ search: '', status: '', supplier: '' });
        router.get('/admin/products', {}, { preserveState: true, preserveScroll: true, replace: true });
    };

    const deleteProduct = () => {
        if (!showDeleteModal) return;
        router.delete(`/admin/products/${showDeleteModal}`, {
            preserveScroll: true,
            onFinish: () => setShowDeleteModal(null),
        });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 className="text-xl font-bold text-gray-950">Product Management</h2>
                        <p className="mt-1 text-sm text-gray-500">Manage catalog products, inventory, and supplier assignments.</p>
                    </div>
                    <Link
                        href="/admin/products/create"
                        className="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-slate-900 to-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-700/20 transition hover:shadow-blue-700/30 hover:-translate-y-0.5 sm:w-auto"
                    >
                        <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}><path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Add Product
                    </Link>
                </div>
            }
        >
            <Head title="Products" />

            {flash?.success && (
                <div className="mt-4">
                    <div className="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-medium text-emerald-800">{flash.success}</div>
                </div>
            )}
            {flash?.error && (
                <div className="mt-4">
                    <div className="rounded-xl border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-medium text-rose-800">{flash.error}</div>
                </div>
            )}

            <div className="py-8">
                <div className="space-y-6">
                    {/* Filters */}
                    <form onSubmit={submitFilters} className="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm">
                        <div className="grid gap-4 md:grid-cols-[1fr_180px_200px_auto] md:items-end">
                            <div>
                                <label htmlFor="product-search" className="block text-xs font-bold uppercase tracking-wider text-gray-500">Search</label>
                                <input id="product-search" type="search" value={data.search} onChange={(e) => setData('search', e.target.value)}
                                    placeholder="Search by name or SKU…"
                                    className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500" />
                            </div>
                            <div>
                                <label htmlFor="product-status" className="block text-xs font-bold uppercase tracking-wider text-gray-500">Status</label>
                                <select id="product-status" value={data.status} onChange={(e) => setData('status', e.target.value)}
                                    className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">All</option>
                                    {statuses.map((s) => <option key={s} value={s}>{s}</option>)}
                                </select>
                            </div>
                            <div>
                                <label htmlFor="product-supplier" className="block text-xs font-bold uppercase tracking-wider text-gray-500">Supplier</label>
                                <select id="product-supplier" value={data.supplier} onChange={(e) => setData('supplier', e.target.value)}
                                    className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">All</option>
                                    {suppliers.map((s) => <option key={s.id} value={s.id}>{s.label}</option>)}
                                </select>
                            </div>
                            <div className="flex flex-col gap-2 sm:flex-row">
                                <button type="submit" className="inline-flex h-10 w-full items-center justify-center rounded-xl bg-gray-950 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 sm:w-auto">Apply</button>
                                <button type="button" onClick={resetFilters} className="inline-flex h-10 w-full items-center justify-center rounded-xl border border-gray-200 bg-white px-5 text-sm font-semibold text-gray-600 shadow-sm transition hover:bg-gray-50 sm:w-auto">Reset</button>
                            </div>
                        </div>
                    </form>

                    {/* Table */}
                    <section className="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm">
                        <div className="flex flex-col gap-3 border-b border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 className="text-base font-bold text-gray-950">Products</h3>
                                <p className="mt-0.5 text-sm text-gray-500">{products.total} total products</p>
                            </div>
                        </div>

                        {products.data.length === 0 ? (
                            <div className="px-6 py-16 text-center text-sm text-gray-400">No products found.</div>
                        ) : (
                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-100 text-sm">
                                    <thead className="bg-gray-50/80">
                                        <tr>
                                            {['SKU', 'Name', 'Supplier', 'Price', 'Stock', 'MOQ', 'Status', 'Actions'].map((h) => (
                                                <th key={h} className="whitespace-nowrap px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">{h}</th>
                                            ))}
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-50 bg-white">
                                        {products.data.map((product) => (
                                            <tr key={product.id} className="transition hover:bg-blue-50/30">
                                                <td className="whitespace-nowrap px-6 py-4 font-mono text-xs font-semibold text-gray-600">{product.sku}</td>
                                                <td className="whitespace-nowrap px-6 py-4 font-medium text-gray-900">{product.name}</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-gray-600">{product.supplier || '-'}</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-gray-700 font-medium">${product.base_price}</td>
                                                <td className="whitespace-nowrap px-6 py-4">
                                                    <span className={`font-semibold ${product.stock <= 0 ? 'text-rose-600' : product.stock < 10 ? 'text-amber-600' : 'text-gray-700'}`}>
                                                        {product.stock}
                                                    </span>
                                                </td>
                                                <td className="whitespace-nowrap px-6 py-4 text-gray-500">{product.moq}</td>
                                                <td className="whitespace-nowrap px-6 py-4"><StatusPill status={product.status} /></td>
                                                <td className="whitespace-nowrap px-6 py-4">
                                                    <div className="flex items-center gap-2">
                                                        <Link href={`/admin/bulk-pricing?product=${product.id}`}
                                                            className={`inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 text-xs font-semibold shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('secondary')}`}>
                                                            <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}><path strokeLinecap="round" strokeLinejoin="round" d="M12 8v8m-4-4h8m-7.5 7h7a4.5 4.5 0 0 0 4.5-4.5V7.5A4.5 4.5 0 0 0 15.5 3h-7A4.5 4.5 0 0 0 4 7.5v7A4.5 4.5 0 0 0 8.5 19Z" /></svg>
                                                            Pricing
                                                        </Link>
                                                        <Link href={`/admin/products/${product.id}/edit`}
                                                            className={`inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 text-xs font-semibold shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('secondary')}`}>
                                                            <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}><path strokeLinecap="round" strokeLinejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg>
                                                            Edit
                                                        </Link>
                                                        <button onClick={() => setShowDeleteModal(product.id)}
                                                            className={`inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 text-xs font-semibold shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('danger')}`}>
                                                            <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}><path strokeLinecap="round" strokeLinejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                                            Delete
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        )}

                        {products.last_page > 1 && (
                            <div className="flex flex-col gap-3 border-t border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                                <p className="text-sm text-gray-500">Page {products.current_page} of {products.last_page}</p>
                                <div className="flex flex-wrap gap-2">
                                    {products.links.filter((l) => l.url).map((link, i) => (
                                        <Link key={i} href={link.url} preserveScroll preserveState
                                            className={`inline-flex h-9 min-w-[36px] items-center justify-center rounded-lg border px-3 text-sm font-medium transition ${link.active ? 'border-blue-600 bg-blue-600 text-white shadow-sm' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50'}`}
                                            dangerouslySetInnerHTML={{ __html: link.label }} />
                                    ))}
                                </div>
                            </div>
                        )}
                    </section>
                </div>
            </div>

            {/* Delete Confirmation Modal */}
            {showDeleteModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/50 backdrop-blur-sm" onClick={() => setShowDeleteModal(null)}>
                    <div className="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-2xl" onClick={(e) => e.stopPropagation()}>
                        <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-rose-100">
                            <svg className="h-6 w-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}><path strokeLinecap="round" strokeLinejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126Z" /><path strokeLinecap="round" strokeLinejoin="round" d="M12 15.75h.007v.008H12v-.008Z" /></svg>
                        </div>
                        <h3 className="mt-4 text-center text-lg font-bold text-gray-950">Delete Product</h3>
                        <p className="mt-2 text-center text-sm text-gray-500">This product will be soft-deleted and removed from the active catalog. Continue?</p>
                        <div className="mt-6 flex gap-3">
                            <button onClick={() => setShowDeleteModal(null)} className="flex-1 rounded-xl border border-gray-200 bg-white py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Cancel</button>
                            <button onClick={deleteProduct} className="flex-1 rounded-xl bg-rose-600 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700">Delete</button>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
