import { Head, Link } from '@inertiajs/react';

function formatMoney(amount, currency = 'BDT') {
    const numericAmount = Number(amount ?? 0);

    try {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: currency || 'BDT',
            maximumFractionDigits: 2,
        }).format(numericAmount);
    } catch {
        return `${currency || 'BDT'} ${numericAmount.toFixed(2)}`;
    }
}

export default function CheckoutSuccess({
    auth,
    flash,
    orderNumber,
    paymentMethod,
    paymentStatus,
    transactionId,
    currency,
    grandTotal,
}) {
    const isPaid = ['completed', 'paid'].includes((paymentStatus ?? '').toString().toLowerCase());
    const message = flash?.success || flash?.error;
    const title = isPaid ? 'Payment confirmed' : 'Checkout received';
    const toneClass = isPaid
        ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
        : 'border-amber-200 bg-amber-50 text-amber-800';

    return (
        <>
            <Head title={`Checkout ${orderNumber}`} />

            <div className="min-h-screen bg-[radial-gradient(circle_at_top,_#dbeafe_0,_#eff6ff_38%,_#ffffff_100%)] text-slate-900">
                <div className="flex min-h-screen w-full items-center px-4 py-10 sm:px-6">
                    <div className="grid w-full gap-6 rounded-[2rem] border border-white/70 bg-white/90 p-6 shadow-[0_24px_80px_rgba(15,23,42,0.12)] backdrop-blur md:p-8 lg:grid-cols-[1.2fr_0.8fr]">
                        <section className="space-y-6">
                            <div className="inline-flex items-center gap-3 rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700">
                                <span className={`flex h-9 w-9 items-center justify-center rounded-full ${isPaid ? 'bg-emerald-600' : 'bg-amber-500'} text-white`}>
                                    {isPaid ? '✓' : '•'}
                                </span>
                                PlexusBiz checkout
                            </div>

                            <div className="space-y-3">
                                <p className="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">
                                    {isPaid ? 'Successful payment' : 'Payment status update'}
                                </p>
                                <h1 className="max-w-xl text-4xl font-black tracking-tight text-slate-950 md:text-5xl">
                                    {title} for order <span className="text-blue-700">{orderNumber}</span>.
                                </h1>
                                <p className="max-w-2xl text-base leading-7 text-slate-600 md:text-lg">
                                    {isPaid
                                        ? 'Your payment gateway returned a successful confirmation. The order is ready for the next step in the workflow.'
                                        : 'We received your checkout request and are waiting for the gateway confirmation to finalize the order.'}
                                </p>
                            </div>

                            {message && (
                                <div className={`rounded-2xl border px-4 py-3 text-sm font-medium ${toneClass}`}>
                                    {message}
                                </div>
                            )}

                            <div className="rounded-2xl border border-blue-200 bg-blue-50 px-4 py-4 text-sm leading-6 text-blue-900">
                                This order has already been routed to supplier fulfillment automatically. You do not need to
                                contact the supplier manually. The buyer sees the confirmed order here, while the supplier
                                sees a pending fulfillment row in the supplier queue and ships the product after confirming it.
                            </div>

                            <div className="flex flex-wrap gap-3">
                                {auth?.user ? (
                                    <Link
                                        href={route('dashboard')}
                                        className="inline-flex items-center justify-center rounded-full bg-blue-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-800"
                                    >
                                        Go to dashboard
                                    </Link>
                                ) : null}

                                <Link
                                    href="/"
                                    className="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50"
                                >
                                    Back to home
                                </Link>
                            </div>
                        </section>

                        <aside className="rounded-[1.5rem] bg-slate-950 p-5 text-white shadow-lg">
                            <p className="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">
                                Payment summary
                            </p>

                            <div className="mt-5 space-y-4">
                                <div className="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <p className="text-sm text-slate-400">Amount</p>
                                    <p className="mt-1 text-3xl font-black tracking-tight">
                                        {formatMoney(grandTotal, currency)}
                                    </p>
                                </div>

                                <dl className="grid gap-3 text-sm">
                                    <div className="flex items-center justify-between gap-4 rounded-2xl bg-white/5 px-4 py-3">
                                        <dt className="text-slate-400">Payment method</dt>
                                        <dd className="font-semibold capitalize text-white">
                                            {paymentMethod || 'Pending'}
                                        </dd>
                                    </div>
                                    <div className="flex items-center justify-between gap-4 rounded-2xl bg-white/5 px-4 py-3">
                                        <dt className="text-slate-400">Status</dt>
                                        <dd className="font-semibold capitalize text-white">
                                            {paymentStatus || 'pending'}
                                        </dd>
                                    </div>
                                    <div className="flex items-center justify-between gap-4 rounded-2xl bg-white/5 px-4 py-3">
                                        <dt className="text-slate-400">Transaction</dt>
                                        <dd className="font-semibold text-white">
                                            {transactionId || 'Waiting'}
                                        </dd>
                                    </div>
                                </dl>

                                <div className="rounded-2xl border border-sky-400/20 bg-sky-400/10 p-4 text-sm leading-6 text-sky-100">
                                    Keep this page open until the gateway redirect finishes. Your order number and payment
                                    state are synced with the backend.
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </>
    );
}
