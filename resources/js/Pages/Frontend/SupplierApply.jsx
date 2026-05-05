import FrontendLayout from '@/Layouts/FrontendLayout';
import { Head, Link, useForm } from '@inertiajs/react';

const highlights = [
    {
        title: 'Pending approval',
        copy: 'Applications go to the admin queue before supplier access is activated.',
    },
    {
        title: 'Bulk-first setup',
        copy: 'Your account is built around B2B product listings, MOQ, and stock control.',
    },
    {
        title: 'Optional verification',
        copy: 'Tax and business details are stored now so admin review can happen later.',
    },
];

export default function SupplierApply({ countries = [] }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        company_name: '',
        email: '',
        phone: '',
        tax_number: '',
        address_line1: '',
        address_line2: '',
        city: '',
        country: 'Bangladesh',
        password: '',
        password_confirmation: '',
    });

    const submit = (event) => {
        event.preventDefault();
        post(route('supplier.apply.store'), {
            preserveScroll: true,
            onSuccess: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <FrontendLayout>
            <Head title="Supplier Onboarding" />

            <section className="bg-[#f4f7fc] py-16">
                <div className="grid w-full gap-8 px-4 sm:px-6 lg:grid-cols-[minmax(0,1.08fr)_minmax(0,0.92fr)] lg:px-8">
                    <div className="rounded-[32px] border border-[#d8e3f6] bg-gradient-to-r from-[#4f7fe0] via-[#3f70d4] to-[#2953b1] p-8 text-white shadow-[0_30px_100px_rgba(10,32,88,0.24)]">
                        <span className="inline-flex rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[11px] font-black uppercase tracking-[0.24em] text-[#ffd59a]">
                            Supplier onboarding
                        </span>
                        <h1 className="mt-4 max-w-xl text-4xl font-black tracking-tight sm:text-5xl">
                            Apply as a supplier and start selling in the B2B marketplace.
                        </h1>
                        <p className="mt-4 max-w-2xl text-base leading-7 text-blue-100/90">
                            Each approved supplier gets a dedicated dashboard, product management access, inventory control, and order visibility for wholesale transactions.
                        </p>

                        <div className="mt-8 grid gap-3 sm:grid-cols-2">
                            {highlights.map((item) => (
                                <div key={item.title} className="rounded-2xl border border-white/12 bg-white/10 p-4 backdrop-blur">
                                    <p className="text-sm font-black uppercase tracking-[0.18em] text-[#ffd59a]">{item.title}</p>
                                    <p className="mt-2 text-sm leading-6 text-blue-50/90">{item.copy}</p>
                                </div>
                            ))}
                        </div>

                        <div className="mt-8 rounded-3xl border border-white/12 bg-white/10 p-5">
                            <p className="text-sm font-semibold text-blue-50/90">What happens next</p>
                            <ol className="mt-3 space-y-2 text-sm leading-6 text-blue-100/90">
                                <li>1. Submit the application form.</li>
                                <li>2. Admin reviews the supplier profile.</li>
                                <li>3. Approved suppliers gain access to product and order tools.</li>
                            </ol>
                        </div>
                    </div>

                    <div className="rounded-[32px] border border-[#d8e3f6] bg-white p-6 shadow-[0_20px_70px_rgba(15,23,42,0.08)] sm:p-8">
                        <div>
                            <h2 className="text-2xl font-black tracking-tight text-slate-950">Supplier application</h2>
                            <p className="mt-2 text-sm leading-6 text-slate-600">
                                Fill in your contact details and business profile. KYC fields are optional but recommended.
                            </p>
                        </div>

                        <form onSubmit={submit} className="mt-8 space-y-5">
                            <div className="grid gap-5 sm:grid-cols-2">
                                <Field label="Your Name" error={errors.name}>
                                    <input
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        className="input"
                                        placeholder="Ayesha Rahman"
                                        required
                                    />
                                </Field>

                                <Field label="Company Name" error={errors.company_name}>
                                    <input
                                        value={data.company_name}
                                        onChange={(e) => setData('company_name', e.target.value)}
                                        className="input"
                                        placeholder="Plexus Industrial Supply"
                                        required
                                    />
                                </Field>
                            </div>

                            <div className="grid gap-5 sm:grid-cols-2">
                                <Field label="Email" error={errors.email}>
                                    <input
                                        type="email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        className="input"
                                        placeholder="supplier@company.com"
                                        required
                                    />
                                </Field>

                                <Field label="Phone" error={errors.phone}>
                                    <input
                                        value={data.phone}
                                        onChange={(e) => setData('phone', e.target.value)}
                                        className="input"
                                        placeholder="+880 1XXXXXXXXX"
                                    />
                                </Field>
                            </div>

                            <div className="grid gap-5 sm:grid-cols-2">
                                <Field label="Tax / VAT Number" error={errors.tax_number}>
                                    <input
                                        value={data.tax_number}
                                        onChange={(e) => setData('tax_number', e.target.value)}
                                        className="input"
                                        placeholder="Optional"
                                    />
                                </Field>

                                <Field label="Country" error={errors.country}>
                                    <select
                                        value={data.country}
                                        onChange={(e) => setData('country', e.target.value)}
                                        className="input"
                                    >
                                        {countries.map((country) => (
                                            <option key={country} value={country}>{country}</option>
                                        ))}
                                    </select>
                                </Field>
                            </div>

                            <div className="grid gap-5 sm:grid-cols-2">
                                <Field label="Address Line 1" error={errors.address_line1}>
                                    <input
                                        value={data.address_line1}
                                        onChange={(e) => setData('address_line1', e.target.value)}
                                        className="input"
                                        placeholder="House, street, road"
                                    />
                                </Field>

                                <Field label="Address Line 2" error={errors.address_line2}>
                                    <input
                                        value={data.address_line2}
                                        onChange={(e) => setData('address_line2', e.target.value)}
                                        className="input"
                                        placeholder="District, area, landmark"
                                    />
                                </Field>
                            </div>

                            <div className="grid gap-5 sm:grid-cols-2">
                                <Field label="City" error={errors.city}>
                                    <input
                                        value={data.city}
                                        onChange={(e) => setData('city', e.target.value)}
                                        className="input"
                                        placeholder="Dhaka"
                                    />
                                </Field>

                                <Field label="Password" error={errors.password}>
                                    <input
                                        type="password"
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        className="input"
                                        placeholder="Create a password"
                                        required
                                    />
                                </Field>
                            </div>

                            <Field label="Confirm Password" error={errors.password_confirmation}>
                                <input
                                    type="password"
                                    value={data.password_confirmation}
                                    onChange={(e) => setData('password_confirmation', e.target.value)}
                                    className="input"
                                    placeholder="Repeat the password"
                                    required
                                />
                            </Field>

                            <div className="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm leading-6 text-slate-600">
                                Your application will be reviewed by an admin before supplier tools are enabled.
                            </div>

                            <div className="flex flex-col gap-3 sm:flex-row">
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-slate-950 to-blue-700 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-700/20 transition hover:-translate-y-0.5 hover:shadow-blue-700/30 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    {processing ? 'Submitting...' : 'Submit application'}
                                </button>
                                <Link
                                    href="/login"
                                    className="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50"
                                >
                                    Already have an account?
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </FrontendLayout>
    );
}

function Field({ label, error, children }) {
    return (
        <label className="block">
            <span className="mb-2 block text-xs font-black uppercase tracking-[0.18em] text-slate-500">
                {label}
            </span>
            {children}
            {error ? <span className="mt-1.5 block text-sm text-rose-600">{error}</span> : null}
        </label>
    );
}
