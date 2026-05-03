import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard({ auth, dashboard }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div>
                    <h2 className="text-xl font-semibold leading-tight text-gray-900">{dashboard.role.label} Dashboard</h2>
                    <p className="mt-1 text-sm text-gray-600">Account status: {dashboard.status}</p>
                </div>
            }
        >
            <Head title="Dashboard" />

            <div className="py-10">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="grid gap-4 md:grid-cols-3">
                        {dashboard.cards.map((card) => (
                            <section key={card.label} className="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                                <p className="text-sm font-medium text-gray-500">{card.label}</p>
                                <p className="mt-2 text-2xl font-semibold text-gray-950">{card.value}</p>
                                <p className="mt-3 text-sm leading-6 text-gray-600">{card.description}</p>
                            </section>
                        ))}
                    </div>

                    <div className="mt-6 grid gap-6 lg:grid-cols-[1fr_360px]">
                        <section className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                            <div className="flex items-center justify-between gap-4">
                                <div>
                                    <h3 className="text-base font-semibold text-gray-950">Permissions</h3>
                                    <p className="mt-1 text-sm text-gray-600">Resolved from assigned roles and direct permissions.</p>
                                </div>
                                <span className="rounded-full bg-emerald-50 px-3 py-1 text-sm font-medium text-emerald-700">
                                    {dashboard.permissions.length} active
                                </span>
                            </div>

                            <div className="mt-5 flex flex-wrap gap-2">
                                {dashboard.permissions.map((permission) => (
                                    <span
                                        key={permission}
                                        className="rounded-md border border-gray-200 bg-gray-50 px-3 py-1.5 text-sm font-medium text-gray-700"
                                    >
                                        {permission.replaceAll('_', ' ')}
                                    </span>
                                ))}
                            </div>
                        </section>

                        <section className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                            <h3 className="text-base font-semibold text-gray-950">Role Workspace</h3>
                            <div className="mt-4 divide-y divide-gray-100">
                                {dashboard.quickLinks.map((link) => (
                                    <a
                                        key={link.href}
                                        href={link.href}
                                        className="flex items-center justify-between py-3 text-sm font-medium text-gray-700 hover:text-indigo-700"
                                    >
                                        <span>{link.label}</span>
                                        <span aria-hidden="true">-&gt;</span>
                                    </a>
                                ))}
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
