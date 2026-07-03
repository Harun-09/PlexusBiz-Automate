import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function TrashIndex({ auth, items, type }) {
    const { post, processing } = useForm();

    const handleRestore = (id, e) => {
        e.preventDefault();
        if (confirm('Are you sure you want to restore this item?')) {
            post(route('admin.trash.restore', { type, id }));
        }
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Trash (Recycle Bin)</h2>}
        >
            <Head title="Trash" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div className="flex space-x-4 mb-6">
                            <Link href={route('admin.trash.index', { type: 'products' })} className={`px-4 py-2 rounded-md ${type === 'products' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'}`}>Products</Link>
                            <Link href={route('admin.trash.index', { type: 'orders' })} className={`px-4 py-2 rounded-md ${type === 'orders' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'}`}>Orders</Link>
                            <Link href={route('admin.trash.index', { type: 'users' })} className={`px-4 py-2 rounded-md ${type === 'users' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'}`}>Users</Link>
                        </div>

                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name / Identifier</th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deleted At</th>
                                        <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                    {items.data.length > 0 ? (
                                        items.data.map((item) => (
                                            <tr key={item.id}>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{item.id}</td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {type === 'products' && (item.name)}
                                                    {type === 'orders' && (item.order_number)}
                                                    {type === 'users' && (item.name + ' (' + item.email + ')')}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {new Date(item.deleted_at).toLocaleString()}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <button 
                                                        onClick={(e) => handleRestore(item.id, e)}
                                                        disabled={processing}
                                                        className="text-blue-600 hover:text-blue-900 font-semibold mr-3"
                                                    >
                                                        Restore
                                                    </button>
                                                    <button 
                                                        onClick={(e) => {
                                                            e.preventDefault();
                                                            if (confirm('Are you sure you want to PERMANENTLY delete this item?')) {
                                                                post(route(`admin.trash.force-delete`, { type, id: item.id }));
                                                            }
                                                        }}
                                                        disabled={processing}
                                                        className="text-red-600 hover:text-red-900 font-semibold"
                                                    >
                                                        Delete Permanently
                                                    </button>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="4" className="px-6 py-4 text-center text-sm text-gray-500">No items found in {type} trash.</td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                        {/* Pagination placeholder */}
                        <div className="mt-4">
                            <span className="text-sm text-gray-500">Showing {items.data.length} of {items.total} items</span>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
