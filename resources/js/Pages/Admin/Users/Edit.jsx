import AdminLayout from '@/Layouts/AdminLayout';
import { Head } from '@inertiajs/react';
import UserForm from './Form';

export default function EditUser({ user, roles, statuses }) {
    return (
        <AdminLayout
            header={{
                title: 'Edit User',
                subtitle: 'Update user details, role, and account status.'
            }}
        >
            <Head title={`Edit ${user.name}`} />

            <div className="py-8">
                <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                    <div className="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm sm:p-8">
                        <UserForm
                            user={user}
                            roles={roles}
                            statuses={statuses}
                            submitUrl={`/admin/users/${user.id}`}
                            method="put"
                        />
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
