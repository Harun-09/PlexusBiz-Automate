import { Head, Link } from '@inertiajs/react';
import FrontendLayout from '@/Layouts/FrontendLayout';

export default function B2BRegisterPlaceholder({ auth }) {
    return (
        <FrontendLayout auth={auth} canLogin={true}>
            <Head title="B2B Wholesaler Registration" />

            <div className="min-h-[70vh] flex items-center justify-center bg-slate-50 py-12 px-4 sm:px-6 lg:px-8">
                <div className="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-xl border border-slate-100 text-center">
                    <div>
                        <h2 className="mt-6 text-3xl font-extrabold text-slate-900">
                            Become a Wholesaler
                        </h2>
                        <p className="mt-2 text-sm text-slate-600">
                            This is a placeholder page for the B2B Wholesaler Registration and onboarding flow.
                        </p>
                    </div>
                    <div className="pt-6">
                        <Link
                            href="/"
                            className="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Return to Home
                        </Link>
                    </div>
                </div>
            </div>
        </FrontendLayout>
    );
}
