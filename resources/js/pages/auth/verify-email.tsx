import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/auth-layout';
import { login, logout } from '@/routes';
import { Head, Link } from '@inertiajs/react';

export default function VerifyEmail({ status }: { status?: string }) {
    return (
        <AuthLayout
            title="Verify email"
            description="Email verification is handled by our login provider."
        >
            <Head title="Email verification" />

            {status === 'verification-link-sent' && (
                <div className="mb-4 text-center text-sm font-medium text-green-600">
                    A new verification link has been sent to your email address.
                </div>
            )}

            <div className="flex flex-col gap-3 text-center">
                <Button asChild>
                    <Link href={login()} tabIndex={1}>
                        Continue to login
                    </Link>
                </Button>
                <Link
                    href={logout()}
                    method="post"
                    as="button"
                    className="text-sm text-muted-foreground hover:text-foreground"
                >
                    Log out
                </Link>
            </div>
        </AuthLayout>
    );
}
