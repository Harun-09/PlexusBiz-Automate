import { useEffect } from 'react';
import Checkbox from '@/Components/Checkbox';
import GuestLayout from '@/Layouts/GuestLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    useEffect(() => {
        return () => {
            reset('password');
        };
    }, []);

    const submit = (e) => {
        e.preventDefault();

        post(route('login'));
    };

    return (
        <GuestLayout
            variant="register"
            registerHero={{
                eyebrow: 'Business secure access',
                title: 'Access your operations control center.',
                lead: 'Sign in to manage teams, workflows, and B2B operations in PlexusBiz Automate.',
            }}
        >
            <Head title="Sign in" />

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
                        <span>Business operations portal</span>
                    </span>
                </div>

                <div className="auth-form__header auth-form__header--register">
                    <span className="auth-eyebrow">Sign in</span>
                    <h2>Sign in to your business workspace.</h2>
                </div>

                {status && <div className="auth-alert auth-alert--success">{status}</div>}

                <form onSubmit={submit} className="auth-form__body auth-form__body--register">
                    <div className="auth-field">
                        <InputLabel htmlFor="email" value="Email address" className="auth-label" />

                        <TextInput
                            id="email"
                            type="email"
                            name="email"
                            value={data.email}
                            className="auth-input"
                            autoComplete="username"
                            isFocused={true}
                            placeholder="name@company.com"
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
                            autoComplete="current-password"
                            placeholder="Enter your password"
                            onChange={(e) => setData('password', e.target.value)}
                        />

                        <InputError message={errors.password} className="auth-error" />
                    </div>

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

                        {canResetPassword && (
                            <Link href={route('password.request')} className="auth-link">
                                Forgot your password?
                            </Link>
                        )}
                    </div>

                    <PrimaryButton className="auth-submit" disabled={processing}>
                        {processing ? 'Signing in...' : 'Log in'}
                    </PrimaryButton>
                </form>

                <div className="auth-alt-action">
                    <span>Need an account?</span>
                    <Link href={route('register')} className="auth-link">
                        Create account
                    </Link>
                </div>
            </div>
        </GuestLayout>
    );
}
