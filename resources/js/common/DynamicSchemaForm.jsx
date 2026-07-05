import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
// import { client } from 'laravel-precognition-react'; // Can be added later when Precognition is fully installed

/**
 * A foundational dynamic schema form component utilizing Laravel Precognition concepts.
 * Receives a JSON schema from the backend and dynamically renders fields.
 * 
 * @param {Object} props
 * @param {Array} props.schema Array of field definitions { name, label, type, required }
 * @param {String} props.submitUrl URL to POST the form to
 */
export default function DynamicSchemaForm({ schema = [], submitUrl }) {
    // Initialize form data based on schema
    const initialData = schema.reduce((acc, field) => {
        acc[field.name] = field.defaultValue || '';
        return acc;
    }, {});

    const { data, setData, post, processing, errors, clearErrors } = useForm(initialData);

    const handleSubmit = (e) => {
        e.preventDefault();
        // Here we would typically use precognition to validate before full submission
        post(submitUrl, {
            preserveScroll: true,
            onSuccess: () => {
                console.log("Form successfully submitted.");
            },
        });
    };

    const handleChange = (e, fieldName) => {
        setData(fieldName, e.target.value);
        clearErrors(fieldName);
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-6">
            {schema.map((field) => (
                <div key={field.name} className="flex flex-col">
                    <label htmlFor={field.name} className="block text-sm font-medium text-slate-700">
                        {field.label} {field.required && <span className="text-red-500">*</span>}
                    </label>
                    
                    {field.type === 'textarea' ? (
                        <textarea
                            id={field.name}
                            value={data[field.name]}
                            onChange={(e) => handleChange(e, field.name)}
                            required={field.required}
                            className="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        />
                    ) : (
                        <input
                            type={field.type || 'text'}
                            id={field.name}
                            value={data[field.name]}
                            onChange={(e) => handleChange(e, field.name)}
                            required={field.required}
                            className="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        />
                    )}

                    {errors[field.name] && (
                        <p className="mt-2 text-sm text-red-600">{errors[field.name]}</p>
                    )}
                </div>
            ))}

            <button
                type="submit"
                disabled={processing}
                className="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
            >
                {processing ? 'Submitting...' : 'Submit'}
            </button>
        </form>
    );
}
