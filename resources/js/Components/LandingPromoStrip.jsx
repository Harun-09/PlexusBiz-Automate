import { useState } from 'react';

export default function LandingPromoStrip() {
    const [email, setEmail] = useState('');
    const [phone, setPhone] = useState('');

    const handleEmailSubmit = (event) => {
        event.preventDefault();
        alert('Thank you for subscribing!');
        setEmail('');
    };

    const handleSendLink = (event) => {
        event.preventDefault();
        alert('Download link sent!');
        setPhone('');
    };

    return (
        <section className="rounded-[28px] border border-[#d7e3f4] bg-white p-5 shadow-sm sm:p-7">
            <div className="flex flex-col gap-3 border-b border-[#d7e3f4] pb-5 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p className="text-[11px] font-black uppercase tracking-[0.22em] text-[#0b3d91]">
                        Stay connected
                    </p>
                    <h2 className="mt-2 text-2xl font-black tracking-[-0.05em] text-slate-900">
                        Deals and app access before the footer.
                    </h2>
                    <p className="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                        Keep the promo strip on the landing page where it belongs. Auth screens stay focused,
                        while the homepage keeps the marketing utilities.
                    </p>
                </div>

                <span className="inline-flex w-fit rounded-full border border-[#d7e3f4] bg-[#f4f8ff] px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-[#0b3d91]">
                    Homepage only
                </span>
            </div>

            <div className="mt-6 grid gap-6 lg:grid-cols-2">
                <div className="rounded-[22px] border border-[#d7e3f4] bg-[#f8fbff] p-5 shadow-[0_14px_30px_-24px_rgba(15,23,42,0.45)]">
                    <h3 className="text-lg font-black italic text-slate-900">Deals Just For You</h3>
                    <p className="mt-2 text-sm leading-6 text-slate-600">
                        Sign up to receive exclusive offers in your inbox.
                    </p>
                    <form onSubmit={handleEmailSubmit} className="mt-4 flex flex-col sm:flex-row">
                        <input
                            type="email"
                            value={email}
                            onChange={(event) => setEmail(event.target.value)}
                            placeholder="Enter your e-mail address"
                            className="min-w-0 flex-1 rounded-t-md border border-gray-300 px-4 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 sm:rounded-r-none sm:rounded-l-md"
                            required
                        />
                        <button
                            type="submit"
                            className="rounded-b-md bg-blue-600 px-6 py-2 text-sm font-semibold text-white hover:bg-blue-700 sm:rounded-l-none sm:rounded-r-md"
                        >
                            Sign up
                        </button>
                    </form>
                    <a href="#" className="mt-3 inline-block text-sm text-blue-600 hover:text-blue-800">
                        View Latest Email Deals &rarr;
                    </a>
                </div>

                <div className="rounded-[22px] border border-[#d7e3f4] bg-[#f8fbff] p-5 shadow-[0_14px_30px_-24px_rgba(15,23,42,0.45)]">
                    <h3 className="text-lg font-black text-slate-900">Download Our APP</h3>
                    <p className="mt-2 text-sm leading-6 text-slate-600">
                        Enter your phone number and we'll send a download link.
                    </p>
                    <form onSubmit={handleSendLink} className="mt-4 flex flex-col sm:flex-row">
                        <span className="rounded-t-md border border-b-0 border-gray-300 bg-gray-100 px-3 py-2 text-sm text-gray-600 sm:rounded-l-md sm:rounded-r-none sm:border-b sm:border-r-0">
                            +1
                        </span>
                        <input
                            type="tel"
                            value={phone}
                            onChange={(event) => setPhone(event.target.value)}
                            placeholder="Enter your phone number"
                            className="min-w-0 flex-1 rounded-none border border-gray-300 px-4 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                            required
                        />
                        <button
                            type="submit"
                            className="rounded-b-md bg-blue-500 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-600 sm:rounded-l-none sm:rounded-r-md"
                        >
                            Send Link
                        </button>
                    </form>
                    <div className="mt-4 flex flex-wrap items-center gap-3">
                        <span className="text-sm text-gray-500">OR</span>
                        <div className="h-12 w-12 rounded bg-gray-200" aria-hidden="true" />
                        <div className="text-xs">
                            <p className="font-medium text-gray-900">Scan QR code</p>
                            <p className="text-gray-500">to download App</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
