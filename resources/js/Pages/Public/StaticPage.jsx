import { Head, Link } from '@inertiajs/react';
import FrontendLayout from '@/Layouts/FrontendLayout';

export default function StaticPage({ title, lead, sections = [] }) {
    return (
        <FrontendLayout>
            <Head title={`${title} - PlexusBiz Automate`} />

            <div className="min-h-screen bg-[#f5f8ff]">
                <section className="bg-gradient-to-r from-[#0f3d93] via-[#1d56b8] to-[#2b73dd] px-6 pb-16 pt-14 text-white sm:px-10 lg:px-16">
                    <div className="mx-auto max-w-5xl">
                        <p className="text-xs font-black uppercase tracking-[0.22em] text-[#b8d5ff]">PlexusBiz Public Info</p>
                        <h1 className="mt-3 text-3xl font-black tracking-[-0.03em] sm:text-4xl">{title}</h1>
                        <p className="mt-4 max-w-3xl text-sm leading-7 text-white/90 sm:text-base">{lead}</p>
                        <div className="mt-6 flex flex-wrap gap-3">
                            <Link
                                href={route('landing')}
                                className="inline-flex items-center rounded-full bg-white px-4 py-2 text-sm font-bold text-[#0f3d93] transition hover:bg-[#e8f0ff]"
                            >
                                Back to home
                            </Link>
                            <Link
                                href={route('contact')}
                                className="inline-flex items-center rounded-full border border-white/30 px-4 py-2 text-sm font-bold text-white transition hover:bg-white/10"
                            >
                                Contact support
                            </Link>
                        </div>
                    </div>
                </section>

                <section className="-mt-8 px-6 pb-20 sm:px-10 lg:px-16">
                    <div className="mx-auto grid max-w-5xl gap-4 md:grid-cols-3">
                        {sections.map((section) => (
                            <article key={section.title} className="rounded-2xl border border-[#d9e6ff] bg-white p-6 shadow-sm">
                                <h2 className="text-base font-black text-[#0c2f74]">{section.title}</h2>
                                <p className="mt-3 text-sm leading-7 text-slate-600">{section.content}</p>
                            </article>
                        ))}
                    </div>
                </section>
            </div>
        </FrontendLayout>
    );
}
