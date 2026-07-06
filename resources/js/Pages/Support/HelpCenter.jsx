import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import PageHeader from '@/Components/PageHeader';
import FlashBanner from '@/Components/FlashBanner';
import { Head, Link, useForm } from '@inertiajs/react';
import { useState, useRef, useEffect } from 'react';
import axios from 'axios';

export default function HelpCenter({ auth, faqs = [], recentTickets = [], recentOrders = [] }) {
    // Chat states
    const [messages, setMessages] = useState([
        {
            id: 1,
            sender: 'bot',
            text: `Hello ${auth.user.name}! I am your AI Support Assistant. How can I help you today? You can ask me questions about your orders, shipping, refunds, or type your query.`,
            timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        }
    ]);
    const [input, setInput] = useState('');
    const [isTyping, setIsTyping] = useState(false);
    const messagesEndRef = useRef(null);

    // FAQ Search state
    const [faqSearch, setFaqSearch] = useState('');
    const [activeFaqId, setActiveFaqId] = useState(null);

    // Escalation State
    const [showEmailForm, setShowEmailForm] = useState(false);
    const [emailForm, setEmailForm] = useState({
        subject: '',
        description: '',
        order_id: '',
    });
    const [emailSubmitting, setEmailSubmitting] = useState(false);
    const [emailStatus, setEmailStatus] = useState(null);

    // Scroll chat to bottom
    const scrollToBottom = () => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    };

    useEffect(() => {
        scrollToBottom();
    }, [messages, isTyping]);

    // Send chat message
    const handleSendMessage = async (e) => {
        e.preventDefault();
        if (!input.trim()) return;

        const userMsgText = input;
        setInput('');

        // Add user message
        const userMsg = {
            id: Date.now(),
            sender: 'user',
            text: userMsgText,
            timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        };
        setMessages((prev) => [...prev, userMsg]);
        setIsTyping(true);

        try {
            const response = await axios.post(route('support.help-center.message'), {
                message: userMsgText
            });

            setIsTyping(false);
            const data = response.data;

            const botMsg = {
                id: Date.now() + 1,
                sender: 'bot',
                text: data.answer,
                escalate: data.escalate,
                timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
            };

            setMessages((prev) => [...prev, botMsg]);
        } catch (error) {
            setIsTyping(false);
            setMessages((prev) => [
                ...prev,
                {
                    id: Date.now() + 1,
                    sender: 'bot',
                    text: "I'm sorry, I encountered an error. Please try again or escalate to support.",
                    escalate: true,
                    timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                }
            ]);
        }
    };

    // Create Support Ticket (Talk to Admin)
    const handleCreateTicketEscalation = (msgText) => {
        // We will pre-fill a ticket creation form and submit it
        const userQuery = messages.filter(m => m.sender === 'user').pop()?.text || msgText;
        
        // Using Inertia useForm to post to tickets.store
        const form = useForm({
            subject: `AI Escalation: Support Request`,
            description: `Escalated query from AI Help Center: "${userQuery}"`,
            priority: 'normal',
            supplier_id: null,
            order_id: null,
        });

        form.post(route('support.tickets.store'));
    };

    // Handle Email Escalation Submit
    const handleEmailSubmit = async (e) => {
        e.preventDefault();
        if (!emailForm.subject.trim() || !emailForm.description.trim()) {
            setEmailStatus({ type: 'error', message: 'Subject and Description are required.' });
            return;
        }

        setEmailSubmitting(true);
        setEmailStatus(null);

        try {
            const response = await axios.post(route('support.help-center.email-escalate'), emailForm);
            setEmailSubmitting(false);
            setEmailStatus({ type: 'success', message: response.data.message });
            
            // Append message to chatbot history indicating email was sent
            setMessages((prev) => [
                ...prev,
                {
                    id: Date.now(),
                    sender: 'bot',
                    text: `✅ Email sent successfully! Subject: "${emailForm.subject}". Our support team will get back to you shortly.`,
                    timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                }
            ]);

            // Reset form and close
            setEmailForm({ subject: '', description: '', order_id: '' });
            setTimeout(() => {
                setShowEmailForm(false);
                setEmailStatus(null);
            }, 3000);

        } catch (error) {
            setEmailSubmitting(false);
            setEmailStatus({ type: 'error', message: 'Failed to send email. Please try again later.' });
        }
    };

    // Filter FAQs
    const filteredFaqs = faqs.filter(faq => {
        const query = faqSearch.toLowerCase();
        const questionMatch = faq.question.toLowerCase().includes(query);
        const answerMatch = faq.answer.toLowerCase().includes(query);
        const keywordsMatch = Array.isArray(faq.keywords_json) && faq.keywords_json.some(k => String(k).toLowerCase().includes(query));
        return questionMatch || answerMatch || keywordsMatch;
    });

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <PageHeader
                    eyebrow="Help & Support"
                    title="AI Help Center"
                    description="Get answers instantly from our AI Assistant or escalate to our support team via live ticket or email."
                />
            }
        >
            <Head title="AI Help Center" />

            <div className="grid gap-6 lg:grid-cols-12">
                {/* Left Panel: Chatbot & Email Escalation */}
                <div className="lg:col-span-8 flex flex-col gap-6">
                    {/* Chatbot Card */}
                    <div className="flex flex-col h-[600px] rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        {/* Chat Header */}
                        <div className="flex items-center justify-between bg-slate-900 px-6 py-4 text-white">
                            <div className="flex items-center gap-3">
                                <div className="relative">
                                    <div className="h-10 w-10 rounded-full bg-amber-500 flex items-center justify-center text-slate-950 font-black text-xl">
                                        🤖
                                    </div>
                                    <span className="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-emerald-500 border-2 border-slate-900"></span>
                                </div>
                                <div>
                                    <h3 className="font-bold text-sm">AI Support Assistant</h3>
                                    <p className="text-xs text-slate-400">Always active & ready to help</p>
                                </div>
                            </div>
                            <div className="flex gap-2">
                                <button 
                                    onClick={() => {
                                        setMessages([{
                                            id: 1,
                                            sender: 'bot',
                                            text: `Hello ${auth.user.name}! I am your AI Support Assistant. How can I help you today? You can ask me questions about your orders, shipping, refunds, or type your query.`,
                                            timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                                        }]);
                                        setShowEmailForm(false);
                                    }}
                                    className="p-2 text-slate-400 hover:text-white transition rounded-lg hover:bg-white/10"
                                    title="Reset Conversation"
                                >
                                    🔄
                                </button>
                            </div>
                        </div>

                        {/* Chat Messages */}
                        <div className="flex-1 overflow-y-auto p-6 space-y-4 bg-slate-50">
                            {messages.map((message) => (
                                <div
                                    key={message.id}
                                    className={`flex ${message.sender === 'user' ? 'justify-end' : 'justify-start'}`}
                                >
                                    <div className={`max-w-[75%] rounded-2xl p-4 shadow-sm ${
                                        message.sender === 'user'
                                            ? 'bg-slate-950 text-white rounded-tr-none'
                                            : 'bg-white text-slate-800 border border-slate-100 rounded-tl-none'
                                    }`}>
                                        <p className="text-sm leading-relaxed whitespace-pre-wrap">{message.text}</p>
                                        <span className={`block text-[9px] mt-2 text-right ${
                                            message.sender === 'user' ? 'text-slate-400' : 'text-slate-500'
                                        }`}>
                                            {message.timestamp}
                                        </span>

                                        {/* Escalation Options */}
                                        {message.escalate && (
                                            <div className="mt-4 pt-4 border-t border-slate-100 flex flex-col sm:flex-row gap-2">
                                                <button
                                                    onClick={() => handleCreateTicketEscalation(message.text)}
                                                    className="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-xs font-bold text-slate-950 hover:bg-amber-400 transition"
                                                >
                                                    👤 Talk to Admin (Create Ticket)
                                                </button>
                                                <button
                                                    onClick={() => setShowEmailForm(true)}
                                                    className="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-200 transition"
                                                >
                                                    ✉️ Contact via Email
                                                </button>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            ))}
                            {isTyping && (
                                <div className="flex justify-start">
                                    <div className="bg-white border border-slate-100 rounded-2xl rounded-tl-none p-4 shadow-sm flex items-center gap-1.5">
                                        <span className="h-2 w-2 rounded-full bg-slate-400 animate-bounce"></span>
                                        <span className="h-2 w-2 rounded-full bg-slate-400 animate-bounce [animation-delay:0.2s]"></span>
                                        <span className="h-2 w-2 rounded-full bg-slate-400 animate-bounce [animation-delay:0.4s]"></span>
                                    </div>
                                </div>
                            )}
                            <div ref={messagesEndRef} />
                        </div>

                        {/* Chat Input */}
                        <form onSubmit={handleSendMessage} className="border-t border-slate-200 p-4 bg-white flex gap-2">
                            <input
                                value={input}
                                onChange={(e) => setInput(e.target.value)}
                                className="flex-1 rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                                placeholder="Type your support request..."
                                disabled={isTyping || showEmailForm}
                            />
                            <button
                                type="submit"
                                className="inline-flex items-center justify-center h-11 w-11 rounded-xl bg-slate-950 text-white hover:bg-slate-800 transition disabled:opacity-50"
                                disabled={!input.trim() || isTyping || showEmailForm}
                            >
                                ➔
                            </button>
                        </form>
                    </div>

                    {/* Email Escalation Form Card */}
                    {showEmailForm && (
                        <div className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm animate-fadeIn">
                            <div className="flex justify-between items-center border-b border-slate-100 pb-4 mb-4">
                                <div>
                                    <h3 className="text-lg font-black text-slate-950">✉️ Email Support Desk</h3>
                                    <p className="text-xs text-slate-500">Send an escalation email to our support team.</p>
                                </div>
                                <button
                                    onClick={() => setShowEmailForm(false)}
                                    className="text-slate-400 hover:text-slate-700 transition"
                                >
                                    ✕ Close
                                </button>
                            </div>

                            {emailStatus && (
                                <div className={`mb-4 rounded-xl p-3 text-sm font-semibold ${
                                    emailStatus.type === 'success' ? 'bg-emerald-50 text-emerald-800' : 'bg-rose-50 text-rose-800'
                                }`}>
                                    {emailStatus.message}
                                </div>
                            )}

                            <form onSubmit={handleEmailSubmit} className="space-y-4">
                                <div>
                                    <label className="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Subject</label>
                                    <input
                                        value={emailForm.subject}
                                        onChange={(e) => setEmailForm({ ...emailForm, subject: e.target.value })}
                                        className="input"
                                        placeholder="Briefly state the issue"
                                        required
                                    />
                                </div>

                                <div>
                                    <label className="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Associated Order (Optional)</label>
                                    <select
                                        value={emailForm.order_id}
                                        onChange={(e) => setEmailForm({ ...emailForm, order_id: e.target.value })}
                                        className="input"
                                    >
                                        <option value="">No order selected</option>
                                        {recentOrders.map(order => (
                                            <option key={order.id} value={order.id}>
                                                #{order.order_number} ({order.status}) - {order.total_amount}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                <div>
                                    <label className="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Detailed Description</label>
                                    <textarea
                                        value={emailForm.description}
                                        onChange={(e) => setEmailForm({ ...emailForm, description: e.target.value })}
                                        className="input min-h-[140px] resize-y"
                                        placeholder="Describe your issue or request in detail so we can assist you..."
                                        required
                                    />
                                </div>

                                <div className="flex gap-2">
                                    <button
                                        type="submit"
                                        disabled={emailSubmitting}
                                        className="inline-flex items-center justify-center rounded-xl bg-slate-950 px-5 py-3 text-xs font-bold text-white transition hover:bg-slate-800 disabled:opacity-50"
                                    >
                                        {emailSubmitting ? 'Sending...' : 'Send Escalation Email'}
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => setShowEmailForm(false)}
                                        className="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-xs font-bold text-slate-700 transition hover:bg-slate-50"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    )}
                </div>

                {/* Right Panel: FAQs & Recent Tickets */}
                <div className="lg:col-span-4 space-y-6">
                    {/* Recent Support Tickets */}
                    <div className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div className="flex justify-between items-center mb-4">
                            <h3 className="text-base font-extrabold text-slate-950">🎫 Recent Tickets</h3>
                            <Link href={route('support.tickets.index')} className="text-xs text-indigo-600 hover:text-indigo-800 font-bold transition">
                                View All ➔
                            </Link>
                        </div>
                        {recentTickets.length === 0 ? (
                            <div className="text-sm text-slate-500 bg-slate-50 rounded-2xl p-4 border border-dashed border-slate-200">
                                You have no open tickets. You can chat with our AI to create one.
                            </div>
                        ) : (
                            <div className="space-y-3">
                                {recentTickets.map(ticket => (
                                    <div key={ticket.id} className="rounded-2xl border border-slate-100 p-4 hover:border-indigo-100 transition bg-slate-50/50">
                                        <div className="flex justify-between items-start mb-2">
                                            <span className="text-xs font-black text-slate-500">{ticket.ticket_number}</span>
                                            <span className={`inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold capitalize ${
                                                ['resolved', 'closed'].includes(ticket.status) ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'
                                            }`}>
                                                {ticket.status.replace('_', ' ')}
                                            </span>
                                        </div>
                                        <h4 className="text-sm font-extrabold text-slate-950 mb-2 truncate">{ticket.subject}</h4>
                                        <div className="flex justify-between items-center pt-2 border-t border-slate-100">
                                            <span className="text-[10px] text-slate-500">{ticket.created_at}</span>
                                            <Link
                                                href={route('support.tickets.show', ticket.id)}
                                                className="inline-flex items-center justify-center rounded-lg bg-slate-950 px-3 py-1.5 text-[10px] font-bold text-white hover:bg-slate-800 transition"
                                            >
                                                Open Chat
                                            </Link>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* FAQ Knowledge Base Search */}
                    <div className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col max-h-[500px]">
                        <h3 className="text-base font-extrabold text-slate-950 mb-3">📚 Knowledge Base FAQs</h3>
                        <div className="relative mb-4">
                            <input
                                type="search"
                                value={faqSearch}
                                onChange={(e) => setFaqSearch(e.target.value)}
                                className="w-full h-10 pl-8 pr-4 rounded-xl border border-slate-200 text-xs focus:border-slate-900 focus:outline-none"
                                placeholder="Search FAQs..."
                            />
                            <span className="absolute left-2.5 top-3 text-slate-400 text-xs">🔍</span>
                        </div>

                        <div className="flex-1 overflow-y-auto space-y-2 pr-1">
                            {filteredFaqs.length === 0 ? (
                                <div className="text-xs text-slate-500 py-6 text-center">
                                    No matching FAQs found.
                                </div>
                            ) : (
                                filteredFaqs.map(faq => {
                                    const isOpen = activeFaqId === faq.id;
                                    return (
                                        <div key={faq.id} className="rounded-xl border border-slate-100 overflow-hidden bg-slate-50/50">
                                            <button
                                                onClick={() => setActiveFaqId(isOpen ? null : faq.id)}
                                                className="w-full text-left px-4 py-3 flex justify-between items-center gap-2"
                                            >
                                                <span className="text-xs font-extrabold text-slate-950">{faq.question}</span>
                                                <span className="text-slate-400 text-xs">{isOpen ? '▼' : '▶'}</span>
                                            </button>
                                            {isOpen && (
                                                <div className="px-4 pb-4 pt-1 border-t border-slate-100 text-xs leading-relaxed text-slate-700 bg-white whitespace-pre-wrap">
                                                    {faq.answer}
                                                </div>
                                            )}
                                        </div>
                                    );
                                })
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
