import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/auth-layout';
import { login } from '@/routes';
import { Head, Link } from '@inertiajs/react';

interface LoginProps {
    status?: string;
    canResetPassword: boolean;
}

export default function Login({ status, canResetPassword }: LoginProps) {
    return (
        <AuthLayout
            title="Log in to your account"
            description="Continue to the login provider to access your account"
        >
            <Head title="Log in" />

            <div className="flex flex-col gap-6">
                {canResetPassword && (
                    <p className="text-sm text-muted-foreground">
                        Password resets are handled by our login provider.
                    </p>
                )}
                <Button asChild className="w-full">
                    <Link href={login()} tabIndex={1} data-test="login-button">
                        Continue to login
                    </Link>
                </Button>
            </div>

            {status && (
                <div className="mb-4 text-center text-sm font-medium text-green-600">
                    {status}
                </div>
            )}
        </AuthLayout>
    );
}
