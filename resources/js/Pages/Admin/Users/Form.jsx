import { useForm } from '@inertiajs/react';

export default function UserForm({ user = null, roles, statuses, submitUrl, method = 'post' }) {
    const isEditing = !!user;

    const { data, setData, post, put, processing, errors } = useForm({
        name: user?.name || '',
        email: user?.email || '',
        password: '',
        role: user?.role || (roles.length > 0 ? roles[0] : ''),
        status: user?.status || 'active',
    });

    const submit = (e) => {
        e.preventDefault();
        if (method === 'put') {
            put(submitUrl, { preserveScroll: true });
        } else {
            post(submitUrl, { preserveScroll: true });
        }
    };

    return (
        <form onSubmit={submit} className="space-y-6">
            <div className="grid gap-6 md:grid-cols-2">
                {/* Name */}
                <div>
                    <label htmlFor="user-name" className="block text-xs font-bold uppercase tracking-wider text-gray-500">
                        Name <span className="text-rose-500">*</span>
                    </label>
                    <input
                        id="user-name"
                        type="text"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        placeholder="Full name"
                        required
                    />
                    {errors.name && <p className="mt-1.5 text-sm text-rose-600">{errors.name}</p>}
                </div>

                {/* Email */}
                <div>
                    <label htmlFor="user-email" className="block text-xs font-bold uppercase tracking-wider text-gray-500">
                        Email <span className="text-rose-500">*</span>
                    </label>
                    <input
                        id="user-email"
                        type="email"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        placeholder="email@example.com"
                        required
                    />
                    {errors.email && <p className="mt-1.5 text-sm text-rose-600">{errors.email}</p>}
                </div>

                {/* Password */}
                <div>
                    <label htmlFor="user-password" className="block text-xs font-bold uppercase tracking-wider text-gray-500">
                        Password {!isEditing && <span className="text-rose-500">*</span>}
                    </label>
                    <input
                        id="user-password"
                        type="password"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        placeholder={isEditing ? 'Leave blank to keep current' : 'Minimum 8 characters'}
                        required={!isEditing}
                    />
                    {errors.password && <p className="mt-1.5 text-sm text-rose-600">{errors.password}</p>}
                </div>

                {/* Role */}
                <div>
                    <label htmlFor="user-role" className="block text-xs font-bold uppercase tracking-wider text-gray-500">
                        Role <span className="text-rose-500">*</span>
                    </label>
                    <select
                        id="user-role"
                        value={data.role}
                        onChange={(e) => setData('role', e.target.value)}
                        className="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                        {roles.map((r) => (
                            <option key={r} value={r}>{r.replace('_', ' ')}</option>
                        ))}
                    </select>
                    {errors.role && <p className="mt-1.5 text-sm text-rose-600">{errors.role}</p>}
                </div>

                {/* Status */}
                <div>
                    <label htmlFor="user-status" className="block text-xs font-bold uppercase tracking-wider text-gray-500">
                        Status <span className="text-rose-500">*</span>
                    </label>
                    <select
                        id="user-status"
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
                    {isEditing ? 'Update User' : 'Create User'}
                </button>
                <a href="/admin/users" className="inline-flex w-full items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-600 shadow-sm transition hover:bg-gray-50 sm:w-auto">
                    Cancel
                </a>
            </div>
        </form>
    );
}
