import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Search } from 'lucide-react';

export default function Dashboard() {
    return (
        <AppLayout
            breadcrumbs={[
                {
                    title: 'Dashboard',
                    href: dashboard().url,
                },
            ]}
        >
            <Head title="Dashboard" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div>
                    <h1 className="text-lg font-semibold text-foreground">
                        Dashboard
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Status, storage, and activity in one place.
                    </p>
                </div>

                <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div className="rounded-xl border border-border bg-card p-4">
                        <div className="text-xs font-medium text-muted-foreground">
                            Files uploaded
                        </div>
                        <div className="mt-2 text-2xl font-semibold">1,284</div>
                        <div className="mt-1 text-xs text-muted-foreground">
                            +4.2% this week
                        </div>
                    </div>
                    <div className="rounded-xl border border-border bg-card p-4">
                        <div className="text-xs font-medium text-muted-foreground">
                            Plan & price
                        </div>
                        <div className="mt-2 text-2xl font-semibold">
                            Pro · €149/mo
                        </div>
                        <div className="mt-1 text-xs text-muted-foreground">
                            Renews on Dec 01
                        </div>
                    </div>
                    <div className="rounded-xl border border-border bg-card p-4">
                        <div className="text-xs font-medium text-muted-foreground">
                            Storage remaining
                        </div>
                        <div className="mt-2 text-2xl font-semibold">
                            2.4 TB
                        </div>
                        <div className="mt-1 text-xs text-muted-foreground">
                            7.6 TB used of 10 TB
                        </div>
                    </div>
                    <div className="rounded-xl border border-border bg-card p-4">
                        <div className="text-xs font-medium text-muted-foreground">
                            System status
                        </div>
                        <div className="mt-2 flex items-center gap-2 text-sm font-semibold">
                            <span className="h-2.5 w-2.5 rounded-full bg-emerald-500" />
                            All systems operational
                        </div>
                        <div className="mt-1 text-xs text-muted-foreground">
                            Last incident: 32 days ago
                        </div>
                    </div>
                </div>

                <div className="grid gap-4 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)]">
                    <div className="rounded-xl border border-border bg-card p-6">
                        <div className="text-sm font-medium">Quick AI search</div>
                        <div className="mt-3 flex flex-wrap items-center gap-2 rounded-lg border border-border bg-background p-2">
                            <Search className="h-4 w-4 text-muted-foreground" />
                            <Input
                                placeholder="Ask AI to find backups, links, or vault activity..."
                                className="h-9 min-w-[220px] flex-1 border-0 bg-transparent p-0 shadow-none focus-visible:ring-0"
                            />
                            <Button size="sm">Ask AI</Button>
                        </div>
                        <div className="mt-4 grid gap-3 sm:grid-cols-2">
                            <div className="rounded-lg border border-border bg-muted/40 p-3 text-sm">
                                “Show links accessed in the last 24 hours”
                            </div>
                            <div className="rounded-lg border border-border bg-muted/40 p-3 text-sm">
                                “Find backups larger than 2GB”
                            </div>
                        </div>
                    </div>

                    <div className="rounded-xl border border-border bg-card p-6">
                        <div className="text-sm font-medium">Link access</div>
                        <div className="mt-3 space-y-3 text-sm">
                            <div className="flex items-center justify-between rounded-lg border border-border bg-muted/40 p-3">
                                <div>
                                    <div className="font-medium">Finance exports</div>
                                    <div className="text-xs text-muted-foreground">
                                        Last accessed 2h ago
                                    </div>
                                </div>
                                <div className="text-xs text-muted-foreground">
                                    18 visits
                                </div>
                            </div>
                            <div className="flex items-center justify-between rounded-lg border border-border bg-muted/40 p-3">
                                <div>
                                    <div className="font-medium">Partner review</div>
                                    <div className="text-xs text-muted-foreground">
                                        Never accessed
                                    </div>
                                </div>
                                <div className="text-xs text-muted-foreground">
                                    0 visits
                                </div>
                            </div>
                            <div className="flex items-center justify-between rounded-lg border border-border bg-muted/40 p-3">
                                <div>
                                    <div className="font-medium">Ops handoff</div>
                                    <div className="text-xs text-muted-foreground">
                                        Last accessed 3 days ago
                                    </div>
                                </div>
                                <div className="text-xs text-muted-foreground">
                                    6 visits
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="rounded-xl border border-border bg-card p-6">
                    <div className="text-sm font-medium">
                        Most frequently accessed
                    </div>
                    <div className="mt-4 grid gap-3 md:grid-cols-3">
                        {[
                            { title: 'Marketing site backup', count: '312 hits' },
                            { title: 'Ops handoff', count: '184 hits' },
                            { title: 'Investor deck', count: '129 hits' },
                        ].map((item) => (
                            <div
                                key={item.title}
                                className="rounded-lg border border-border bg-muted/40 p-3 text-sm"
                            >
                                <div className="font-medium">{item.title}</div>
                                <div className="text-xs text-muted-foreground">
                                    {item.count}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
