import { login } from '@/routes';
import { Head, Link } from '@inertiajs/react';

import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/auth-layout';

export default function Register() {
    return (
        <AuthLayout
            title="Create an account"
            description="Sign up is handled via our login provider."
        >
            <Head title="Register" />
            <div className="flex flex-col gap-6">
                <p className="text-sm text-muted-foreground">
                    Continue to log in to create or access your account.
                </p>
                <Button asChild className="w-full">
                    <Link href={login()} tabIndex={1}>
                        Continue to login
                    </Link>
                </Button>
            </div>
        </AuthLayout>
    );
}
