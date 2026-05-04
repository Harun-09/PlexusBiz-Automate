import AdminLayout from '@/Layouts/AdminLayout';
import { Head } from '@inertiajs/react';
import SupplierForm from './Form';

export default function CreateSupplier({ users, statuses }) {
    return (
        <AdminLayout
            header={{
                title: 'Create Supplier',
                subtitle: 'Onboard a new supplier by linking a user account and entering company details.'
            }}
        >
            <Head title="Create Supplier" />

            <div className="py-8">
                <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                    <div className="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm sm:p-8">
                        <SupplierForm
                            users={users}
                            statuses={statuses}
                            submitUrl="/admin/suppliers"
                            method="post"
                        />
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
