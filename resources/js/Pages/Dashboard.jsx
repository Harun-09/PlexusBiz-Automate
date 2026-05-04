import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link } from '@inertiajs/react';

const cardToneClasses = {
    blue: {
        shell: 'border-[#d9e6f8] bg-gradient-to-br from-[#f7fbff] via-white to-[#eef5ff]',
        badge: 'bg-[#e8f0ff] text-[#0b2e71]',
        value: 'text-[#0b2e71]',
    },
    emerald: {
        shell: 'border-[#d8eadf] bg-gradient-to-br from-[#f5fff7] via-white to-[#edf9f0]',
        badge: 'bg-[#e7f7ec] text-[#117a43]',
        value: 'text-[#0f7a43]',
    },
    amber: {
        shell: 'border-[#f2e1bf] bg-gradient-to-br from-[#fffaf1] via-white to-[#fff4dc]',
        badge: 'bg-[#fff0c9] text-[#9c5b00]',
        value: 'text-[#9c5b00]',
    },
    rose: {
        shell: 'border-[#f3d7dd] bg-gradient-to-br from-[#fff7f8] via-white to-[#fff0f2]',
        badge: 'bg-[#fde4e8] text-[#a11f47]',
        value: 'text-[#a11f47]',
    },
    slate: {
        shell: 'border-slate-200 bg-gradient-to-br from-white via-white to-slate-50',
        badge: 'bg-slate-100 text-slate-600',
        value: 'text-slate-900',
    },
};

function StatCard({ card }) {
    const tone = cardToneClasses[card.tone] ?? cardToneClasses.slate;

    return (
        <section className={`rounded-[24px] border p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md ${tone.shell}`}>
            <div className="flex items-start justify-between gap-3">
                <div>
                    <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-500">{card.label}</p>
                    <p className={`mt-2 text-[2rem] font-black tracking-[-0.05em] ${tone.value}`}>{card.value}</p>
                </div>
                <span className={`rounded-full px-3 py-1 text-[11px] font-black uppercase tracking-[0.18em] ${tone.badge}`}>
                    Live
                </span>
            </div>
            <p className="mt-3 text-sm leading-6 text-slate-600">{card.description}</p>
        </section>
    );
}

