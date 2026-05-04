import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

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
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div>
                    <p className="text-[11px] font-black uppercase tracking-[0.24em] text-slate-500">Live snapshot</p>
                    <h2 className="mt-1 text-2xl font-black tracking-[-0.04em] text-gray-900">{dashboard.role.label} Dashboard</h2>
                    <p className="mt-1 text-sm text-gray-600">Account status: {dashboard.status}. The cards below are pulled from live records on every visit.</p>
                </div>
            }
        >
            <Head title="Dashboard" />

            <div className="py-10">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        {dashboard.cards.map((card) => (
                            <StatCard key={card.label} card={card} />
                        ))}
                    </div>

                    <div className="mt-6 grid gap-6 lg:grid-cols-[1fr_360px]">
                        <section className="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
                            <div className="flex items-center justify-between gap-4">
                                <div>
                                    <h3 className="text-base font-black tracking-[-0.03em] text-gray-950">Permissions</h3>
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

                        <section className="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
                            <h3 className="text-base font-black tracking-[-0.03em] text-gray-950">Role Workspace</h3>
                            <div className="mt-4 divide-y divide-gray-100">
                                {dashboard.quickLinks.map((link) => (
                                    <a
                                        key={link.href}
                                        href={link.href}
                                        className="flex items-center justify-between py-3 text-sm font-semibold text-gray-700 transition hover:text-indigo-700"
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
