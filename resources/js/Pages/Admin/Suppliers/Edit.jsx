import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import SupplierForm from './Form';

export default function EditSupplier({ auth, supplier, statuses }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div>
                    <h2 className="text-xl font-bold text-gray-950">Edit Supplier</h2>
                    <p className="mt-1 text-sm text-gray-500">Update company details, approval status, and contact information.</p>
                </div>
            }
        >
            <Head title={`Edit ${supplier.company_name}`} />

            <div className="py-8">
                <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                    <div className="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm sm:p-8">
                        <SupplierForm
                            supplier={supplier}
                            statuses={statuses}
                            submitUrl={`/admin/suppliers/${supplier.id}`}
                            method="put"
                        />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
