export default function InputLabel({ value, className = '', children, ...props }) {
    const isAuthVariant = className.includes('auth-label');

    return (
        <label
            {...props}
            className={
                isAuthVariant
                    ? `auth-label ${className}`.trim()
                    : `block font-medium text-sm text-gray-700 ${className}`.trim()
            }
        >
            {value ? value : children}
        </label>
    );
}
