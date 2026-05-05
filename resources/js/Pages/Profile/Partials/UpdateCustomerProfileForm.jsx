import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Transition } from '@headlessui/react';
import { useForm, usePage } from '@inertiajs/react';

const countryOptions = ['Bangladesh', 'India', 'Singapore', 'Malaysia', 'United Arab Emirates'];

function formatDate(value) {
    if (!value) {
        return 'n/a';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return 'n/a';
    }

    return new Intl.DateTimeFormat('en-GB', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
}

function SummaryPill({ label, value }) {
    const displayValue = value === null || value === undefined || value === '' ? 'n/a' : value;

    return (
        <div className="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
            <p className="text-[11px] font-black uppercase tracking-[0.18em] text-slate-400">{label}</p>
            <p className="mt-1 text-sm font-bold text-slate-950">{displayValue}</p>
        </div>
    );
}

export default function UpdateCustomerProfileForm({ customer, summary, className = '' }) {
    const user = usePage().props.auth.user;
    const address = customer?.address || {};

    const { data, setData, patch, errors, processing, recentlySuccessful } = useForm({
        contact_name: customer?.contact_name || user.name || '',
        company_name: customer?.company_name || '',
        phone: customer?.phone || '',
        business_type: customer?.business_type || '',
        address_line1: address.line_1 || '',
        address_line2: address.line_2 || '',
        city: address.city || '',
        state: address.state || '',
        postal_code: address.postal_code || '',
        country: address.country || 'Bangladesh',
    });

    const submit = (event) => {
        event.preventDefault();
        patch(route('profile.customer.update'));
    };

    return (
        <section className={className}>
            <header>
                <h2 className="text-lg font-medium text-gray-900">Customer Profile</h2>

                <p className="mt-1 text-sm text-gray-600">
                    Keep your business record current for CRM, purchase history, and future segmentation.
                </p>
            </header>

            <div className="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <SummaryPill label="Status" value={customer.status} />
                <SummaryPill label="Lifecycle" value={customer.lifecycle_stage} />
                <SummaryPill label="Orders" value={summary?.orders_count ?? 0} />
                <SummaryPill label="Last activity" value={formatDate(customer.last_activity_at)} />
            </div>

            <div className="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm leading-6 text-slate-600">
                Your account email is managed in the profile section above. This form updates the buyer record used by CRM and order history.
            </div>

            <form onSubmit={submit} className="mt-6 space-y-6">
                <div className="grid gap-6 md:grid-cols-2">
                    <Field label="Contact name" error={errors.contact_name}>
                        <TextInput
                            id="contact_name"
                            className="mt-1 block w-full"
                            value={data.contact_name}
                            onChange={(event) => setData('contact_name', event.target.value)}
                            required
                            autoComplete="name"
                        />
                    </Field>

                    <Field label="Company name" error={errors.company_name}>
                        <TextInput
                            id="company_name"
                            className="mt-1 block w-full"
                            value={data.company_name}
                            onChange={(event) => setData('company_name', event.target.value)}
                            autoComplete="organization"
                        />
                    </Field>
                </div>

                <div className="grid gap-6 md:grid-cols-2">
                    <Field label="Phone" error={errors.phone}>
                        <TextInput
                            id="phone"
                            className="mt-1 block w-full"
                            value={data.phone}
                            onChange={(event) => setData('phone', event.target.value)}
                            autoComplete="tel"
                        />
                    </Field>

                    <Field label="Business type" error={errors.business_type}>
                        <TextInput
                            id="business_type"
                            className="mt-1 block w-full"
                            value={data.business_type}
                            onChange={(event) => setData('business_type', event.target.value)}
                            placeholder="Wholesale distributor, retailer, manufacturer..."
                        />
                    </Field>
                </div>

                <div className="grid gap-6 md:grid-cols-2">
                    <Field label="Address line 1" error={errors.address_line1}>
                        <TextInput
                            id="address_line1"
                            className="mt-1 block w-full"
                            value={data.address_line1}
                            onChange={(event) => setData('address_line1', event.target.value)}
                            autoComplete="address-line1"
                        />
                    </Field>

                    <Field label="Address line 2" error={errors.address_line2}>
                        <TextInput
                            id="address_line2"
                            className="mt-1 block w-full"
                            value={data.address_line2}
                            onChange={(event) => setData('address_line2', event.target.value)}
                            autoComplete="address-line2"
                        />
                    </Field>
                </div>

                <div className="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                    <Field label="City" error={errors.city}>
                        <TextInput
                            id="city"
                            className="mt-1 block w-full"
                            value={data.city}
                            onChange={(event) => setData('city', event.target.value)}
                            autoComplete="address-level2"
                        />
                    </Field>

                    <Field label="State / Region" error={errors.state}>
                        <TextInput
                            id="state"
                            className="mt-1 block w-full"
                            value={data.state}
                            onChange={(event) => setData('state', event.target.value)}
                            autoComplete="address-level1"
                        />
                    </Field>

                    <Field label="Postal code" error={errors.postal_code}>
                        <TextInput
                            id="postal_code"
                            className="mt-1 block w-full"
                            value={data.postal_code}
                            onChange={(event) => setData('postal_code', event.target.value)}
                            autoComplete="postal-code"
                        />
                    </Field>

                    <Field label="Country" error={errors.country}>
                        <select
                            id="country"
                            className="input mt-1"
                            value={data.country}
                            onChange={(event) => setData('country', event.target.value)}
                        >
                            {countryOptions.map((country) => (
                                <option key={country} value={country}>
                                    {country}
                                </option>
                            ))}
                        </select>
                    </Field>
                </div>

                <div className="flex items-center gap-4">
                    <PrimaryButton disabled={processing}>Save</PrimaryButton>

                    <Transition
                        show={recentlySuccessful}
                        enter="transition ease-in-out"
                        enterFrom="opacity-0"
                        leave="transition ease-in-out"
                        leaveTo="opacity-0"
                    >
                        <p className="text-sm text-gray-600">Saved.</p>
                    </Transition>
                </div>
            </form>
        </section>
    );
}

function Field({ label, error, children }) {
    return (
        <label className="block">
            <InputLabel value={label} />
            {children}
            <InputError className="mt-2" message={error} />
        </label>
    );
}
