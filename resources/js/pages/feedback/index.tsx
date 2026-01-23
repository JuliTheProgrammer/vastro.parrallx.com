import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Mail } from 'lucide-react';

export default function FeedbackPage() {
    return (
        <AppLayout breadcrumbs={[{ title: 'Feedback', href: '/feedback' }]}>
            <Head title="Feedback" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div>
                    <h1 className="text-lg font-semibold text-foreground">
                        Feedback
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Send product feedback or reach out directly over email.
                    </p>
                </div>

                <div className="max-w-xl rounded-xl border border-border bg-card p-6">
                    <div className="text-sm font-medium">Send email</div>
                    <p className="mt-2 text-sm text-muted-foreground">
                        Prefer email? Contact our team directly and we will follow up.
                    </p>
                    <div className="mt-6 flex items-center gap-3 rounded-lg border border-border bg-muted/40 p-4">
                        <span className="flex h-10 w-10 items-center justify-center rounded-full bg-background">
                            <Mail className="h-5 w-5" />
                        </span>
                        <div>
                            <div className="text-sm font-medium text-foreground">
                                info@vastro.dev
                            </div>
                            <div className="text-xs text-muted-foreground">
                                Mon-Fri · 9am-6pm CET
                            </div>
                        </div>
                    </div>
                    <Button variant="outline" className="mt-4" asChild>
                        <a href="mailto:info@vastro.dev">Email support</a>
                    </Button>
                </div>
            </div>
        </AppLayout>
    );
}
