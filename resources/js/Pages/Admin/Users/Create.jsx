import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import UserForm from './Form';

export default function CreateUser({ auth, roles, statuses }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div>
                    <h2 className="text-xl font-bold text-gray-950">Create User</h2>
                    <p className="mt-1 text-sm text-gray-500">Add a new user to the platform with role assignment.</p>
                </div>
            }
        >
            <Head title="Create User" />

            <div className="py-8">
                <div className="w-full">
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
        </AuthenticatedLayout>
    );
}
