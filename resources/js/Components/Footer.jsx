import { useState } from 'react';
import { Link } from '@inertiajs/react';

export default function Footer() {
    const [email, setEmail] = useState('');
    const [phone, setPhone] = useState('');

    const handleEmailSubmit = (e) => {
        e.preventDefault();
        alert('Thank you for subscribing!');
        setEmail('');
    };

    const handleSendLink = (e) => {
        e.preventDefault();
        alert('Download link sent!');
        setPhone('');
    };

    return (
        <>
            {/* SEO Section */}
            <section className="bg-gray-50 border-t border-gray-200 py-8">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <p className="text-sm text-gray-600 leading-relaxed">
                        Make <strong>PlexusBiz</strong> your one-stop B2B marketplace for industrial supplies, 
                        office equipment, and safety gear. Serving businesses across Asia Pacific and beyond 
                        with competitive wholesale pricing and volume discounts.
                    </p>
                </div>
            </section>

            {/* Deals & App Cards */}
            <section className="bg-indigo-50 border-t border-indigo-100 py-8">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        {/* Card 1 - Email */}
                        <div className="bg-white rounded-xl shadow p-6">
                            <h3 className="text-lg font-bold text-gray-900 italic mb-2">
                                Deals Just For You
                            </h3>
                            <p className="text-sm text-gray-600 mb-4">
                                Sign up to receive exclusive offers in your inbox.
                            </p>
                            <form onSubmit={handleEmailSubmit} className="flex">
                                <input
                                    type="email"
                                    value={email}
                                    onChange={(e) => setEmail(e.target.value)}
                                    placeholder="Enter your e-mail address"
                                    className="flex-1 px-4 py-2 text-sm border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                />
                                <button
                                    type="submit"
                                    className="px-6 py-2 bg-blue-600 text-white text-sm font-semibold rounded-r-md hover:bg-blue-700"
                                >
                                    Sign up
                                </button>
                            </form>
                            <Link href="#" className="text-sm text-blue-600 hover:text-blue-800 mt-3 inline-block">
                                View Latest Email Deals →
                            </Link>
                        </div>

                        {/* Card 2 - App */}
                        <div className="bg-white rounded-xl shadow p-6">
                            <h3 className="text-lg font-bold text-gray-900 mb-2">
                                Download Our APP
                            </h3>
                            <p className="text-sm text-gray-600 mb-4">
                                Enter your phone number and we'll send a download link.
                            </p>
                            <form onSubmit={handleSendLink} className="flex">
                                <span className="px-3 py-2 bg-gray-100 border border-r-0 border-gray-300 rounded-l-md text-sm text-gray-600">
                                    +1
                                </span>
                                <input
                                    type="tel"
                                    value={phone}
                                    onChange={(e) => setPhone(e.target.value)}
                                    placeholder="Enter your phone number"
                                    className="flex-1 px-4 py-2 text-sm border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                />
                                <button
                                    type="submit"
                                    className="px-5 py-2 bg-blue-500 text-white text-sm font-semibold rounded-r-md hover:bg-blue-600"
                                >
                                    Send Link
                                </button>
                            </form>
                            <div className="flex items-center gap-3 mt-4">
                                <span className="text-sm text-gray-500">OR</span>
                                <div className="w-12 h-12 bg-gray-200 rounded"></div>
                                <div className="text-xs">
                                    <p className="font-medium text-gray-900">Scan QR code</p>
                                    <p className="text-gray-500">to download App</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            {/* Main Footer */}
            <footer className="bg-slate-900 text-white py-8">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-8 text-sm">
                        <div>
                            <h4 className="font-semibold mb-3">Quick Links</h4>
                            <ul className="space-y-2 text-slate-400">
                                <li><Link href="/marketplace" className="hover:text-white">Marketplace</Link></li>
                                <li><Link href="/about" className="hover:text-white">About Us</Link></li>
                                <li><Link href="/contact" className="hover:text-white">Contact</Link></li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="font-semibold mb-3">Support</h4>
                            <ul className="space-y-2 text-slate-400">
                                <li><Link href="/support/tickets" className="hover:text-white">Help Center</Link></li>
                                <li><Link href="/faq" className="hover:text-white">FAQs</Link></li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="font-semibold mb-3">Contact</h4>
                            <ul className="space-y-2 text-slate-400">
                                <li>Dhaka, Bangladesh</li>
                                <li>support@plexusbiz.com</li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="font-semibold mb-3">Legal</h4>
                            <ul className="space-y-2 text-slate-400">
                                <li><Link href="/terms" className="hover:text-white">Terms</Link></li>
                                <li><Link href="/privacy" className="hover:text-white">Privacy</Link></li>
                            </ul>
                        </div>
                    </div>
                    <div className="border-t border-slate-800 mt-8 pt-4 text-center text-sm text-slate-400">
                        © 2024 PlexusBiz Automate. All rights reserved.
                    </div>
                </div>
            </footer>
        </>
    );
}
