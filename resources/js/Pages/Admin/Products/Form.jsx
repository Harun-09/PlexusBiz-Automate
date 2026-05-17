import { useForm } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export default function ProductForm({
    product = null,
    suppliers = [],
    statuses,
    submitUrl,
    method = 'post',
    defaultSupplierId = '',
    hideSupplier = false,
    cancelUrl = '/admin/products',
    supplierLabel = '',
}) {
    const isEditing = !!product;
    const pricingUrl = !hideSupplier && (product?.id ? `/admin/bulk-pricing?product=${product.id}` : '/admin/bulk-pricing');
    const [imagePreview, setImagePreview] = useState(product?.primary_image_url || '');

    const { data, setData, post, processing, errors } = useForm({
        supplier_id: product?.supplier_id || defaultSupplierId || '',
        sku: product?.sku || '',
        name: product?.name || '',
        description: product?.description || '',
        tags: Array.isArray(product?.tags) ? product.tags.join(', ') : (product?.tags || ''),
        base_price: product?.base_price || '',
        moq: product?.moq || 1,
        bulk_price: product?.bulk_price || '',
        stock_quantity: product?.stock_quantity || 0,
        status: product?.status || 'draft',
        image: null,
        ...(method === 'put' ? { _method: 'put' } : {}),
    });

    useEffect(() => {
        if (data.image instanceof File) {
            const previewUrl = URL.createObjectURL(data.image);
            setImagePreview(previewUrl);

            return () => URL.revokeObjectURL(previewUrl);
        }

        setImagePreview(product?.primary_image_url || '');
    }, [data.image, product?.primary_image_url]);

    const submit = (e) => {
        e.preventDefault();
        post(submitUrl, { preserveScroll: true, forceFormData: true });
    };

    return (
        <form onSubmit={submit} className="space-y-6">
            <div className="grid gap-6 md:grid-cols-2">
                {hideSupplier ? (
                    <div className="md:col-span-2 rounded-2xl border border-blue-200 bg-blue-50/80 p-4 text-sm text-blue-900">
                        <p className="font-semibold">Supplier</p>
                        <p className="mt-1 text-blue-800/90">
                            {supplierLabel || 'This product will be saved under your supplier account.'}
                        </p>
                    </div>
                ) : (
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
                            <option value="">Select a supplier...</option>
                            {suppliers.map((s) => (
                                <option key={s.id} value={s.id}>{s.label}</option>
                            ))}
                        </select>
                        {errors.supplier_id && <p className="mt-1.5 text-sm text-rose-600">{errors.supplier_id}</p>}
                    </div>
                )}

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

                <div className="md:col-span-2">
                    <div className="grid gap-4 lg:grid-cols-[280px_minmax(0,1fr)]">
                        <div>
                            <label htmlFor="product-image" className="block text-xs font-bold uppercase tracking-wider text-gray-500">
                                Product Image
                            </label>
                            <input
                                id="product-image"
                                type="file"
                                accept="image/*"
                                onChange={(e) => setData('image', e.target.files?.[0] ?? null)}
                                className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition file:mr-4 file:rounded-lg file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-xs file:font-semibold file:uppercase file:tracking-wider file:text-white hover:file:bg-blue-700 focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                            />
                            <p className="mt-1.5 text-xs leading-5 text-gray-500">
                                JPG, PNG, WEBP, or AVIF. Upload one primary image for marketplace and admin previews.
                            </p>
                            {errors.image && <p className="mt-1.5 text-sm text-rose-600">{errors.image}</p>}
                            {data.image?.name ? (
                                <p className="mt-2 text-xs font-medium text-blue-700">
                                    Selected file: {data.image.name}
                                </p>
                            ) : null}
                        </div>

                        <div className="rounded-2xl border border-dashed border-blue-200 bg-blue-50/60 p-4">
                            {imagePreview ? (
                                <img
                                    src={imagePreview}
                                    alt={product?.name || 'Product image preview'}
                                    className="h-48 w-full rounded-xl object-cover shadow-sm"
                                />
                            ) : (
                                <div className="flex h-48 items-center justify-center rounded-xl border border-dashed border-blue-100 bg-white text-sm text-gray-400">
                                    No product image selected yet.
                                </div>
                            )}
                            <p className="mt-3 text-xs leading-5 text-gray-500">
                                {product?.primary_image_url
                                    ? 'This is the current primary image. Choose a new file to replace it.'
                                    : 'This image will become the primary product image after save.'}
                            </p>
                        </div>
                    </div>
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
                        placeholder="Product description, specifications, and key features..."
                    />
                    {errors.description && <p className="mt-1.5 text-sm text-rose-600">{errors.description}</p>}
                </div>

                <div className="md:col-span-2">
                    <label htmlFor="product-tags" className="block text-xs font-bold uppercase tracking-wider text-gray-500">Tags</label>
                    <input
                        id="product-tags"
                        type="text"
                        value={data.tags}
                        onChange={(e) => setData('tags', e.target.value)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        placeholder="industrial, wholesale, heavy-duty"
                    />
                    <p className="mt-1.5 text-xs text-gray-500">Comma-separated tags for product discovery and filtering.</p>
                    {errors.tags && <p className="mt-1.5 text-sm text-rose-600">{errors.tags}</p>}
                </div>

                {/* Pricing */}
                <div>
                    <label htmlFor="product-price" className="block text-xs font-bold uppercase tracking-wider text-gray-500">
                        Regular Price <span className="text-rose-500">*</span>
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

                <div>
                    <label htmlFor="product-moq" className="block text-xs font-bold uppercase tracking-wider text-gray-500">
                        MOQ <span className="text-rose-500">*</span>
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

                <div>
                    <label htmlFor="product-bulk-price" className="block text-xs font-bold uppercase tracking-wider text-gray-500">
                        Bulk Price
                    </label>
                    <input
                        id="product-bulk-price"
                        type="number"
                        step="0.01"
                        min="0"
                        value={data.bulk_price}
                        onChange={(e) => setData('bulk_price', e.target.value)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        placeholder="Optional price at MOQ"
                    />
                    {errors.bulk_price && <p className="mt-1.5 text-sm text-rose-600">{errors.bulk_price}</p>}
                </div>

                {!hideSupplier && product?.id ? (
                    <div className="rounded-2xl border border-blue-200 bg-blue-50/80 p-4 text-sm text-blue-900">
                        <p className="font-semibold">Advanced tier pricing</p>
                        <p className="mt-1 text-blue-800/90">Use the dedicated page for multiple bulk tiers.</p>
                        <a href={pricingUrl} className="mt-3 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-white transition hover:bg-blue-700">
                            Open tiers
                        </a>
                    </div>
                ) : null}

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
                <a href={cancelUrl} className="inline-flex w-full items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-600 shadow-sm transition hover:bg-gray-50 sm:w-auto">
                    Cancel
                </a>
            </div>
        </form>
    );
}
