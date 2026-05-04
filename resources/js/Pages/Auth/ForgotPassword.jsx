import GuestLayout from '@/Layouts/GuestLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Head, Link, useForm } from '@inertiajs/react';

export default function ForgotPassword({ status }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    const submit = (e) => {
        e.preventDefault();

        post(route('password.email'));
    };

    return (
        <GuestLayout
            variant="register"
            registerHero={{
                eyebrow: 'Account recovery',
                title: 'Forgot your password?',
                lead: 'No problem. Just let us know your email address and we will email you a password reset link.',
            }}
        >
            <Head title="Forgot Password" />

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
                        <span>Account Recovery</span>
                    </span>
                </div>

                <div className="auth-form__header auth-form__header--register">
                    <span className="auth-eyebrow">Recovery</span>
                    <h2>Reset your password.</h2>
                    <p className="auth-description mt-2">
                        Enter the email address associated with your account and we'll send you a link to reset your password.
                    </p>
                </div>

                {status && <div className="auth-alert auth-alert--success mb-4">{status}</div>}

                <form onSubmit={submit} className="auth-form__body auth-form__body--register">
                    <div className="auth-field">
                        <InputLabel htmlFor="email" value="Email address" className="auth-label" />
                        <TextInput
                            id="email"
                            type="email"
                            name="email"
                            value={data.email}
                            className="auth-input"
                            isFocused={true}
                            placeholder="name@company.com"
                            onChange={(e) => setData('email', e.target.value)}
                        />
                        <InputError message={errors.email} className="auth-error" />
                    </div>

                    <div className="auth-form__actions mt-6">
                        <PrimaryButton className="auth-submit w-full" disabled={processing}>
                            {processing ? 'Sending Link...' : 'Email Password Reset Link'}
                        </PrimaryButton>
                    </div>
                </form>

                <div className="auth-alt-action">
                    <span>Remembered your password?</span>
                    <Link href={route('login')} className="auth-link">
                        Back to login
                    </Link>
                </div>
            </div>
        </GuestLayout>
    );
}
