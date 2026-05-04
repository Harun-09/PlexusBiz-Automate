import { useEffect } from 'react';
import GuestLayout from '@/Layouts/GuestLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Head, useForm } from '@inertiajs/react';

export default function ConfirmPassword() {
    const { data, setData, post, processing, errors, reset } = useForm({
        password: '',
    });

    useEffect(() => {
        return () => {
            reset('password');
        };
    }, []);

    const submit = (e) => {
        e.preventDefault();

        post(route('password.confirm'));
    };

    return (
        <GuestLayout
            variant="register"
            registerHero={{
                eyebrow: 'Security check',
                title: 'Confirm your identity.',
                lead: 'This is a secure area. Please enter your password to confirm access to this module.',
            }}
        >
            <Head title="Confirm Password" />

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
                        <span>Identity Verification</span>
                    </span>
                </div>

                <div className="auth-form__header auth-form__header--register">
                    <span className="auth-eyebrow">Verify</span>
                    <h2>Enter your password.</h2>
                </div>

                <form onSubmit={submit} className="auth-form__body auth-form__body--register">
                    <div className="auth-field">
                        <InputLabel htmlFor="password" value="Password" className="auth-label" />
                        <TextInput
                            id="password"
                            type="password"
                            name="password"
                            value={data.password}
                            className="auth-input"
                            isFocused={true}
                            placeholder="Enter your password"
                            onChange={(e) => setData('password', e.target.value)}
                        />
                        <InputError message={errors.password} className="auth-error" />
                    </div>

                    <div className="auth-form__actions mt-6">
                        <PrimaryButton className="auth-submit w-full" disabled={processing}>
                            {processing ? 'Verifying...' : 'Confirm Password'}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </GuestLayout>
    );
}
