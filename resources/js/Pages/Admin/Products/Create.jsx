import AdminLayout from '@/Layouts/AdminLayout';
import { Head } from '@inertiajs/react';
import ProductForm from './Form';

export default function CreateProduct({ suppliers, statuses }) {
    return (
        <AdminLayout
            header={{
                title: 'Create Product',
                subtitle: 'Add a new product to the catalog.'
            }}
        >
            <Head title="Create Product" />
            <div className="py-8">
                <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                    <div className="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm sm:p-8">
                        <ProductForm suppliers={suppliers} statuses={statuses} submitUrl="/admin/products" method="post" />
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
