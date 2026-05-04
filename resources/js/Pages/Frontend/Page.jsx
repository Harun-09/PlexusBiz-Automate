import { Head } from '@inertiajs/react';
import FrontendLayout from '@/Layouts/FrontendLayout';

export default function Page({ page, title, slug }) {
    const isPrivacyPolicy = slug === 'privacy-policy' || title.toLowerCase().includes('privacy');
    const headerBg = isPrivacyPolicy 
        ? "bg-gradient-to-r from-[#0d6e5a] via-[#10b981] to-[#34d399]" 
        : "bg-gradient-to-r from-[#10306c] via-[#205db8] to-[#409cf2]";
    const kickerText = isPrivacyPolicy ? "DATA PROTECTION" : "PLEXUSBIZ INFORMATION";
    const kickerColor = isPrivacyPolicy ? "text-emerald-100" : "text-blue-200";

    return (
        <FrontendLayout>
            <Head title={`${title} - PlexusBiz Automate`} />
            
            <div className="min-h-screen bg-[#f8fafc]">
                {/* Dynamic Gradient Header Box */}
                <div className={`${headerBg} pt-16 pb-32 text-white`}>
                    <div className="mx-auto max-w-[1500px] px-6 sm:px-10 lg:px-16">
                        <div className="max-w-5xl">
                            <h2 className={`text-sm font-bold uppercase tracking-[0.3em] ${kickerColor} mb-3`}>
                                {kickerText}
                            </h2>
                            <h1 className="text-5xl sm:text-7xl font-black tracking-tight mb-6">
                                {title}
                            </h1>
                            <p className="text-white/90 text-xl sm:text-2xl leading-relaxed max-w-4xl">
                                Welcome to our information center. Below you'll find comprehensive details and policies regarding {title}. If you have any further questions, feel free to contact our support team.
                            </p>
                            
                            <div className="mt-10 flex flex-wrap gap-4">
                                <span className="inline-flex items-center rounded-full border border-white/30 bg-white/10 px-5 py-2 text-base font-semibold text-white shadow-sm backdrop-blur-md transition hover:bg-white/20">
                                    {isPrivacyPolicy ? 'Information We Collect' : 'Trusted Sellers'}
                                </span>
                                <span className="inline-flex items-center rounded-full border border-white/30 bg-white/10 px-5 py-2 text-base font-semibold text-white shadow-sm backdrop-blur-md transition hover:bg-white/20">
                                    {isPrivacyPolicy ? 'How We Use Data' : 'Secure Checkout'}
                                </span>
                                <span className="inline-flex items-center rounded-full border border-white/30 bg-white/10 px-5 py-2 text-base font-semibold text-white shadow-sm backdrop-blur-md transition hover:bg-white/20">
                                    {isPrivacyPolicy ? 'Security Practices' : 'Fast Delivery'}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <main className="mx-auto max-w-[1500px] px-6 sm:px-10 lg:px-16 -mt-16 pb-24 relative z-10">
                    
                    {/* Dynamic Content Article Section */}
                    <div className="bg-white rounded-[32px] shadow-[0_20px_70px_-15px_rgba(0,0,0,0.12)] border border-gray-100 p-10 md:p-16 mb-12">
                        <div className="max-w-6xl mx-auto">
                            <div 
                                className="prose prose-xl prose-slate max-w-none text-gray-700 prose-headings:text-[#0b192c] prose-headings:font-black prose-a:text-[#FF8B00] hover:prose-a:text-[#E67D00] prose-p:leading-relaxed prose-p:text-xl"
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
