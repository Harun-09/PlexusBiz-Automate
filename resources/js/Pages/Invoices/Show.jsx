import { Head } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { downloadPdf, fetchPdfObjectUrl } from '@/Utils/pdf';
import { actionButtonClasses } from '@/Utils/pillStyles';

export default function Show({ invoice }) {
    const [pdfUrl, setPdfUrl] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');

    useEffect(() => {
        let active = true;
        let objectUrl = null;

        const loadPreview = async () => {
            try {
                objectUrl = await fetchPdfObjectUrl(route('invoices.preview', invoice.id));

                if (active) {
                    setPdfUrl(objectUrl);
                }
            } catch (previewError) {
                if (active) {
                    setError(previewError.message || 'Unable to load invoice preview.');
                }
            } finally {
                if (active) {
                    setLoading(false);
                }
            }
        };

        loadPreview();

        return () => {
            active = false;

            if (objectUrl) {
                URL.revokeObjectURL(objectUrl);
            }
        };
    }, [invoice.id]);

    const handleDownload = async () => {
        await downloadPdf(route('invoices.download', invoice.id), `invoice-${invoice.invoice_number}.pdf`);
    };

    return (
        <>
            <Head title={`Invoice ${invoice.invoice_number}`} />

            <div className="min-h-screen bg-slate-100 p-4">
                <div className="flex min-h-[calc(100vh-2rem)] w-full flex-col gap-4">
                    <div className="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                        <div className="min-w-0">
                            <h1 className="truncate text-xl font-black text-slate-950">{invoice.invoice_number}</h1>
                        </div>

                        <button
                            type="button"
                            onClick={handleDownload}
                            className={`inline-flex items-center justify-center rounded-xl border px-4 py-2 text-sm font-bold transition focus:outline-none focus:ring-2 focus:ring-offset-2 ${actionButtonClasses('primary')}`}
                        >
                            Download PDF
                        </button>
                    </div>

                    <section className="flex-1 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        {loading ? (
                            <div className="flex h-full min-h-[70vh] items-center justify-center px-6 text-sm font-semibold text-slate-500">
                                Loading invoice preview...
                            </div>
                        ) : error ? (
                            <div className="flex h-full min-h-[70vh] items-center justify-center px-6">
                                <div className="max-w-md rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-center text-sm text-rose-700">
                                    {error}
                                </div>
                            </div>
                        ) : (
                            <iframe
                                title={`Invoice ${invoice.invoice_number}`}
                                src={pdfUrl}
                                className="h-[calc(100vh-9rem)] w-full border-0 bg-white"
                            />
                        )}
                    </section>
                </div>
            </div>
        </>
    );
}
