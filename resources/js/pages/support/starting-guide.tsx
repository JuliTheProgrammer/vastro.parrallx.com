import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

export default function StartingGuide() {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Help Center', href: '/support/starting-guide' },
                { title: 'Starting guide', href: '/support/starting-guide' },
            ]}
        >
            <Head title="Starting guide" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div>
                    <h1 className="text-lg font-semibold text-foreground">
                        Starting guide
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        A quick overview of how to store and protect your backups.
                    </p>
                </div>

                <div className="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                    <div className="rounded-xl border border-border bg-card p-6">
                        <h2 className="text-base font-semibold text-foreground">
                            What this app does
                        </h2>
                        <p className="mt-2 text-sm text-muted-foreground">
                            Parrallx is a backups service. Upload files in chunks,
                            organize them into vaults, and keep everything protected
                            in dedicated S3 buckets.
                        </p>

                        <h2 className="mt-6 text-base font-semibold text-foreground">
                            Create a vault first
                        </h2>
                        <p className="mt-2 text-sm text-muted-foreground">
                            Vaults map to dedicated S3 buckets. Create a vault before
                            you upload any backups so every file lands in the right
                            place.
                        </p>

                        <h2 className="mt-6 text-base font-semibold text-foreground">
                            Upload backups
                        </h2>
                        <p className="mt-2 text-sm text-muted-foreground">
                            Choose one or more files, pick a vault, and upload.
                            We handle chunking for large files so you can upload
                            as much as you want.
                        </p>
                    </div>

                    <div className="space-y-6">
                        <div className="rounded-xl border border-border bg-card p-6">
                            <h3 className="text-base font-semibold text-foreground">
                                Storage classes
                            </h3>
                            <p className="mt-2 text-sm text-muted-foreground">
                                Storage classes control retrieval speed and cost.
                                Choose the class that matches how often you need
                                to restore.
                            </p>
                            <div className="mt-4 space-y-2 text-sm text-muted-foreground">
                                <div>Standard: fastest access for frequent restores.</div>
                                <div>Standard - Infrequent Access: lower cost, occasional restores.</div>
                                <div>One Zone - Infrequent Access: cost-optimized, single AZ.</div>
                                <div>Glacier Instant Retrieval: archival with fast access.</div>
                                <div>Intelligent Tiering: auto-optimizes access tiers.</div>
                                <div>Deep Archive: lowest cost for long-term retention.</div>
                            </div>
                        </div>

                        <div className="rounded-xl border border-border bg-card p-6">
                            <h3 className="text-base font-semibold text-foreground">
                                Protections
                            </h3>
                            <div className="mt-3 space-y-3 text-sm text-muted-foreground">
                                <div>
                                    <span className="font-medium text-foreground">
                                        WORM Protection:
                                    </span>{' '}
                                    Write once, read many. Prevents edits and preserves
                                    compliance-grade retention.
                                </div>
                                <div>
                                    <span className="font-medium text-foreground">
                                        Delete Protection:
                                    </span>{' '}
                                    Adds an extra guard so backups cannot be removed
                                    accidentally.
                                </div>
                            </div>
                        </div>

                        <div className="rounded-xl border border-border bg-card p-6">
                            <h3 className="text-base font-semibold text-foreground">
                                Pricing
                            </h3>
                            <p className="mt-2 text-sm text-muted-foreground">
                                Everything is free. Upload as much as you want.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
