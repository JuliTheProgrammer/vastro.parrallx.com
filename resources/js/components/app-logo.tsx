import { type SharedData } from '@/types';
import { usePage } from '@inertiajs/react';

export default function AppLogo() {
    const { logo } = usePage<SharedData>().props;

    return (
        <>
            <div className="ml-1 flex items-center text-left text-sm">
                <img
                    src={logo || '/logo.svg'}
                    alt="Parallax logo"
                    className="h-12 w-12 object-cover"
                />
                <span className="mb-0.5 truncate leading-tight font-semibold">Parallax</span>
            </div>
        </>
    );
}
