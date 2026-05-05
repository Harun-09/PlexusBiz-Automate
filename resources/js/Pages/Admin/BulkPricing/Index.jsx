import KpiCard from '@/Components/KpiCard';
import PageHeader from '@/Components/PageHeader';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { actionButtonClasses, statusBadgeClasses } from '@/Utils/pillStyles';
import { Head, Link, router, useForm } from '@inertiajs/react';
import { useState } from 'react';

const pricingColumns = [
    { label: 'SKU', className: 'w-[120px]' },
    { label: 'Product', className: 'w-[280px]' },
    { label: 'Supplier', className: 'w-[220px]' },
    { label: 'MOQ', className: 'w-[88px]' },
    { label: 'Tiers', className: 'w-[88px]' },
    { label: 'Base price', className: 'w-[140px]' },
    { label: 'Status', className: 'w-[110px]' },
    { label: 'Action', className: 'w-[110px]' },
];

const StatusPill = ({ status }) => (
    <span className={`inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold capitalize ${statusBadgeClasses(status)}`}>
        {status}
    </span>
);

const formatMoney = (value) => new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
}).format(Number(value || 0));

const moneyLabel = (value) => `BDT ${formatMoney(value)}`;

const toQuery = (params) => Object.fromEntries(
    Object.entries(params).filter(([, value]) => value !== '' && value !== null && value !== undefined),
);

