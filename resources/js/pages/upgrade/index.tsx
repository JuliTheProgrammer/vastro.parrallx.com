import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

const benefits = [
    'Create more than 3 vaults',
    'Use custom encryption policies',
    'Share more than 3 links at a time',
    'Priority export queue for large backups',
    'Extended retention and audit history',
];

export default function UpgradeIndex() {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Account', href: '#' },
                { title: 'Upgrade to Pro', href: '/upgrade' },
            ]}
        >
            <Head title="Upgrade to Pro" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div className="relative overflow-hidden rounded-2xl border border-border bg-gradient-to-br from-sky-500/10 via-emerald-500/10 to-amber-500/10 p-6">
                    <div className="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(56,189,248,0.18),transparent_55%)]" />
                    <div className="relative">
                        <div className="text-xs uppercase tracking-[0.3em] text-muted-foreground">
                            Pro subscription
                        </div>
                        <h1 className="mt-3 text-2xl font-semibold text-foreground">
                            Upgrade to Pro
                        </h1>
                        <p className="mt-2 max-w-xl text-sm text-muted-foreground">
                            Unlock advanced controls and scale your backup
                            storage with Pro.
                        </p>
                        <div className="mt-5 flex flex-wrap gap-2 text-xs text-muted-foreground">
                            <span className="rounded-full border border-border bg-background/70 px-3 py-1">
                                More vaults
                            </span>
                            <span className="rounded-full border border-border bg-background/70 px-3 py-1">
                                Custom encryption
                            </span>
                            <span className="rounded-full border border-border bg-background/70 px-3 py-1">
                                Higher share limits
                            </span>
                        </div>
                    </div>
                </div>
                <div>
                    <h2 className="text-lg font-semibold text-foreground">
                        Choose your plan
                    </h2>
                    <p className="text-sm text-muted-foreground">
                        Flexible billing for teams that need more power.
                    </p>
                </div>

                <div className="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                    <Card>
                        <CardHeader>
                            <CardTitle>Pro benefits</CardTitle>
                            <CardDescription>
                                Everything you need to manage bigger volumes.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <ul className="space-y-3 text-sm text-foreground">
                                {benefits.map((benefit) => (
                                    <li
                                        key={benefit}
                                        className="flex items-start gap-3"
                                    >
                                        <span className="mt-1 h-2 w-2 rounded-full bg-primary" />
                                        <span>{benefit}</span>
                                    </li>
                                ))}
                            </ul>
                        </CardContent>
                    </Card>

                    <Card className="border-primary/30 bg-primary/5">
                        <CardHeader>
                            <CardTitle>Pro plan</CardTitle>
                            <CardDescription>
                                Simple monthly billing, cancel anytime.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="text-3xl font-semibold text-foreground">
                                $2.99
                                <span className="text-sm font-normal text-muted-foreground">
                                    /month
                                </span>
                            </div>
                            <p className="mt-2 text-sm text-muted-foreground">
                                Includes vault expansion, custom encryption,
                                and higher sharing limits.
                            </p>
                        </CardContent>
                        <CardFooter className="gap-3">
                            <Button className="w-full">
                                Start Pro trial
                            </Button>
                        </CardFooter>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
