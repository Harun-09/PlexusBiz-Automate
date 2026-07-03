import React from 'react';
import { Head } from '@inertiajs/react';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    BarChart,
    Bar,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip as RechartsTooltip,
    Legend,
    ResponsiveContainer,
    LineChart,
    Line
} from 'recharts';

export default function AnalyticsIndex({ auth, topSearches, rfqTrends, priceTrends }) {
    return (
        <AuthenticatedLayout user={auth?.user}>
            <Head title="Supplier Analytics & BI" />

            <div className="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <div className="flex justify-between items-center">
                    <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Analytics & Business Intelligence</h1>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Demand Forecasting (Top Searches) */}
                    <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                        <h2 className="text-lg font-medium text-gray-900 dark:text-white mb-4">Demand Forecasting (Top Searches - Last 30 Days)</h2>
                        <div className="h-72">
                            <ResponsiveContainer width="100%" height="100%">
                                <BarChart data={topSearches} margin={{ top: 5, right: 20, bottom: 5, left: 0 }}>
                                    <CartesianGrid strokeDasharray="3 3" />
                                    <XAxis dataKey="query" />
                                    <YAxis />
                                    <RechartsTooltip />
                                    <Legend />
                                    <Bar dataKey="count" fill="#3b82f6" name="Search Count" />
                                </BarChart>
                            </ResponsiveContainer>
                        </div>
                    </div>

                    {/* RFQ Volume Trend */}
                    <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                        <h2 className="text-lg font-medium text-gray-900 dark:text-white mb-4">RFQ Volume Trend (Last 30 Days)</h2>
                        <div className="h-72">
                            <ResponsiveContainer width="100%" height="100%">
                                <LineChart data={rfqTrends} margin={{ top: 5, right: 20, bottom: 5, left: 0 }}>
                                    <CartesianGrid strokeDasharray="3 3" />
                                    <XAxis dataKey="date" />
                                    <YAxis />
                                    <RechartsTooltip />
                                    <Legend />
                                    <Line type="monotone" dataKey="total" stroke="#10b981" name="RFQs Received" strokeWidth={2} />
                                </LineChart>
                            </ResponsiveContainer>
                        </div>
                    </div>

                    {/* Price Trends */}
                    <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-6 lg:col-span-2">
                        <h2 className="text-lg font-medium text-gray-900 dark:text-white mb-4">Market Price Trends (Top 5 Products)</h2>
                        
                        {priceTrends.length === 0 ? (
                            <p className="text-gray-500">No sufficient RFQ data to show price trends.</p>
                        ) : (
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {priceTrends.map((pt, idx) => (
                                    <div key={idx} className="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                        <h3 className="text-md font-semibold text-gray-700 dark:text-gray-300 mb-2">{pt.product_name}</h3>
                                        <div className="h-48">
                                            <ResponsiveContainer width="100%" height="100%">
                                                <LineChart data={pt.trends} margin={{ top: 5, right: 10, bottom: 5, left: 0 }}>
                                                    <CartesianGrid strokeDasharray="3 3" />
                                                    <XAxis dataKey="date" />
                                                    <YAxis />
                                                    <RechartsTooltip />
                                                    <Line type="monotone" dataKey="avg_price" stroke="#f59e0b" name="Avg Target Price" strokeWidth={2} />
                                                </LineChart>
                                            </ResponsiveContainer>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>

                <div className="mt-8 flex justify-center">
                    <FeedbackWidget context="supplier.analytics" />
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

function FeedbackWidget({ context }) {
    const [showChat, setShowChat] = React.useState(false);
    const [feedbackGiven, setFeedbackGiven] = React.useState(false);
    const [message, setMessage] = React.useState('');
    const [submitting, setSubmitting] = React.useState(false);

    const handleFeedback = (isHelpful) => {
        axios.post('/feedback', { is_helpful: isHelpful, context }).catch(() => {});
        setFeedbackGiven(true);
        if (!isHelpful) {
            setShowChat(true);
        }
    };

    const submitMessage = () => {
        if (!message.trim()) return;
        setSubmitting(true);
        axios.post('/feedback', { is_helpful: false, context, message })
            .then(() => {
                setShowChat(false);
                setFeedbackGiven(true);
            })
            .finally(() => setSubmitting(false));
    };

    return (
        <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-6 w-full max-w-2xl border border-gray-100 dark:border-gray-700">
            {!showChat && !feedbackGiven && (
                <div className="flex items-center justify-between">
                    <p className="text-gray-700 dark:text-gray-300 font-medium">Was this visual helpful?</p>
                    <div className="space-x-3">
                        <button onClick={() => handleFeedback(true)} className="px-5 py-2 border border-gray-300 dark:border-gray-600 rounded-full text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition">Yes</button>
                        <button onClick={() => handleFeedback(false)} className="px-5 py-2 border border-gray-300 dark:border-gray-600 rounded-full text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition">No</button>
                    </div>
                </div>
            )}

            {feedbackGiven && !showChat && (
                <p className="text-emerald-600 font-medium text-center">Thank you for your feedback!</p>
            )}

            {showChat && (
                <div className="mt-2 animate-fade-in-up">
                    <p className="font-semibold text-gray-800 dark:text-white mb-2">We're sorry it wasn't helpful. How can we improve this for you?</p>
                    <textarea 
                        value={message}
                        onChange={e => setMessage(e.target.value)}
                        className="w-full mt-2 p-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" 
                        placeholder="Tell us what went wrong or how we can help..."
                        rows={3}
                    ></textarea>
                    <div className="mt-3 flex justify-end space-x-3">
                        <button onClick={() => setShowChat(false)} className="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Cancel</button>
                        <button 
                            onClick={submitMessage} 
                            disabled={submitting}
                            className="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition disabled:opacity-50"
                        >
                            {submitting ? 'Sending...' : 'Send Message'}
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}
