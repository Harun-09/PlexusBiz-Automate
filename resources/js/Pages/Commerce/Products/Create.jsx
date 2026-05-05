import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import ProductForm from '@/Pages/Admin/Products/Form';

export default function Create({ auth, supplier, statuses }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div>
                    <h2 className="text-xl font-bold text-gray-950">Add Product</h2>
                    <p className="mt-1 text-sm text-gray-500">
                        Create a supplier-owned product record for the marketplace catalog.
                    </p>
                </div>
            }
        >
            <Head title="Add Product" />
            <div className="py-8">
                <div className="w-full">
                    <div className="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm sm:p-8">
                        <ProductForm
                            suppliers={[]}
                            statuses={statuses}
                            submitUrl={route('commerce.products.store')}
                            method="post"
                            defaultSupplierId={supplier.id}
                            hideSupplier
                            cancelUrl={route('commerce.products.index')}
                            supplierLabel={supplier.company_name}
                        />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
