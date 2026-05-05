import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import UserForm from './Form';

export default function EditUser({ auth, user, roles, statuses }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div>
                    <h2 className="text-xl font-bold text-gray-950">Edit User</h2>
                    <p className="mt-1 text-sm text-gray-500">Update user details, role, and account status.</p>
                </div>
            }
        >
            <Head title={`Edit ${user.name}`} />

            <div className="py-8">
                <div className="w-full">
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
        </AuthenticatedLayout>
    );
}
