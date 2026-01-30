import HeadingSmall from '@/components/heading-small';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { login } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Two-Factor Authentication',
        href: login(),
    },
];

export default function TwoFactor() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Two-Factor Authentication" />

            <h1 className="sr-only">Two-Factor Authentication Settings</h1>

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall
                        title="Two-Factor Authentication"
                        description="Two-factor authentication is managed by our login provider."
                    />

                    <Button asChild className="w-full sm:w-auto">
                        <Link href={login()} tabIndex={1}>
                            Continue to login
                        </Link>
                    </Button>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
