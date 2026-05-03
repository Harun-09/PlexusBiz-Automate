export default function InputError({ message, className = '', ...props }) {
    const isAuthVariant = className.includes('auth-error');

    return message ? (
        <p
            {...props}
            className={
                isAuthVariant ? `auth-error ${className}`.trim() : `text-sm text-red-600 ${className}`.trim()
            }
        >
            {message}
        </p>
    ) : null;
}
