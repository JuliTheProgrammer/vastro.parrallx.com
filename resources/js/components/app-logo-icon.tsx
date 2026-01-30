import { cn } from '@/lib/utils';
import { type SharedData } from '@/types';
import { usePage } from '@inertiajs/react';

export default function AppLogoIcon({ className }: { className?: string }) {
    const { logo } = usePage<SharedData>().props;

    return (
        <img
            src={logo || '/logo.svg'}
            alt="Parallax logo"
            className={cn('h-6 w-6 object-cover', className)}
        />
    );
}
