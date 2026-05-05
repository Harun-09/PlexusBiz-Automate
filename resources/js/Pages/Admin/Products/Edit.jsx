import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import ProductForm from './Form';

export default function EditProduct({ auth, product, suppliers, statuses }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div>
                    <h2 className="text-xl font-bold text-gray-950">Edit Product</h2>
                    <p className="mt-1 text-sm text-gray-500">Update product details, inventory, and the primary image. MOQ and tier pricing live in the bulk pricing workspace.</p>
                </div>
            }
        >
            <Head title={`Edit ${product.name}`} />
            <div className="py-8">
                <div className="w-full">
                    <div className="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm sm:p-8">
                        <ProductForm
                            product={product}
                            suppliers={suppliers}
                            statuses={statuses}
                            submitUrl={`/admin/products/${product.id}`}
                            method="put"
                        />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
