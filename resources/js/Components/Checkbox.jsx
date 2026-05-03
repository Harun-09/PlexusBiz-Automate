export default function Checkbox({ className = '', ...props }) {
    const isAuthVariant = className.includes('auth-checkbox');

    return (
        <input
            {...props}
            type="checkbox"
            className={
                isAuthVariant
                    ? `auth-checkbox ${className}`.trim()
                    : 'rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 ' + className
            }
        />
    );
}