export default function Dashboard({ auth, dashboard }) {
    return (
        <AdminLayout
            header={{
                title: 'Dashboard',
                subtitle: 'Welcome back! Here is what\'s happening today.'
            }}
        >
            <Head title="Dashboard" />

            <div className="py-8">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    {/* Stats Cards */}
                    <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <div className="rounded-2xl border border-[#d7e3f4] bg-white p-6 shadow-sm">
                            <div className="flex items-center gap-4">
                                <div className="rounded-xl bg-[#0b2e71] p-3 text-white">
                                    <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p className="text-sm font-medium text-gray-600">Total Users</p>
                                    <p className="text-2xl font-bold text-gray-900">{dashboard.stats?.users || '1,284'}</p>
                                </div>
                            </div>
                        </div>

                        <div className="rounded-2xl border border-[#d7e3f4] bg-white p-6 shadow-sm">
                            <div className="flex items-center gap-4">
                                <div className="rounded-xl bg-[#ff8a00] p-3 text-white">
                                    <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h8.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H10.125c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                                </div>
                                <div>
                                    <p className="text-sm font-medium text-gray-600">Products</p>
                                    <p className="text-2xl font-bold text-gray-900">{dashboard.stats?.products || '3,842'}</p>
                                </div>
                            </div>
                        </div>

                        <div className="rounded-2xl border border-[#d7e3f4] bg-white p-6 shadow-sm">
                            <div className="flex items-center gap-4">
                                <div className="rounded-xl bg-emerald-500 p-3 text-white">
                                    <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p className="text-sm font-medium text-gray-600">Revenue</p>
                                    <p className="text-2xl font-bold text-gray-900">{dashboard.stats?.revenue || '$48.2K'}</p>
                                </div>
                            </div>
                        </div>

                        <div className="rounded-2xl border border-[#d7e3f4] bg-white p-6 shadow-sm">
                            <div className="flex items-center gap-4">
                                <div className="rounded-xl bg-blue-500 p-3 text-white">
                                    <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v8.25A2.25 2.25 0 006 16.5h2.25m3 .75h.01" />
                                    </svg>
                                </div>
                                <div>
                                    <p className="text-sm font-medium text-gray-600">Orders</p>
                                    <p className="text-2xl font-bold text-gray-900">{dashboard.stats?.orders || '156'}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Quick Actions */}
                    <div className="mt-6 grid gap-6 lg:grid-cols-3">
                        <Link href={route('admin.users.create')} className="group flex items-center gap-4 rounded-2xl border border-[#d7e3f4] bg-white p-6 shadow-sm transition hover:border-[#0b2e71] hover:shadow-md">
                            <div className="rounded-xl bg-[#0b2e71]/10 p-3 text-[#0b2e71] transition group-hover:bg-[#0b2e71] group-hover:text-white">
                                <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766z" />
                                </svg>
                            </div>
                            <div>
                                <p className="font-semibold text-gray-900">Add User</p>
                                <p className="text-sm text-gray-500">Create new user account</p>
                            </div>
                        </Link>

                        <Link href={route('admin.products.create')} className="group flex items-center gap-4 rounded-2xl border border-[#d7e3f4] bg-white p-6 shadow-sm transition hover:border-[#ff8a00] hover:shadow-md">
                            <div className="rounded-xl bg-[#ff8a00]/10 p-3 text-[#ff8a00] transition group-hover:bg-[#ff8a00] group-hover:text-white">
                                <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </div>
                            <div>
                                <p className="font-semibold text-gray-900">Add Product</p>
                                <p className="text-sm text-gray-500">Add new product to catalog</p>
                            </div>
                        </Link>

                        <Link href={route('admin.suppliers.create')} className="group flex items-center gap-4 rounded-2xl border border-[#d7e3f4] bg-white p-6 shadow-sm transition hover:border-emerald-500 hover:shadow-md">
                            <div className="rounded-xl bg-emerald-500/10 p-3 text-emerald-500 transition group-hover:bg-emerald-500 group-hover:text-white">
                                <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-6 0H5.625a2.25 2.25 0 01-2.25-2.25V14.81a2.25 2.25 0 012.25-2.25h.885c.465 0 .91.184 1.238.513l.655.655c.576.576.85.855 1.253 1.05a3 3 0 012.832 0c.403.195.677.474 1.253 1.05l.655.655c.328.329.773.513 1.238.513h.885a2.25 2.25 0 012.25 2.25v1.395c0 .621-.504 1.125-1.125 1.125H18.375a1.5 1.5 0 00-1.5 1.5v.75m-6-18a1.5 1.5 0 011.5-1.5H18a1.5 1.5 0 011.5 1.5v2.25a1.5 1.5 0 01-1.5 1.5h-2.25a1.5 1.5 0 01-1.5-1.5V3.375z" />
                                </svg>
                            </div>
                            <div>
                                <p className="font-semibold text-gray-900">Add Supplier</p>
                                <p className="text-sm text-gray-500">Register new supplier</p>
                            </div>
                        </Link>
                    </div>

                    {/* Recent Activity & Permissions */}
                    <div className="mt-6 grid gap-6 lg:grid-cols-[1fr_380px]">
                        <section className="rounded-2xl border border-[#d7e3f4] bg-white p-6 shadow-sm">
                            <div className="flex items-center justify-between">
                                <h3 className="text-lg font-bold text-gray-900">Recent Activity</h3>
                                <span className="rounded-full bg-[#0b2e71]/10 px-3 py-1 text-xs font-medium text-[#0b2e71]">Live</span>
                            </div>
                            <div className="mt-4 space-y-4">
                                {dashboard.recentActivity?.map((activity, index) => (
                                    <div key={index} className="flex items-start gap-3 border-b border-gray-100 pb-3 last:border-0">
                                        <div className="rounded-full bg-[#0b2e71]/10 p-2 text-[#0b2e71]">
                                            <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div className="flex-1">
                                            <p className="text-sm font-medium text-gray-900">{activity.title}</p>
                                            <p className="text-xs text-gray-500">{activity.time}</p>
                                        </div>
                                    </div>
                                )) || (
                                    <>
                                        <div className="flex items-start gap-3 border-b border-gray-100 pb-3">
                                            <div className="rounded-full bg-[#0b2e71]/10 p-2 text-[#0b2e71]">
                                                <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766z" />
                                                </svg>
                                            </div>
                                            <div className="flex-1">
                                                <p className="text-sm font-medium text-gray-900">New user registered</p>
                                                <p className="text-xs text-gray-500">John Doe - 2 minutes ago</p>
                                            </div>
                                        </div>
                                        <div className="flex items-start gap-3 border-b border-gray-100 pb-3">
                                            <div className="rounded-full bg-[#ff8a00]/10 p-2 text-[#ff8a00]">
                                                <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h8.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H10.125c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                                </svg>
                                            </div>
                                            <div className="flex-1">
                                                <p className="text-sm font-medium text-gray-900">New order received</p>
                                                <p className="text-xs text-gray-500">Order #1234 - 15 minutes ago</p>
                                            </div>
                                        </div>
                                        <div className="flex items-start gap-3">
                                            <div className="rounded-full bg-emerald-500/10 p-2 text-emerald-500">
                                                <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div className="flex-1">
                                                <p className="text-sm font-medium text-gray-900">Product approved</p>
                                                <p className="text-xs text-gray-500">iPhone 15 Pro - 1 hour ago</p>
                                            </div>
                                        </div>
                                    </>
                                )}
                            </div>
                        </section>

                        <section className="rounded-2xl border border-[#d7e3f4] bg-white p-6 shadow-sm">
                            <div className="flex items-center justify-between">
                                <h3 className="text-lg font-bold text-gray-900">Your Permissions</h3>
                                <span className="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                                    {dashboard.permissions?.length || 5} active
                                </span>
                            </div>
                            <div className="mt-4 flex flex-wrap gap-2">
                                {(dashboard.permissions || ['View Users', 'Edit Products', 'Manage Orders', 'View Analytics', 'Edit Settings']).map((permission) => (
                                    <span
                                        key={permission}
                                        className="rounded-lg border border-[#d7e3f4] bg-[#f8fafc] px-3 py-1.5 text-xs font-medium text-gray-700"
                                    >
                                        {permission.replaceAll('_', ' ')}
                                    </span>
                                ))}
                            </div>
                            <div className="mt-6 rounded-xl bg-[#0b2e71] p-4 text-white">
                                <p className="text-sm font-medium">Role: <span className="text-[#ff8a00]">{dashboard.role?.label || 'Administrator'}</span></p>
                                <p className="mt-1 text-xs text-white/70">Full access to all admin features</p>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
