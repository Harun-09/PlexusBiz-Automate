import { useEffect, useState } from 'react';
import Checkbox from '@/Components/Checkbox';
import GuestLayout from '@/Layouts/GuestLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Head, useForm } from '@inertiajs/react';

export default function B2cLogin({ status }) {
    const [isRegistering, setIsRegistering] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        remember: false,
    });

    useEffect(() => {
        return () => {
            reset('password', 'password_confirmation');
        };
    }, [isRegistering]);

    const submit = (e) => {
        e.preventDefault();
        if (isRegistering) {
            post(route('b2c.register'));
        } else {
            post(route('b2c.login'));
        }
    };

    return (
        <GuestLayout
            variant="register"
            registerHero={{
                eyebrow: 'Customer Access',
                title: isRegistering ? 'Create your customer account.' : 'Sign in to your customer account.',
                lead: 'Join us to buy single products at fixed prices with ease.',
            }}
        >
            <Head title={isRegistering ? "Register (Customer)" : "Sign in (Customer)"} />

            <div className="auth-form auth-form--register">
                <div className="auth-card-brand">
                    <span className="auth-card-brand__mark">
                        <img
                            src="/images/project-logo.png"
                            alt="PlexusBiz Automate"
                            className="auth-card-brand__logo"
                        />
                    </span>
                    <span className="auth-card-brand__copy">
                        <strong>PlexusBiz Automate</strong>
                        <span>Customer Portal</span>
                    </span>
                </div>

                <div className="auth-form__header auth-form__header--register">
                    <span className="auth-eyebrow">{isRegistering ? "Customer Registration" : "Customer Sign in"}</span>
                    <h2>{isRegistering ? "Create a new account." : "Welcome back."}</h2>
                </div>

                {status && <div className="auth-alert auth-alert--success">{status}</div>}

                <form onSubmit={submit} className="auth-form__body auth-form__body--register">
                    {isRegistering && (
                        <div className="auth-field">
                            <InputLabel htmlFor="name" value="Full Name" className="auth-label" />
                            <TextInput
                                id="name"
                                type="text"
                                name="name"
                                value={data.name}
                                className="auth-input"
                                autoComplete="name"
                                isFocused={isRegistering}
                                placeholder="John Doe"
                                onChange={(e) => setData('name', e.target.value)}
                            />
                            <InputError message={errors.name} className="auth-error" />
                        </div>
                    )}

                    <div className="auth-field">
                        <InputLabel htmlFor="email" value="Email address" className="auth-label" />
                        <TextInput
                            id="email"
                            type="email"
                            name="email"
                            value={data.email}
                            className="auth-input"
                            autoComplete="username"
                            isFocused={!isRegistering}
                            placeholder="name@example.com"
                            onChange={(e) => setData('email', e.target.value)}
                        />
                        <InputError message={errors.email} className="auth-error" />
                    </div>

                    <div className="auth-field">
                        <InputLabel htmlFor="password" value="Password" className="auth-label" />
                        <TextInput
                            id="password"
                            type="password"
                            name="password"
                            value={data.password}
                            className="auth-input"
                            autoComplete={isRegistering ? "new-password" : "current-password"}
                            placeholder="Enter your password"
                            onChange={(e) => setData('password', e.target.value)}
                        />
                        <InputError message={errors.password} className="auth-error" />
                    </div>

                    {isRegistering && (
                        <div className="auth-field">
                            <InputLabel htmlFor="password_confirmation" value="Confirm Password" className="auth-label" />
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                value={data.password_confirmation}
                                className="auth-input"
                                autoComplete="new-password"
                                placeholder="Confirm your password"
                                onChange={(e) => setData('password_confirmation', e.target.value)}
                            />
                            <InputError message={errors.password_confirmation} className="auth-error" />
                        </div>
                    )}

                    {!isRegistering && (
                        <div className="auth-row">
                            <label className="auth-remember">
                                <Checkbox
                                    name="remember"
                                    checked={data.remember}
                                    className="auth-checkbox"
                                    onChange={(e) => setData('remember', e.target.checked)}
                                />
                                <span>Remember me</span>
                            </label>
                        </div>
                    )}

                    <PrimaryButton className="auth-submit" disabled={processing}>
                        {processing ? 'Processing...' : (isRegistering ? 'Register' : 'Log in')}
                    </PrimaryButton>
                </form>

                <div className="auth-alt-action">
                    <span>{isRegistering ? 'Already have an account?' : 'Need a customer account?'}</span>
                    <button type="button" onClick={() => setIsRegistering(!isRegistering)} className="auth-link ml-2">
                        {isRegistering ? 'Log in here' : 'Register here'}
                    </button>
                </div>
            </div>
        </GuestLayout>
    );
}
