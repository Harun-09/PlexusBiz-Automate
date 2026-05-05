import AppShell from '@/Layouts/AppShell';
import { usePage } from '@inertiajs/react';

export default function Authenticated({ user, header, children, showBreadcrumbs = true }) {
    const { props } = usePage();

    return (
        <AppShell user={user} header={header} flash={props.flash} showBreadcrumbs={showBreadcrumbs}>
            {children}
        </AppShell>
    );
}
