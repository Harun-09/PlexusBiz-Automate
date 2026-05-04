import { useState } from 'react';

const emailDealImage = '/images/landing/hero-desk.jpg';
const appDealImage = '/images/landing/deal-two-monitor.jpg';

function PromoVisual({ image, eyebrow, title, copy }) {
    return (
        <div className="hidden lg:block">
            <div className="relative overflow-hidden rounded-[24px] border border-white/70 bg-white shadow-[0_20px_45px_-30px_rgba(15,23,42,0.55)]">
                <div className="aspect-[4/3]">
                    <img src={image} alt="" aria-hidden="true" className="h-full w-full object-cover" />
                </div>

                <div className="absolute inset-0 bg-gradient-to-tr from-white/10 via-transparent to-[#0b3d91]/25" />

                <div className="absolute left-3 top-3 flex flex-wrap gap-2">
                    <span className="rounded-full bg-white/90 px-3 py-1 text-[10px] font-black uppercase tracking-[0.16em] text-[#0b3d91]">
                        {eyebrow}
                    </span>
                </div>

                <div className="absolute bottom-3 left-3 right-3 rounded-[18px] border border-white/60 bg-white/92 p-3 shadow-[0_12px_30px_-24px_rgba(15,23,42,0.45)] backdrop-blur">
                    <p className="text-[10px] font-black uppercase tracking-[0.18em] text-[#0b3d91]">{title}</p>
                    <p className="mt-1 text-xs leading-5 text-slate-600">{copy}</p>
                </div>
            </div>
        </div>
    );
}

function MiniQr() {
    const cells = Array.from({ length: 49 }, (_, index) => {
        const row = Math.floor(index / 7);
        const col = index % 7;
        const finder =
            (row < 2 && col < 2) ||
            (row < 2 && col > 4) ||
            (row > 4 && col < 2);
        const fill = finder || (row + col) % 3 === 0 || (row === 3 && col === 3);

        return fill;
    });

    return (
        <div className="grid h-20 w-20 grid-cols-7 gap-1 rounded-2xl border border-[#d7e3f4] bg-white p-2 shadow-[0_12px_28px_-24px_rgba(15,23,42,0.45)]">
            {cells.map((fill, index) => (
                <span
                    key={index}
                    className={`rounded-[2px] ${fill ? 'bg-[#0b3d91]' : 'bg-[#dbe7fb]'}`}
                />
            ))}
        </div>
    );
}

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
        <section className="rounded-[28px] border border-[#d7e3f4] bg-gradient-to-b from-white to-[#f4f8ff] p-5 shadow-sm sm:p-7">
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

            <div className="mt-6 grid gap-5 lg:grid-cols-2">
                <article className="overflow-hidden rounded-[24px] border border-[#d7e3f4] bg-[#f8fbff] p-5 shadow-[0_14px_30px_-24px_rgba(15,23,42,0.45)]">
                    <div className="grid gap-5 lg:grid-cols-[minmax(0,1fr)_240px] lg:items-center">
                        <div className="min-w-0">
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

                        <PromoVisual
                            image={emailDealImage}
                            eyebrow="Homepage only"
                            title="Fresh deals landing here"
                            copy="Desktop gets a stronger visual rail while mobile keeps the signup flow compact."
                        />
                    </div>
                </article>

                <article className="overflow-hidden rounded-[24px] border border-[#d7e3f4] bg-[#f8fbff] p-5 shadow-[0_14px_30px_-24px_rgba(15,23,42,0.45)]">
                    <div className="grid gap-5 lg:grid-cols-[minmax(0,1fr)_240px] lg:items-center">
                        <div className="min-w-0">
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
                                <MiniQr />
                                <div className="text-xs">
                                    <p className="font-medium text-gray-900">Scan the QR code</p>
                                    <p className="text-gray-500">to download App</p>
                                </div>
                            </div>
                        </div>

                        <PromoVisual
                            image={appDealImage}
                            eyebrow="App access"
                            title="Download flow on desktop"
                            copy="The app rail can carry a stronger visual on larger screens without crowding the form."
                        />
                    </div>
                </article>
            </div>
        </section>
    );
}
