import { useEffect } from 'react';
import Checkbox from '@/Components/Checkbox';
import GuestLayout from '@/Layouts/GuestLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Head, Link, useForm } from '@inertiajs/react';

const employeeOptions = ['1 - 10', '11 - 25', '26 - 50', '51 - 200', '201 - 500', '500+'];

const countryOptions = [
    'Bangladesh',
    'United States',
    'United Kingdom',
    'India',
    'Singapore',
    'Australia',
];

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        first_name: '',
        last_name: '',
        job_title: '',
        phone: '',
        employees: '',
        company: '',
        country: 'Bangladesh',
        email: '',
        password: '',
        password_confirmation: '',
        agree_terms: false,
        marketing_opt_in: false,
    });

    useEffect(() => {
        return () => {
            reset('password', 'password_confirmation');
        };
    }, []);

    useEffect(() => {
        const fullName = [data.first_name, data.last_name].filter(Boolean).join(' ').trim();

        if (data.name !== fullName) {
            setData('name', fullName);
        }
    }, [data.first_name, data.last_name, data.name, setData]);

    const submit = (e) => {
        e.preventDefault();

        post(route('register'));
    };

    return (
        <GuestLayout variant="register">
            <Head title="Create account" />

            <div className="auth-form auth-form--register">
                <div className="auth-card-brand auth-card-brand--visible">
                    <span className="auth-card-brand__mark">
                        <img
                            src="/images/plexuscloud-logo.png"
                            alt="Plexus Cloud"
                            className="auth-card-brand__logo"
                        />
                    </span>
                    <span className="auth-card-brand__copy">
                        <strong>Plexus Cloud</strong>
                        <span>Enterprise signup</span>
                    </span>
                </div>

                <div className="auth-form__header auth-form__header--register">
                    <span className="auth-eyebrow">Create your account</span>
                    <h2>Sign up now to start your free trial.</h2>
                    <p>
                        Complete the form to start your free trial. Our team will be in touch to help you
                        make the most of your workspace.
                    </p>
                </div>

                <div className="auth-register-badges" aria-label="Signup highlights">
                    <span className="auth-badge">Fast onboarding</span>
                    <span className="auth-badge">Team access</span>
                    <span className="auth-badge">Secure account</span>
                </div>

                <form onSubmit={submit} className="auth-form__body auth-form__body--register">
                    <div className="auth-register-grid auth-register-grid--two">
                        <div className="auth-field">
                            <InputLabel htmlFor="first_name" value="First name" className="auth-label" />

                            <TextInput
                                id="first_name"
                                name="first_name"
                                value={data.first_name}
                                className="auth-input"
                                autoComplete="given-name"
                                isFocused={true}
                                placeholder="Jane"
                                onChange={(e) => setData('first_name', e.target.value)}
                                required
                            />

                            <InputError message={errors.first_name} className="auth-error" />
                        </div>

                        <div className="auth-field">
                            <InputLabel htmlFor="last_name" value="Last name" className="auth-label" />

                            <TextInput
                                id="last_name"
                                name="last_name"
                                value={data.last_name}
                                className="auth-input"
                                autoComplete="family-name"
                                placeholder="Doe"
                                onChange={(e) => setData('last_name', e.target.value)}
                                required
                            />

                            <InputError message={errors.last_name} className="auth-error" />
                        </div>
                    </div>

                    <InputError message={errors.name} className="auth-error" />

                    <div className="auth-register-grid auth-register-grid--two">
                        <div className="auth-field">
                            <InputLabel htmlFor="job_title" value="Job title" className="auth-label" />

                            <TextInput
                                id="job_title"
                                name="job_title"
                                value={data.job_title}
                                className="auth-input"
                                autoComplete="organization-title"
                                placeholder="Sales Manager"
                                onChange={(e) => setData('job_title', e.target.value)}
                                required
                            />

                            <InputError message={errors.job_title} className="auth-error" />
                        </div>

                        <div className="auth-field">
                            <InputLabel htmlFor="company" value="Company" className="auth-label" />

                            <TextInput
                                id="company"
                                name="company"
                                value={data.company}
                                className="auth-input"
                                autoComplete="organization"
                                placeholder="Plexus Cloud"
                                onChange={(e) => setData('company', e.target.value)}
                                required
                            />

                            <InputError message={errors.company} className="auth-error" />
                        </div>
                    </div>

                    <div className="auth-register-grid auth-register-grid--two">
                        <div className="auth-field">
                            <InputLabel htmlFor="email" value="Email" className="auth-label" />

                            <TextInput
                                id="email"
                                type="email"
                                name="email"
                                value={data.email}
                                className="auth-input"
                                autoComplete="email"
                                placeholder="name@company.com"
                                onChange={(e) => setData('email', e.target.value)}
                                required
                            />

                            <InputError message={errors.email} className="auth-error" />
                        </div>

                        <div className="auth-field">
                            <InputLabel htmlFor="phone" value="Phone" className="auth-label" />

                            <TextInput
                                id="phone"
                                type="tel"
                                name="phone"
                                value={data.phone}
                                className="auth-input"
                                autoComplete="tel"
                                placeholder="+880 1XXXXXXXXX"
                                onChange={(e) => setData('phone', e.target.value)}
                            />

                            <InputError message={errors.phone} className="auth-error" />
                        </div>
                    </div>

                    <div className="auth-register-grid auth-register-grid--two">
                        <div className="auth-field">
                            <InputLabel htmlFor="employees" value="Employees" className="auth-label" />

                            <select
                                id="employees"
                                name="employees"
                                value={data.employees}
                                className="auth-input auth-select"
                                onChange={(e) => setData('employees', e.target.value)}
                            >
                                <option value="" disabled>
                                    Select team size
                                </option>
                                {employeeOptions.map((option) => (
                                    <option key={option} value={option}>
                                        {option}
                                    </option>
                                ))}
                            </select>

                            <InputError message={errors.employees} className="auth-error" />
                        </div>

                        <div className="auth-field">
                            <InputLabel htmlFor="country" value="Country/Region" className="auth-label" />

                            <select
                                id="country"
                                name="country"
                                value={data.country}
                                className="auth-input auth-select"
                                onChange={(e) => setData('country', e.target.value)}
                            >
                                {countryOptions.map((option) => (
                                    <option key={option} value={option}>
                                        {option}
                                    </option>
                                ))}
                            </select>

                            <InputError message={errors.country} className="auth-error" />
                        </div>
                    </div>

                    <div className="auth-register-grid auth-register-grid--two">
                        <div className="auth-field">
                            <InputLabel htmlFor="password" value="Password" className="auth-label" />

                            <TextInput
                                id="password"
                                type="password"
                                name="password"
                                value={data.password}
                                className="auth-input"
                                autoComplete="new-password"
                                placeholder="Create a strong password"
                                onChange={(e) => setData('password', e.target.value)}
                                required
                            />

                            <InputError message={errors.password} className="auth-error" />
                        </div>

                        <div className="auth-field">
                            <InputLabel
                                htmlFor="password_confirmation"
                                value="Confirm password"
                                className="auth-label"
                            />

                            <TextInput
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                value={data.password_confirmation}
                                className="auth-input"
                                autoComplete="new-password"
                                placeholder="Repeat your password"
                                onChange={(e) => setData('password_confirmation', e.target.value)}
                                required
                            />

                            <InputError message={errors.password_confirmation} className="auth-error" />
                        </div>
                    </div>

                    <div className="auth-register-consents">
                        <label className="auth-consent">
                            <Checkbox
                                name="agree_terms"
                                checked={data.agree_terms}
                                className="auth-checkbox auth-checkbox--register"
                                onChange={(e) => setData('agree_terms', e.target.checked)}
                                required
                            />
                            <span>
                                I agree to the <span className="auth-link">Main Services Agreement</span>.
                            </span>
                        </label>

                        <label className="auth-consent auth-consent--secondary">
                            <Checkbox
                                name="marketing_opt_in"
                                checked={data.marketing_opt_in}
                                className="auth-checkbox auth-checkbox--register"
                                onChange={(e) => setData('marketing_opt_in', e.target.checked)}
                            />
                            <span>
                                Yes, I would like to receive product updates and release notes.
                            </span>
                        </label>
                    </div>

                    <p className="auth-note auth-note--register">
                        We use this information to create your workspace and keep onboarding fast for your
                        team.
                    </p>

                    <PrimaryButton className="auth-submit" disabled={processing}>
                        {processing ? 'Creating account...' : 'Start my free trial'}
                    </PrimaryButton>

                    <div className="auth-alt-action">
                        <span>Already have an account?</span>
                        <Link href={route('login')} className="auth-link">
                            Log in
                        </Link>
                    </div>
                </form>
            </div>
        </GuestLayout>
    );
}
