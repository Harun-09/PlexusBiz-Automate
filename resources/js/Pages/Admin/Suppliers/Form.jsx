import { useForm } from '@inertiajs/react';

export default function SupplierForm({ supplier = null, users = [], statuses, submitUrl, method = 'post' }) {
    const isEditing = !!supplier;

    const { data, setData, post, put, processing, errors } = useForm({
        user_id: supplier?.user_id || '',
        company_name: supplier?.company_name || '',
        contact_email: supplier?.contact_email || '',
        phone: supplier?.phone || '',
        tax_number: supplier?.tax_number || '',
        tin_number: supplier?.tin_number || '',
        bin_number: supplier?.bin_number || '',
        logo: null,
        verification_document: null,
        trade_license: null,
        corporate_certificate: null,
        status: supplier?.status || 'pending',
    });

    const submit = (e) => {
        e.preventDefault();
        if (method === 'put') {
            put(submitUrl, { preserveScroll: true, forceFormData: true });
        } else {
            post(submitUrl, { preserveScroll: true, forceFormData: true });
        }
    };

    return (
        <form onSubmit={submit} className="space-y-6">
            <div className="grid gap-6 md:grid-cols-2">
                {/* User (owner) — only on create */}
                {!isEditing ? (
                    <div className="md:col-span-2">
                        <label htmlFor="supplier-user" className="block text-xs font-bold uppercase tracking-wider text-gray-500">
                            Platform User (Owner) <span className="text-rose-500">*</span>
                        </label>
                        <select
                            id="supplier-user"
                            value={data.user_id}
                            onChange={(e) => setData('user_id', e.target.value)}
                            className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                            required
                        >
                            <option value="">Select a user…</option>
                            {users.map((u) => (
                                <option key={u.id} value={u.id}>{u.label}</option>
                            ))}
                        </select>
                        {errors.user_id && <p className="mt-1.5 text-sm text-rose-600">{errors.user_id}</p>}
                        <p className="mt-1.5 text-xs text-gray-400">Only users without an existing supplier profile are shown.</p>
                    </div>
                ) : (
                    <div className="md:col-span-2">
                        <label className="block text-xs font-bold uppercase tracking-wider text-gray-500">Owner</label>
                        <p className="mt-1.5 text-sm text-gray-700">{supplier.user_label}</p>
                    </div>
                )}

                {/* Company Name */}
                <div>
                    <label htmlFor="supplier-company" className="block text-xs font-bold uppercase tracking-wider text-gray-500">
                        Company Name <span className="text-rose-500">*</span>
                    </label>
                    <input
                        id="supplier-company"
                        type="text"
                        value={data.company_name}
                        onChange={(e) => setData('company_name', e.target.value)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        placeholder="Acme Corporation"
                        required
                    />
                    {errors.company_name && <p className="mt-1.5 text-sm text-rose-600">{errors.company_name}</p>}
                </div>

                {/* Contact Email */}
                <div>
                    <label htmlFor="supplier-email" className="block text-xs font-bold uppercase tracking-wider text-gray-500">
                        Contact Email <span className="text-rose-500">*</span>
                    </label>
                    <input
                        id="supplier-email"
                        type="email"
                        value={data.contact_email}
                        onChange={(e) => setData('contact_email', e.target.value)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        placeholder="contact@supplier.com"
                        required
                    />
                    {errors.contact_email && <p className="mt-1.5 text-sm text-rose-600">{errors.contact_email}</p>}
                </div>

                {/* Phone */}
                <div>
                    <label htmlFor="supplier-phone" className="block text-xs font-bold uppercase tracking-wider text-gray-500">Phone</label>
                    <input
                        id="supplier-phone"
                        type="text"
                        value={data.phone}
                        onChange={(e) => setData('phone', e.target.value)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        placeholder="+1 555 123 4567"
                    />
                    {errors.phone && <p className="mt-1.5 text-sm text-rose-600">{errors.phone}</p>}
                </div>

                {/* Tax Number */}
                <div>
                    <label htmlFor="supplier-tax" className="block text-xs font-bold uppercase tracking-wider text-gray-500">Tax Number</label>
                    <input
                        id="supplier-tax"
                        type="text"
                        value={data.tax_number}
                        onChange={(e) => setData('tax_number', e.target.value)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        placeholder="Tax number"
                    />
                    {errors.tax_number && <p className="mt-1.5 text-sm text-rose-600">{errors.tax_number}</p>}
                </div>

                {/* TIN Number */}
                <div>
                    <label htmlFor="supplier-tin" className="block text-xs font-bold uppercase tracking-wider text-gray-500">TIN Number</label>
                    <input
                        id="supplier-tin"
                        type="text"
                        value={data.tin_number}
                        onChange={(e) => setData('tin_number', e.target.value)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        placeholder="TIN number"
                    />
                    {errors.tin_number && <p className="mt-1.5 text-sm text-rose-600">{errors.tin_number}</p>}
                </div>

                {/* BIN Number */}
                <div>
                    <label htmlFor="supplier-bin" className="block text-xs font-bold uppercase tracking-wider text-gray-500">BIN Number</label>
                    <input
                        id="supplier-bin"
                        type="text"
                        value={data.bin_number}
                        onChange={(e) => setData('bin_number', e.target.value)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        placeholder="BIN number"
                    />
                    {errors.bin_number && <p className="mt-1.5 text-sm text-rose-600">{errors.bin_number}</p>}
                </div>

                <div>
                    <label htmlFor="supplier-logo" className="block text-xs font-bold uppercase tracking-wider text-gray-500">Company Logo</label>
                    <input
                        id="supplier-logo"
                        type="file"
                        accept="image/*"
                        onChange={(e) => setData('logo', e.target.files?.[0] ?? null)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                    />
                    {supplier?.logo_url ? <p className="mt-1.5 text-xs text-blue-700">Existing logo is already uploaded.</p> : null}
                    {errors.logo && <p className="mt-1.5 text-sm text-rose-600">{errors.logo}</p>}
                </div>

                <div>
                    <label htmlFor="supplier-document" className="block text-xs font-bold uppercase tracking-wider text-gray-500">Other Document</label>
                    <input
                        id="supplier-document"
                        type="file"
                        accept=".pdf,.jpg,.jpeg,.png"
                        onChange={(e) => setData('verification_document', e.target.files?.[0] ?? null)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                    />
                    {supplier?.verification_document_url ? <a href={supplier.verification_document_url} target="_blank" rel="noreferrer" className="mt-1.5 text-xs text-blue-700 underline">View current document</a> : null}
                    {errors.verification_document && <p className="mt-1.5 text-sm text-rose-600">{errors.verification_document}</p>}
                </div>

                <div>
                    <label htmlFor="supplier-trade" className="block text-xs font-bold uppercase tracking-wider text-gray-500">Trade License</label>
                    <input
                        id="supplier-trade"
                        type="file"
                        accept=".pdf,.jpg,.jpeg,.png"
                        onChange={(e) => setData('trade_license', e.target.files?.[0] ?? null)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                    />
                    {supplier?.trade_license_url ? <a href={supplier.trade_license_url} target="_blank" rel="noreferrer" className="mt-1.5 text-xs text-blue-700 underline">View trade license</a> : null}
                    {errors.trade_license && <p className="mt-1.5 text-sm text-rose-600">{errors.trade_license}</p>}
                </div>

                <div>
                    <label htmlFor="supplier-corporate" className="block text-xs font-bold uppercase tracking-wider text-gray-500">Corporate Certificate</label>
                    <input
                        id="supplier-corporate"
                        type="file"
                        accept=".pdf,.jpg,.jpeg,.png"
                        onChange={(e) => setData('corporate_certificate', e.target.files?.[0] ?? null)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                    />
                    {supplier?.corporate_certificate_url ? <a href={supplier.corporate_certificate_url} target="_blank" rel="noreferrer" className="mt-1.5 text-xs text-blue-700 underline">View corporate certificate</a> : null}
                    {errors.corporate_certificate && <p className="mt-1.5 text-sm text-rose-600">{errors.corporate_certificate}</p>}
                </div>

                {/* Status */}
                <div>
                    <label htmlFor="supplier-status" className="block text-xs font-bold uppercase tracking-wider text-gray-500">
                        Status <span className="text-rose-500">*</span>
                    </label>
                    <select
                        id="supplier-status"
                        value={data.status}
                        onChange={(e) => setData('status', e.target.value)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                        {statuses.map((s) => (
                            <option key={s} value={s}>{s}</option>
                        ))}
                    </select>
                    {errors.status && <p className="mt-1.5 text-sm text-rose-600">{errors.status}</p>}
                    {data.status === 'approved' && !isEditing && (
                        <p className="mt-1.5 text-xs text-emerald-600">Setting to approved will immediately grant the user supplier access.</p>
                    )}
                </div>
            </div>

            <div className="flex flex-col gap-3 border-t border-gray-100 pt-6 sm:flex-row sm:items-center">
                <button
                    type="submit"
                    disabled={processing}
                    className="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-slate-900 to-blue-700 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-700/20 transition hover:shadow-blue-700/30 hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto"
                >
                    {processing ? (
                        <svg className="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" /><path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
                    ) : null}
                    {isEditing ? 'Update Supplier' : 'Create Supplier'}
                </button>
                <a href="/admin/suppliers" className="inline-flex w-full items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-600 shadow-sm transition hover:bg-gray-50 sm:w-auto">
                    Cancel
                </a>
            </div>
        </form>
    );
}
