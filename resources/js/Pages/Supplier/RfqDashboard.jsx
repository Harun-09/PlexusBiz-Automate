import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function RfqDashboard({ auth, workspace, filters }) {
    const rows = workspace?.rows || [];
    
    return (
        <AuthenticatedLayout user={auth?.user}>
            <Head title="RFQ Dashboard" />
            
            <div className="flex min-h-screen bg-gray-50 dark:bg-gray-900">
                {/* Domain Navigation Sidebar */}
                <aside className="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 hidden lg:block">
                    <div className="p-6">
                        <h2 className="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4">Domains</h2>
                        <nav className="space-y-2">
                            <Link href="#" className="flex items-center px-4 py-2.5 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-semibold rounded-lg">
                                <svg className="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                RFQ Management
                            </Link>
                            <Link href="/commerce/products" className="flex items-center px-4 py-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-750 font-medium rounded-lg transition-colors">
                                <svg className="w-5 h-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                Product Catalog
                            </Link>
                            <Link href="/commerce/supplier-orders" className="flex items-center px-4 py-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-750 font-medium rounded-lg transition-colors">
                                <svg className="w-5 h-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                Fulfillment
                            </Link>
                            <Link href="/supplier/analytics" className="flex items-center px-4 py-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-750 font-medium rounded-lg transition-colors">
                                <svg className="w-5 h-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Analytics
                            </Link>
                        </nav>
                    </div>
                </aside>

                {/* Main Content */}
                <main className="flex-1 overflow-y-auto">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                        <div className="mb-8">
                            <h1 className="text-2xl font-extrabold text-gray-900 dark:text-white">RFQ Dashboard</h1>
                            <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage and respond to buyer quote requests.</p>
                        </div>

                        {/* Summary Cards */}
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div className="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex flex-col justify-center">
                                <dt className="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total RFQs</dt>
                                <dd className="mt-2 text-3xl font-extrabold text-gray-900 dark:text-white">{workspace?.metrics?.[0]?.value || rows.length}</dd>
                            </div>
                            <div className="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex flex-col justify-center">
                                <dt className="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Awaiting Quote</dt>
                                <dd className="mt-2 text-3xl font-extrabold text-gray-900 dark:text-white">{workspace?.metrics?.[1]?.value || rows.filter(r => r['Response Status'] === 'awaiting_quote').length}</dd>
                            </div>
                            <div className="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex flex-col justify-center">
                                <dt className="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Accepted Quotes</dt>
                                <dd className="mt-2 text-3xl font-extrabold text-green-600 dark:text-green-400">{workspace?.metrics?.[3]?.value || rows.filter(r => r['Response Status'] === 'accepted').length}</dd>
                            </div>
                        </div>

                        {/* RFQ Table */}
                        <div className="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div className="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                                <h3 className="text-lg font-bold leading-6 text-gray-900 dark:text-white">Pending & Active Quotes</h3>
                            </div>
                            
                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead className="bg-gray-50 dark:bg-gray-900/50">
                                        <tr>
                                            <th className="px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">RFQ / Buyer</th>
                                            <th className="px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Items</th>
                                            <th className="px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                            <th className="px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quote Amount</th>
                                            <th className="px-6 py-3.5 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        {rows.length === 0 ? (
                                            <tr>
                                                <td colSpan="5" className="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                                    No RFQs found in the system.
                                                </td>
                                            </tr>
                                        ) : rows.map((row, idx) => (
                                            <tr key={idx} className="hover:bg-gray-50 dark:hover:bg-gray-750/50 transition-colors">
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="text-sm font-semibold text-gray-900 dark:text-white">{row.RFQ}</div>
                                                    <div className="text-sm text-gray-500 dark:text-gray-400">{row.Buyer}</div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {row.Items} items
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <span className={`px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        ${row['Response Status'] === 'accepted' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 
                                                          row['Response Status'] === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' :
                                                          row['Response Status'] === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 
                                                          'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'}`}>
                                                        {row['Response Status'] === 'awaiting_quote' ? 'Awaiting Quote' : row['Response Status'].charAt(0).toUpperCase() + row['Response Status'].slice(1)}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                    {row.Quote}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    {Array.isArray(row.Action) ? row.Action.map((action, actionIdx) => (
                                                        <Link 
                                                            key={actionIdx}
                                                            href={action.href}
                                                            className="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                                        >
                                                            {action.label}
                                                        </Link>
                                                    )) : (
                                                        <span className="text-gray-400">{row.Action}</span>
                                                    )}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </AuthenticatedLayout>
    );
}
