import Header from '@/Components/Header';
import Sidebar from '@/Components/Sidebar';
import FlashBanner from '@/Components/FlashBanner';
import { useEffect, useState } from 'react';

export default function AppShell({ user, header, children, flash = null, showBreadcrumbs = true }) {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const currentPath = typeof window === 'undefined'
        ? '/dashboard'
        : `${window.location.pathname}${window.location.search}`;

    useEffect(() => {
        if (!sidebarOpen) {
            return;
        }

        const onKeyDown = (event) => {
            if (event.key === 'Escape') {
                setSidebarOpen(false);
            }
        };

        window.addEventListener('keydown', onKeyDown);
        return () => window.removeEventListener('keydown', onKeyDown);
    }, [sidebarOpen]);

    return (
        <div className="min-h-screen bg-[#f5f7fb] text-slate-900">
            <div className="hidden lg:fixed lg:inset-y-0 lg:z-40 lg:flex lg:w-72 lg:flex-col">
                <Sidebar user={user} currentPath={currentPath} />
            </div>

            {sidebarOpen ? (
                <div className="fixed inset-0 z-50 lg:hidden">
                    <button
                        type="button"
                        aria-label="Close navigation"
                        className="absolute inset-0 bg-slate-950/40"
                        onClick={() => setSidebarOpen(false)}
                    />
                    <div className="relative h-full w-80 max-w-[86vw] shadow-2xl">
                        <Sidebar user={user} currentPath={currentPath} onNavigate={() => setSidebarOpen(false)} />
                    </div>
                </div>
            ) : null}

            <div className="lg:pl-72">
                <Header
                    user={user}
                    header={header}
                    currentPath={currentPath}
                    onOpenSidebar={() => setSidebarOpen(true)}
                    showBreadcrumbs={showBreadcrumbs}
                />

                <main className="min-h-[calc(100vh-4rem)] px-3 py-5 sm:px-4 lg:px-6">
                    <div className="w-full space-y-6">
                        {flash?.success || flash?.error ? <FlashBanner flash={flash} /> : null}
                        {children}
                    </div>
                </main>
            </div>
        </div>
    );
}
