import { Link } from '@inertiajs/react';
import Footer from '@/Components/Footer';

export default function Guest({ children, variant = 'login', registerHero = {} }) {
    const isRegister = variant === 'register';
    const registerHeroContent = {
        eyebrow: registerHero.eyebrow ?? 'Create account',
        title: registerHero.title ?? 'Start your free trial today.',
        lead: registerHero.lead ?? 'No credit card required.',
    };

    if (isRegister) {
        return (
            <div className="auth-page auth-page--register">
                <div className="auth-register-backdrop" aria-hidden="true">
                    <span className="auth-register-backdrop__curve auth-register-backdrop__curve--large" />
                    <span className="auth-register-backdrop__curve auth-register-backdrop__curve--small" />
                    <span className="auth-register-backdrop__glow" />
                </div>

                <div className="auth-shell auth-shell--register">
                    <aside className="auth-register-hero">
                        <Link href="/" className="auth-brand auth-brand--register" aria-label="Go to homepage">
                            <span className="auth-brand__mark auth-brand__mark--register">
                                <img
                                    src="/images/project-logo.png"
                                    alt="PlexusBiz Automate"
                                    className="auth-brand__logo"
                                />
                            </span>
                            <span className="auth-brand__copy">
                                <strong>PlexusBiz Automate</strong>
                                <span>Business automation workspace</span>
                            </span>
                        </Link>

                        <div className="auth-register-hero__eyebrow">
                            {registerHeroContent.eyebrow}
                        </div>
                        <h1>{registerHeroContent.title}</h1>
                        <p className="auth-register-hero__lede">{registerHeroContent.lead}</p>

                        <div className="auth-register-visual" aria-hidden="true">
                            <div className="auth-register-visual__orb auth-register-visual__orb--large" />
                            <div className="auth-register-visual__orb auth-register-visual__orb--small" />

                            <div className="auth-register-visual__monitor">
                                <div className="auth-register-visual__browser">
                                    <span />
                                    <span />
                                    <span />
                                </div>

                                <div className="auth-register-visual__dashboard">
                                    <div className="auth-register-visual__tile auth-register-visual__tile--wide" />
                                    <div className="auth-register-visual__tile" />
                                    <div className="auth-register-visual__tile auth-register-visual__tile--soft" />
                                    <div className="auth-register-visual__stack">
                                        <span />
                                        <span />
                                        <span />
                                    </div>
                                </div>
                            </div>

                            <div className="auth-register-visual__card auth-register-visual__card--left">
                                <strong>2 min</strong>
                                <span>Quick setup</span>
                            </div>

                            <div className="auth-register-visual__card auth-register-visual__card--right">
                                <strong>Secure</strong>
                                <span>Account access</span>
                            </div>
                        </div>
                    </aside>

                    <main className="auth-card-wrap auth-card-wrap--register">
                        <div className="auth-card auth-card--register">{children}</div>
                    </main>
                </div>
            </div>
        );
    }

    return (
        <div className="auth-page">
            <div className="auth-shell">
                <aside className="auth-panel">
                    <div className="auth-panel__top">
                        <Link href="/" className="auth-brand" aria-label="Go to homepage">
                            <span className="auth-brand__mark">
                                <img
                                    src="/images/project-logo.png"
                                    alt="PlexusBiz Automate"
                                    className="auth-brand__logo"
                                />
                            </span>
                            <span className="auth-brand__copy">
                                <strong>PlexusBiz Automate</strong>
                                <span>Business automation workspace</span>
                            </span>
                        </Link>

                        <div className="auth-panel__eyebrow">Secure access</div>

                        <h1>Run sales, support, and workflows from one clean console.</h1>
                        <p className="auth-panel__lede">
                            Sign in to access your dashboards, automation tools, and team data with a sharper,
                            more focused workspace.
                        </p>
                    </div>

                    <div className="auth-metrics" aria-label="Platform highlights">
                        <div className="auth-metric">
                            <strong>24/7</strong>
                            <span>Workflow visibility</span>
                        </div>
                        <div className="auth-metric">
                            <strong>1 place</strong>
                            <span>For every module</span>
                        </div>
                        <div className="auth-metric">
                            <strong>Fast</strong>
                            <span>Focused login flow</span>
                        </div>
                    </div>

                    <ul className="auth-highlights">
                        <li>Sharper interface with strong contrast and clear hierarchy.</li>
                        <li>Designed for speed on desktop while remaining calm on mobile.</li>
                        <li>Built to feel premium without losing Laravel simplicity.</li>
                    </ul>
                </aside>

                <main className="auth-card-wrap">
                    <div className="auth-card">{children}</div>
                </main>
            </div>
            <Footer />
        </div>
    );
}
