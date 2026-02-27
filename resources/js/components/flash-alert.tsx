import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { SharedData } from '@/types';
import { usePage } from '@inertiajs/react';
import { AlertCircleIcon, XIcon } from 'lucide-react';
import { useEffect, useState } from 'react';

export default function FlashAlert() {
    const { flash } = usePage<SharedData>().props;
    const error = flash?.error;
    const [visible, setVisible] = useState(false);

    useEffect(() => {
        if (error) {
            setVisible(true);
        }
    }, [error]);

    if (!visible || !error) {
        return null;
    }

    return (
        <div className="fixed right-4 top-4 z-[9999] w-full max-w-sm">
            <Alert variant="destructive" className="relative shadow-lg">
                <AlertCircleIcon />
                <AlertTitle>Error</AlertTitle>
                <AlertDescription>{error}</AlertDescription>
                <button
                    onClick={() => setVisible(false)}
                    className="absolute right-3 top-3 rounded-sm opacity-70 hover:opacity-100"
                    aria-label="Dismiss"
                >
                    <XIcon className="h-4 w-4" />
                </button>
            </Alert>
        </div>
    );
}
