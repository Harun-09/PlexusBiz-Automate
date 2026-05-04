import { useEffect } from 'react';
import GuestLayout from '@/Layouts/GuestLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Head, useForm } from '@inertiajs/react';

export default function ResetPassword({ token, email }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        token: token,
        email: email,
        password: '',
        password_confirmation: '',
    });

    useEffect(() => {
        return () => {
            reset('password', 'password_confirmation');
        };
    }, []);

    const submit = (e) => {
        e.preventDefault();

        post(route('password.store'));
    };

    return (
        <GuestLayout
            variant="register"
            registerHero={{
                eyebrow: 'Secure update',
                title: 'Reset your password.',
                lead: 'Create a new, strong password for your account to get back into your workspace.',
            }}
        >
            <Head title="Reset Password" />

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
                        <span>Password Reset</span>
                    </span>
                </div>

                <div className="auth-form__header auth-form__header--register">
                    <span className="auth-eyebrow">Reset</span>
                    <h2>Choose a new password.</h2>
                </div>

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
                            onChange={(e) => setData('email', e.target.value)}
                        />
                        <InputError message={errors.email} className="auth-error" />
                    </div>

                    <div className="auth-field">
                        <InputLabel htmlFor="password" value="New Password" className="auth-label" />
                        <TextInput
                            id="password"
                            type="password"
                            name="password"
                            value={data.password}
                            className="auth-input"
                            autoComplete="new-password"
                            isFocused={true}
                            placeholder="Enter new password"
                            onChange={(e) => setData('password', e.target.value)}
                        />
                        <InputError message={errors.password} className="auth-error" />
                    </div>

                    <div className="auth-field">
                        <InputLabel htmlFor="password_confirmation" value="Confirm New Password" className="auth-label" />
                        <TextInput
                            type="password"
                            name="password_confirmation"
                            value={data.password_confirmation}
                            className="auth-input"
                            autoComplete="new-password"
                            placeholder="Repeat new password"
                            onChange={(e) => setData('password_confirmation', e.target.value)}
                        />
                        <InputError message={errors.password_confirmation} className="auth-error" />
                    </div>

                    <div className="auth-form__actions mt-6">
                        <PrimaryButton className="auth-submit w-full" disabled={processing}>
                            {processing ? 'Updating...' : 'Reset Password'}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </GuestLayout>
    );
}
