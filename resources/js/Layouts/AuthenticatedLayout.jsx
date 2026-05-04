import AppShell from '@/Layouts/AppShell';
import { usePage } from '@inertiajs/react';

export default function Authenticated({ user, header, children }) {
    const { props } = usePage();

    return (
        <AppShell user={user} header={header} flash={props.flash}>
            {children}
        </AppShell>
    );
}
