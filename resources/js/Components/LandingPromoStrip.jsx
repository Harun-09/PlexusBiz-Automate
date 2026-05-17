import { useState } from 'react';
import { Link } from '@inertiajs/react';

const blueSurfaceGradient = 'bg-gradient-to-r from-[#4f7fe0] via-[#3f70d4] to-[#2953b1]';

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
                    className={`rounded-[2px] ${fill ? 'bg-[#3f70d4]' : 'bg-[#dbe7fb]'}`}
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
            <div className="grid gap-5 lg:grid-cols-2">
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
                                    className={`rounded-b-md px-6 py-2 text-sm font-semibold text-white transition hover:brightness-105 sm:rounded-l-none sm:rounded-r-md ${blueSurfaceGradient}`}
                                >
                                    Sign up
                                </button>
                            </form>
                            <Link href={route('products.bulk')} className="mt-3 inline-block text-sm text-[#3f70d4] hover:text-[#2953b1]">
                                View Latest Email Deals &rarr;
                            </Link>
                        </div>

                        <img
                            src="/images/store/banners/tools-feature-04.jpg"
                            alt=""
                            className="hidden min-h-[230px] w-full rounded-[24px] object-cover lg:block"
                            loading="lazy"
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
                                    className={`rounded-b-md px-5 py-2 text-sm font-semibold text-white transition hover:brightness-105 sm:rounded-l-none sm:rounded-r-md ${blueSurfaceGradient}`}
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

                        <img
                            src="/images/store/banners/tools-feature-05.jpg"
                            alt=""
                            className="hidden min-h-[230px] w-full rounded-[24px] object-cover lg:block"
                            loading="lazy"
                        />
                    </div>
                </article>
            </div>
        </section>
    );
}
