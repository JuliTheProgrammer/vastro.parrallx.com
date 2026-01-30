import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/auth-layout';
import { login } from '@/routes';
import { Head, Link } from '@inertiajs/react';

export default function TwoFactorChallenge() {
    return (
        <AuthLayout
            title="Two-factor authentication"
            description="Two-factor authentication is handled by our login provider."
        >
            <Head title="Two-factor authentication" />

            <div className="flex flex-col gap-6">
                <Button asChild className="w-full">
                    <Link href={login()} tabIndex={1}>
                        Continue to login
                    </Link>
                </Button>
            </div>
        </AuthLayout>
    );
}
