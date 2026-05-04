import AdminLayout from '@/Layouts/AdminLayout';
import { Head } from '@inertiajs/react';
import UserForm from './Form';

export default function CreateUser({ auth, roles, statuses }) {
    return (
        <AdminLayout
            header={{
                title: 'Create User',
                subtitle: 'Add a new user to the platform with role assignment.'
            }}
        >
            <Head title="Create User" />

            <div className="py-8">
                <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                    <div className="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm sm:p-8">
                        <UserForm
                            roles={roles}
                            statuses={statuses}
                            submitUrl="/admin/users"
                            method="post"
                        />
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
