import { Head } from '@inertiajs/react';
import FrontendLayout from '@/Layouts/FrontendLayout';

export default function Page({ page, title, slug }) {
    return (
        <FrontendLayout>
            <Head title={`${title} - PlexusBiz Automate`} />
            
            <div className="min-h-screen bg-[#f4f5f7]">
                {/* Blue Gradient Header Box (Matching User Request) */}
                <div className="bg-gradient-to-r from-[#10306c] via-[#205db8] to-[#409cf2] pt-12 pb-24 text-white">
                    <div className="mx-auto max-w-[1200px] px-4 sm:px-6 lg:px-8">
                        <div className="max-w-3xl">
                            <h2 className="text-[13px] font-bold uppercase tracking-[0.2em] text-blue-200 mb-2">
                                PlexusBiz Information
                            </h2>
                            <h1 className="text-4xl sm:text-5xl font-black tracking-tight mb-4">
                                {title}
                            </h1>
                            <p className="text-blue-100 text-lg leading-relaxed max-w-2xl">
                                Welcome to our information center. Below you'll find comprehensive details and policies regarding {title}. If you have any further questions, feel free to contact our support team.
                            </p>
                            
                            <div className="mt-8 flex flex-wrap gap-3">
                                <span className="inline-flex items-center rounded-full border border-white/30 bg-white/10 px-4 py-1.5 text-xs font-semibold text-white shadow-sm backdrop-blur-sm">
                                    Trusted Sellers
                                </span>
                                <span className="inline-flex items-center rounded-full border border-white/30 bg-white/10 px-4 py-1.5 text-xs font-semibold text-white shadow-sm backdrop-blur-sm">
                                    Secure Checkout
                                </span>
                                <span className="inline-flex items-center rounded-full border border-white/30 bg-white/10 px-4 py-1.5 text-xs font-semibold text-white shadow-sm backdrop-blur-sm">
                                    Fast Delivery
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <main className="mx-auto max-w-[1200px] px-4 sm:px-6 lg:px-8 -mt-12 pb-16 relative z-10">
                    
                    {/* Dynamic Content Article Section */}
                    <div className="bg-white rounded-2xl shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] border border-gray-100 p-8 md:p-12 mb-10">
                        <div className="max-w-4xl mx-auto">
                            <div 
                                className="prose prose-blue max-w-none text-gray-700 prose-headings:text-[#0b192c] prose-a:text-[#FF8B00] hover:prose-a:text-[#E67D00] prose-p:leading-relaxed"
                                dangerouslySetInnerHTML={{ __html: page.content }}
                            />
                            
                            <div className="mt-12 pt-6 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4">
                                <span className="text-sm font-medium text-gray-500">Was this information helpful?</span>
                                <div className="flex gap-2">
                                    <button className="px-5 py-2 text-sm font-bold text-gray-700 bg-gray-100 rounded-full hover:bg-gray-200 transition">Yes</button>
                                    <button className="px-5 py-2 text-sm font-bold text-gray-700 bg-gray-100 rounded-full hover:bg-gray-200 transition">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </FrontendLayout>
    );
}
