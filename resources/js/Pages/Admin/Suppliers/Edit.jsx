import AdminLayout from '@/Layouts/AdminLayout';
import { Head } from '@inertiajs/react';
import SupplierForm from './Form';

export default function EditSupplier({ supplier, statuses }) {
    return (
        <AdminLayout
            header={{
                title: 'Edit Supplier',
                subtitle: 'Update company details, approval status, and contact information.'
            }}
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
        </AdminLayout>
    );
}
