import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import ProductForm from './Form';

export default function CreateProduct({ auth, suppliers, statuses }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div>
                    <h2 className="text-xl font-bold text-gray-950">Create Product</h2>
                    <p className="mt-1 text-sm text-gray-500">Add a new product to the catalog.</p>
                </div>
            }
        >
            <Head title="Create Product" />
            <div className="py-8">
                <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                    <div className="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm sm:p-8">
                        <ProductForm suppliers={suppliers} statuses={statuses} submitUrl="/admin/products" method="post" />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
