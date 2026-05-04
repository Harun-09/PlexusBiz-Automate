import AdminLayout from '@/Layouts/AdminLayout';
import { Head } from '@inertiajs/react';
import ProductForm from './Form';

export default function EditProduct({ product, suppliers, statuses }) {
    return (
        <AdminLayout
            header={{
                title: 'Edit Product',
                subtitle: 'Update product details, pricing, and inventory.'
            }}
        >
            <Head title={`Edit ${product.name}`} />
            <div className="py-8">
                <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
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
        </AdminLayout>
    );
}
