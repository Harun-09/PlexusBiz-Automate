import { useForm } from '@inertiajs/react';

export default function ProductForm({ product = null, suppliers = [], statuses, submitUrl, method = 'post' }) {
    const isEditing = !!product;

    const { data, setData, post, put, processing, errors } = useForm({
        supplier_id: product?.supplier_id || '',
        sku: product?.sku || '',
        name: product?.name || '',
        description: product?.description || '',
        base_price: product?.base_price || '',
        moq: product?.moq || 1,
        stock_quantity: product?.stock_quantity || 0,
        status: product?.status || 'draft',
    });

    const submit = (e) => {
        e.preventDefault();
        if (method === 'put') {
            put(submitUrl, { preserveScroll: true });
        } else {
            post(submitUrl, { preserveScroll: true });
        }
    };

    return (
        <form onSubmit={submit} className="space-y-6">
            <div className="grid gap-6 md:grid-cols-2">
                {/* Supplier */}
                <div className="md:col-span-2">
                    <label htmlFor="product-supplier" className="block text-xs font-bold uppercase tracking-wider text-gray-500">
                        Supplier <span className="text-rose-500">*</span>
                    </label>
                    <select
                        id="product-supplier"
                        value={data.supplier_id}
                        onChange={(e) => setData('supplier_id', e.target.value)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                        <option value="">Select a supplier…</option>
                        {suppliers.map((s) => (
                            <option key={s.id} value={s.id}>{s.label}</option>
                        ))}
                    </select>
                    {errors.supplier_id && <p className="mt-1.5 text-sm text-rose-600">{errors.supplier_id}</p>}
                </div>

                {/* SKU */}
                <div>
                    <label htmlFor="product-sku" className="block text-xs font-bold uppercase tracking-wider text-gray-500">
                        SKU <span className="text-rose-500">*</span>
                    </label>
                    <input
                        id="product-sku"
                        type="text"
                        value={data.sku}
                        onChange={(e) => setData('sku', e.target.value)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 font-mono text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        placeholder="PRD-001"
                        required
                    />
                    {errors.sku && <p className="mt-1.5 text-sm text-rose-600">{errors.sku}</p>}
                </div>

                {/* Name */}
                <div>
                    <label htmlFor="product-name" className="block text-xs font-bold uppercase tracking-wider text-gray-500">
                        Product Name <span className="text-rose-500">*</span>
                    </label>
                    <input
                        id="product-name"
                        type="text"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        placeholder="Industrial Widget Pro"
                        required
                    />
                    {errors.name && <p className="mt-1.5 text-sm text-rose-600">{errors.name}</p>}
                </div>

                {/* Description */}
                <div className="md:col-span-2">
                    <label htmlFor="product-description" className="block text-xs font-bold uppercase tracking-wider text-gray-500">Description</label>
                    <textarea
                        id="product-description"
                        value={data.description}
                        onChange={(e) => setData('description', e.target.value)}
                        rows={4}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        placeholder="Product description, specifications, and key features…"
                    />
                    {errors.description && <p className="mt-1.5 text-sm text-rose-600">{errors.description}</p>}
                </div>

                {/* Base Price */}
                <div>
                    <label htmlFor="product-price" className="block text-xs font-bold uppercase tracking-wider text-gray-500">
                        Base Price ($) <span className="text-rose-500">*</span>
                    </label>
                    <input
                        id="product-price"
                        type="number"
                        step="0.01"
                        min="0"
                        value={data.base_price}
                        onChange={(e) => setData('base_price', e.target.value)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        placeholder="0.00"
                        required
                    />
                    {errors.base_price && <p className="mt-1.5 text-sm text-rose-600">{errors.base_price}</p>}
                </div>

                {/* MOQ */}
                <div>
                    <label htmlFor="product-moq" className="block text-xs font-bold uppercase tracking-wider text-gray-500">
                        Minimum Order Qty <span className="text-rose-500">*</span>
                    </label>
                    <input
                        id="product-moq"
                        type="number"
                        min="1"
                        value={data.moq}
                        onChange={(e) => setData('moq', e.target.value)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        required
                    />
                    {errors.moq && <p className="mt-1.5 text-sm text-rose-600">{errors.moq}</p>}
                </div>

                {/* Stock Quantity */}
                <div>
                    <label htmlFor="product-stock" className="block text-xs font-bold uppercase tracking-wider text-gray-500">
                        Stock Quantity <span className="text-rose-500">*</span>
                    </label>
                    <input
                        id="product-stock"
                        type="number"
                        min="0"
                        value={data.stock_quantity}
                        onChange={(e) => setData('stock_quantity', e.target.value)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        required
                    />
                    {errors.stock_quantity && <p className="mt-1.5 text-sm text-rose-600">{errors.stock_quantity}</p>}
                </div>

                {/* Status */}
                <div>
                    <label htmlFor="product-status" className="block text-xs font-bold uppercase tracking-wider text-gray-500">
                        Status <span className="text-rose-500">*</span>
                    </label>
                    <select
                        id="product-status"
                        value={data.status}
                        onChange={(e) => setData('status', e.target.value)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                        {statuses.map((s) => (
                            <option key={s} value={s}>{s}</option>
                        ))}
                    </select>
                    {errors.status && <p className="mt-1.5 text-sm text-rose-600">{errors.status}</p>}
                </div>
            </div>

            <div className="flex flex-col gap-3 border-t border-gray-100 pt-6 sm:flex-row sm:items-center">
                <button
                    type="submit"
                    disabled={processing}
                    className="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-slate-900 to-blue-700 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-700/20 transition hover:shadow-blue-700/30 hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto"
                >
                    {processing ? (
                        <svg className="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" /><path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
                    ) : null}
                    {isEditing ? 'Update Product' : 'Create Product'}
                </button>
                <a href="/admin/products" className="inline-flex w-full items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-600 shadow-sm transition hover:bg-gray-50 sm:w-auto">
                    Cancel
                </a>
            </div>
        </form>
    );
}
