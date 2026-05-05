import ModuleWorkspacePage from '@/Components/ModuleWorkspacePage';

const buildModuleConfig = (auth, supplierStatus = null) => ({
    eyebrow: 'E-Commerce',
    tag: 'Inventory',
    theme: 'slate',
    heroTitle: 'Product operations',
    heroCopy: 'Supplier-owned catalog, stock, MOQ, and status live here for inventory management and B2B visibility.',
    panelTitle: 'What this page covers',
    panelCopy: 'This page is the operations view for supplier catalog records and stock tracking, separate from the buyer marketplace.',
    highlights: [
        { label: 'Supplier ownership', detail: 'Each row keeps the owning supplier visible for operational review.' },
        { label: 'Stock tracking', detail: 'Available inventory is shown with the current product status.' },
        { label: 'MOQ rules', detail: 'Minimum order quantity remains visible without mixing it into checkout screens.' },
    ],
    panelBullets: [
        { label: 'Catalog control', detail: 'Use this list to inspect the active product catalog and its status.' },
        { label: 'Bulk pricing', detail: 'Jump to the dedicated MOQ and tier management page when rules need edits.' },
        { label: 'Supplier flow', detail: 'The list supports supplier-owned product operations and admin oversight.' },
    ],
    actions: [
        ...(auth?.user?.roles?.includes('admin')
            ? [
                { label: 'Bulk Pricing & MOQ', href: '/admin/bulk-pricing', variant: 'primary' },
                { label: 'Supplier Onboarding', href: '/admin/suppliers', variant: 'secondary' },
            ]
            : []),
        ...(auth?.user?.roles?.includes('supplier') && supplierStatus === 'approved'
            ? [
                {
                    label: 'Add Product',
                    href: route('commerce.products.create'),
                    variant: 'primary',
                },
            ]
            : []),
    ],
    tableTitle: 'Inventory records',
    searchPlaceholder: 'Search SKU or product',
    emptyTitle: 'No products found',
});

export default function Index(props) {
    const supplierStatus = props.auth?.user?.supplier?.status || null;

    return <ModuleWorkspacePage {...props} module={buildModuleConfig(props.auth, supplierStatus)} />;
}