function ManagementPanel({ product }) {
    const [editingTierId, setEditingTierId] = useState(null);
    const moqForm = useForm({
        moq: product?.moq || 1,
    });
    const tierForm = useForm({
        min_quantity: '',
        unit_price: '',
    });

    if (!product) {
        return (
            <section className="rounded-3xl border border-dashed border-slate-200 bg-white p-8 shadow-sm">
                <div className="rounded-2xl bg-slate-50 p-8 text-center">
                    <p className="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400">Bulk Pricing Workspace</p>
                    <h3 className="mt-3 text-2xl font-black text-slate-950">Select a product to manage pricing</h3>
                    <p className="mt-2 text-sm text-slate-500">
                        Choose a product from the list to update MOQ and build quantity-based pricing rules.
                    </p>
                </div>
            </section>
        );
    }

    const beginEditTier = (tier) => {
        setEditingTierId(tier.id);
        tierForm.setData({
            min_quantity: tier.min_quantity,
            unit_price: tier.unit_price,
        });
    };

    const cancelEditTier = () => {
        setEditingTierId(null);
        tierForm.reset();
        tierForm.clearErrors();
    };

    const submitMoq = (event) => {
        event.preventDefault();
        moqForm.put(`/admin/bulk-pricing/${product.id}`, {
            preserveScroll: true,
            onSuccess: () => moqForm.reset('moq'),
        });
    };

    const submitTier = (event) => {
        event.preventDefault();

        const url = editingTierId
            ? `/admin/bulk-pricing/${product.id}/tiers/${editingTierId}`
            : `/admin/bulk-pricing/${product.id}/tiers`;

        const submit = editingTierId ? tierForm.put : tierForm.post;

        submit(url, {
            preserveScroll: true,
            onSuccess: () => {
                cancelEditTier();
            },
        });
    };

    const deleteTier = (tierId, tierLabel) => {
        if (!window.confirm(`Delete the pricing tier for ${tierLabel}?`)) {
            return;
        }

        router.delete(`/admin/bulk-pricing/${product.id}/tiers/${tierId}`, {
            preserveScroll: true,
        });
    };

    return (
        <section className="space-y-5 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div className="rounded-2xl bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 px-5 py-4 text-white shadow-lg shadow-slate-950/10">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Selected product</p>
                        <h3 className="mt-1 text-2xl font-black">{product.name}</h3>
                        <p className="mt-1 text-sm text-slate-300">{product.supplier?.company_name || 'Unassigned supplier'}</p>
                    </div>
                    <div className="flex flex-wrap gap-2">
                        <Link
                            href="/admin/products"
                            className={`inline-flex items-center justify-center rounded-xl border px-4 py-2 text-xs font-semibold uppercase tracking-wider transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('secondary')}`}
                        >
                            Product CRUD
                        </Link>
                        <Link
                            href={`/admin/products/${product.id}/edit`}
                            className={`inline-flex items-center justify-center rounded-xl border px-4 py-2 text-xs font-semibold uppercase tracking-wider transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('primary')}`}
                        >
                            Edit Product
                        </Link>
                    </div>
                </div>

                <div className="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    {[
                        ['SKU', product.sku],
                        ['MOQ', product.moq],
                        ['Base price', moneyLabel(product.base_price)],
                        ['Available stock', product.available_stock],
                    ].map(([label, value]) => (
                        <div key={label} className="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                            <p className="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{label}</p>
                            <p className="mt-1 text-lg font-extrabold text-white">{value}</p>
                        </div>
                    ))}
                </div>
            </div>

                    <div className="grid gap-5 xl:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]">
                    <form onSubmit={submitMoq} className="min-w-0 rounded-2xl border border-slate-200 bg-slate-50/80 p-5">
                        <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">MOQ control</p>
                        <h4 className="mt-2 text-lg font-bold text-slate-950">Update minimum order quantity</h4>
                        <p className="mt-1 text-sm text-slate-500">
                            Keep the minimum order rule here so product CRUD stays focused on catalog content only.
                        </p>

                        <label htmlFor={`moq-${product.id}`} className="mt-4 block text-xs font-bold uppercase tracking-wider text-slate-500">
                            Minimum order quantity
                        </label>
                        <input
                            id={`moq-${product.id}`}
                            type="number"
                            min="1"
                            value={moqForm.data.moq}
                            onChange={(e) => moqForm.setData('moq', e.target.value)}
                            className="mt-1.5 block w-full rounded-xl border-slate-200 bg-white text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                        />
                        {moqForm.errors.moq && <p className="mt-1.5 text-sm text-rose-600">{moqForm.errors.moq}</p>}

                        <button
                            type="submit"
                            disabled={moqForm.processing}
                            className="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {moqForm.processing ? 'Saving...' : 'Save MOQ'}
                        </button>

                        <div className="mt-4 rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
                            Tiers cannot start below MOQ. When you increase MOQ, revisit existing tiers to keep checkout rules valid.
                        </div>
                    </form>

                <div className="min-w-0 space-y-4 rounded-2xl border border-slate-200 bg-white p-5">
                    <div>
                        <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">
                            {editingTierId ? 'Edit tier' : 'Add tier'}
                        </p>
                        <h4 className="mt-2 text-lg font-bold text-slate-950">
                            {editingTierId ? 'Update a quantity discount' : 'Create a new bulk pricing tier'}
                        </h4>
                        <p className="mt-1 text-sm text-slate-500">
                            Tier prices drive the storefront MOQ pricing view and the checkout price resolution logic.
                        </p>
                    </div>

                    <form onSubmit={submitTier} className="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label htmlFor={`tier-min-${product.id}`} className="block text-xs font-bold uppercase tracking-wider text-slate-500">
                                Minimum quantity
                            </label>
                            <input
                                id={`tier-min-${product.id}`}
                                type="number"
                                min={product.moq}
                                value={tierForm.data.min_quantity}
                                onChange={(e) => tierForm.setData('min_quantity', e.target.value)}
                                className="mt-1.5 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                                placeholder={`>= ${product.moq}`}
                            />
                            {tierForm.errors.min_quantity && <p className="mt-1.5 text-sm text-rose-600">{tierForm.errors.min_quantity}</p>}
                        </div>
                        <div>
                            <label htmlFor={`tier-price-${product.id}`} className="block text-xs font-bold uppercase tracking-wider text-slate-500">
                                Unit price
                            </label>
                            <input
                                id={`tier-price-${product.id}`}
                                type="number"
                                step="0.01"
                                min="0"
                                value={tierForm.data.unit_price}
                                onChange={(e) => tierForm.setData('unit_price', e.target.value)}
                                className="mt-1.5 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                                placeholder="0.00"
                            />
                            {tierForm.errors.unit_price && <p className="mt-1.5 text-sm text-rose-600">{tierForm.errors.unit_price}</p>}
                        </div>

                        <div className="sm:col-span-2 flex flex-col gap-3 border-t border-slate-100 pt-2 sm:flex-row">
                            <button
                                type="submit"
                                disabled={tierForm.processing}
                                className="inline-flex flex-1 items-center justify-center rounded-xl bg-gradient-to-r from-blue-700 to-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {tierForm.processing ? 'Saving...' : editingTierId ? 'Update tier' : 'Add tier'}
                            </button>
                            {editingTierId ? (
                                <button
                                    type="button"
                                    onClick={cancelEditTier}
                                    className="inline-flex flex-1 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                                >
                                    Cancel edit
                                </button>
                            ) : null}
                        </div>
                    </form>

                    <div className="rounded-2xl border border-slate-200">
                        <div className="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                            <div>
                                <h5 className="text-sm font-bold text-slate-950">Current tiers</h5>
                                <p className="text-xs text-slate-500">{product.pricing_tiers.length} configured rules</p>
                            </div>
                            <span className="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">
                                MOQ {product.moq}
                            </span>
                        </div>

                        {product.pricing_tiers.length === 0 ? (
                            <div className="px-4 py-8 text-sm text-slate-500">
                                No pricing tiers yet. Add the first bulk discount rule using the form above.
                            </div>
                        ) : (
                            <div className="divide-y divide-slate-100">
                                {product.pricing_tiers.map((tier) => (
                                    <div key={tier.id} className="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p className="text-sm font-bold text-slate-950">{tier.min_quantity}+ units</p>
                                            <p className="text-xs text-slate-500">Tier becomes active at this quantity threshold.</p>
                                        </div>
                                        <div className="flex items-center gap-3">
                                            <span className="rounded-full bg-blue-50 px-3 py-1.5 text-sm font-bold text-blue-700">
                                                {moneyLabel(tier.unit_price)}
                                            </span>
                                            <button
                                                type="button"
                                                onClick={() => beginEditTier(tier)}
                                                className={`rounded-lg border px-3 py-1.5 text-xs font-semibold transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('secondary')}`}
                                            >
                                                Edit
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => deleteTier(tier.id, `${tier.min_quantity}+ units`)}
                                                className={`rounded-lg border px-3 py-1.5 text-xs font-semibold transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('danger')}`}
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </section>
    );
}

export default function BulkPricingIndex({ auth, summary, products, selectedProduct, filters, statuses, suppliers, flash }) {
    const { data, setData } = useForm({
        search: filters.search || '',
        status: filters.status || '',
        supplier: filters.supplier || '',
    });

    const currentProductId = selectedProduct?.id || filters.product || '';
    const buildQuery = (productId) => toQuery({
        search: data.search,
        status: data.status,
        supplier: data.supplier,
        product: productId || currentProductId,
    });

    const submitFilters = (event) => {
        event.preventDefault();
        router.get('/admin/bulk-pricing', buildQuery(currentProductId), {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    const resetFilters = () => {
        setData({ search: '', status: '', supplier: '' });
        router.get('/admin/bulk-pricing', toQuery({ product: currentProductId }), {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <PageHeader
                    eyebrow="E-Commerce backend"
                    title="Bulk Pricing &amp; MOQ"
                    description="Manage minimum order quantity and tiered unit pricing from a dedicated backend workspace."
                    actions={(
                        <>
                            <Link
                                href="/admin/products"
                                className="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                            >
                                Product CRUD
                            </Link>
                            <Link
                                href="/admin/products/create"
                                className="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-slate-900 to-blue-700 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-700/20 transition hover:shadow-blue-700/30 hover:-translate-y-0.5"
                            >
                                Add Product
                            </Link>
                        </>
                    )}
                />
            }
        >
            <Head title="Bulk Pricing & MOQ" />

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
                    <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <KpiCard
                            label="Products"
                            value={summary.total_products}
                            description="Available catalog records across the backend."
                            tone="slate"
                        />
                        <KpiCard
                            label="Tiered products"
                            value={summary.products_with_tiers}
                            description="Products already configured with bulk discounts."
                            tone="blue"
                        />
                        <KpiCard
                            label="Needs setup"
                            value={summary.products_without_tiers}
                            description="Products still waiting for tier rules."
                            tone="amber"
                        />
                        <KpiCard
                            label="Average MOQ"
                            value={summary.average_moq}
                            description={`${summary.total_tiers} total tier rules across the catalog.`}
                            tone="emerald"
                        />
                    </div>

                    <div className="grid gap-6 xl:grid-cols-[minmax(0,1.35fr)_minmax(0,0.65fr)]">
                        <section className="min-w-0 space-y-5">
                            <form onSubmit={submitFilters} className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                                <div className="grid gap-4 md:grid-cols-[minmax(0,1fr)_180px_180px_auto] md:items-end">
                                    <div>
                                        <label htmlFor="pricing-search" className="block text-xs font-bold uppercase tracking-wider text-slate-500">Search</label>
                                        <input
                                            id="pricing-search"
                                            type="search"
                                            value={data.search}
                                            onChange={(e) => setData('search', e.target.value)}
                                            placeholder="Search products or suppliers..."
                                            className="mt-1.5 block w-full rounded-xl border-slate-200 bg-slate-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                                        />
                                    </div>
                                    <div>
                                        <label htmlFor="pricing-status" className="block text-xs font-bold uppercase tracking-wider text-slate-500">Status</label>
                                        <select
                                            id="pricing-status"
                                            value={data.status}
                                            onChange={(e) => setData('status', e.target.value)}
                                            className="mt-1.5 block w-full rounded-xl border-slate-200 bg-slate-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                                        >
                                            <option value="">All</option>
                                            {statuses.map((status) => (
                                                <option key={status} value={status}>{status}</option>
                                            ))}
                                        </select>
                                    </div>
                                    <div>
                                        <label htmlFor="pricing-supplier" className="block text-xs font-bold uppercase tracking-wider text-slate-500">Supplier</label>
                                        <select
                                            id="pricing-supplier"
                                            value={data.supplier}
                                            onChange={(e) => setData('supplier', e.target.value)}
                                            className="mt-1.5 block w-full rounded-xl border-slate-200 bg-slate-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                                        >
                                            <option value="">All</option>
                                            {suppliers.map((supplier) => (
                                                <option key={supplier.id} value={supplier.id}>{supplier.label}</option>
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
                                </div>
                            </form>

                            <section className="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                                <div className="flex flex-col gap-3 border-b border-slate-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <h3 className="text-base font-bold text-slate-950">Products with pricing controls</h3>
                                        <p className="mt-0.5 text-sm text-slate-500">{products.total} matching products</p>
                                    </div>
                                    {selectedProduct ? (
                                        <span className="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                            Managing {selectedProduct.sku}
                                        </span>
                                    ) : null}
                                </div>

                                {products.data.length === 0 ? (
                                    <div className="px-6 py-16 text-center text-sm text-slate-400">No products found.</div>
                                ) : (
                                    <div className="overflow-x-auto">
                                        <table className="min-w-full table-fixed divide-y divide-slate-100 text-sm">
                                            <thead className="bg-slate-50/80">
                                                <tr>
                                                    {pricingColumns.map((column) => (
                                                        <th
                                                            key={column.label}
                                                            className={`whitespace-nowrap px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 ${column.className}`}
                                                        >
                                                            {column.label}
                                                        </th>
                                                    ))}
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-slate-50 bg-white">
                                                {products.data.map((product) => {
                                                    const active = selectedProduct?.id === product.id;

                                                    return (
                                                        <tr key={product.id} className={`transition hover:bg-blue-50/30 ${active ? 'bg-blue-50/50 ring-1 ring-inset ring-blue-100' : ''}`}>
                                                            <td className="whitespace-nowrap px-6 py-4 font-mono text-xs font-semibold text-slate-600">{product.sku}</td>
                                                            <td className="px-6 py-4 font-medium text-slate-900">
                                                                <div className="max-w-[280px]">
                                                                    <p className="truncate text-sm font-semibold leading-5 text-slate-950" title={product.name}>
                                                                        {product.name}
                                                                    </p>
                                                                </div>
                                                            </td>
                                                            <td className="whitespace-nowrap px-6 py-4 text-slate-600">{product.supplier || '-'}</td>
                                                            <td className="whitespace-nowrap px-6 py-4 text-slate-700">{product.moq}</td>
                                                            <td className="whitespace-nowrap px-6 py-4 text-slate-700">{product.pricing_tiers_count}</td>
                                                            <td className="whitespace-nowrap px-6 py-4 text-slate-700">{moneyLabel(product.base_price)}</td>
                                                            <td className="whitespace-nowrap px-6 py-4">
                                                                <StatusPill status={product.status} />
                                                            </td>
                                                            <td className="whitespace-nowrap px-6 py-4">
                                                                <Link
                                                                    href={`/admin/bulk-pricing?${new URLSearchParams(toQuery({
                                                                        ...data,
                                                                        product: product.id,
                                                                    })).toString()}`}
                                                                    className="inline-flex items-center justify-center gap-1 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 shadow-sm transition hover:border-blue-300 hover:bg-blue-100"
                                                                >
                                                                    Manage
                                                                </Link>
                                                            </td>
                                                        </tr>
                                                    );
                                                })}
                                            </tbody>
                                        </table>
                                    </div>
                                )}

                                {products.last_page > 1 && (
                                    <div className="flex flex-col gap-3 border-t border-slate-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                                        <p className="text-sm text-slate-500">Page {products.current_page} of {products.last_page}</p>
                                        <div className="flex flex-wrap gap-2">
                                            {products.links.filter((link) => link.url).map((link, index) => (
                                                <Link
                                                    key={index}
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
                                )}
                            </section>
                        </section>

                        <div className="min-w-0 xl:sticky xl:top-6">
                            <ManagementPanel key={selectedProduct ? `${selectedProduct.id}-${selectedProduct.moq}` : 'empty'} product={selectedProduct} />
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
