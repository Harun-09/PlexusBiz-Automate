import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import ProductForm from '@/Pages/Admin/Products/Form';

export default function Edit({ auth, supplier, product, statuses }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div>
                    <h2 className="text-xl font-bold text-gray-950">Edit Product</h2>
                    <p className="mt-1 text-sm text-gray-500">
                        Update your supplier product details, price, stock, and publication status.
                    </p>
                </div>
            }
        >
            <Head title={`Edit ${product.name}`} />
            <div className="py-8">
                <div className="w-full">
                    <div className="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm sm:p-8">
                        <ProductForm
                            product={product}
                            suppliers={[]}
                            statuses={statuses}
                            submitUrl={route('commerce.products.update', product.id)}
                            method="put"
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
