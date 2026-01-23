import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Search } from 'lucide-react';

const articles = [
    {
        id: 'sup-01',
        title: 'Getting started with backups',
        category: 'Getting started',
        excerpt: 'Learn how to upload, organize, and manage your backups.',
        date: 'Nov 12, 2024',
    },
    {
        id: 'sup-02',
        title: 'Understanding WORM protection',
        category: 'Security',
        excerpt: 'Explore write-once, read-many retention and compliance tips.',
        date: 'Nov 05, 2024',
    },
    {
        id: 'sup-03',
        title: 'Creating data vaults',
        category: 'Configuration',
        excerpt: 'Set up vaults with encryption, versioning, and deletion controls.',
        date: 'Oct 28, 2024',
    },
    {
        id: 'sup-04',
        title: 'Sharing backups securely',
        category: 'Sharing',
        excerpt: 'Generate share links with access scopes and expiration dates.',
        date: 'Oct 21, 2024',
    },
    {
        id: 'sup-05',
        title: 'Diagnosing sync delays',
        category: 'Troubleshooting',
        excerpt: 'Steps to inspect replication lags and vault status changes.',
        date: 'Oct 11, 2024',
    },
    {
        id: 'sup-06',
        title: 'Managing access policies',
        category: 'Security',
        excerpt: 'Keep backup access aligned with least privilege practices.',
        date: 'Sep 29, 2024',
    },
];

export default function SupportPage() {
    const [query, setQuery] = useState('');

    const filtered = useMemo(() => {
        const normalized = query.trim().toLowerCase();
        if (!normalized) {
            return articles;
        }
        return articles.filter((article) => {
            return (
                article.title.toLowerCase().includes(normalized) ||
                article.category.toLowerCase().includes(normalized) ||
                article.excerpt.toLowerCase().includes(normalized)
            );
        });
    }, [query]);

    return (
        <AppLayout breadcrumbs={[{ title: 'Support', href: '/support' }]}>
            <Head title="Support" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div>
                    <h1 className="text-lg font-semibold text-foreground">
                        Support
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Search help articles and best practices.
                    </p>
                </div>

                <div className="rounded-xl border border-border bg-card p-6">
                    <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <div className="relative flex-1">
                            <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                value={query}
                                onChange={(event) => setQuery(event.target.value)}
                                placeholder="Search for articles, topics, or keywords"
                                className="pl-9"
                            />
                        </div>
                        <Button variant="outline">Search</Button>
                    </div>
                </div>

                <div className="grid gap-4 lg:grid-cols-2">
                    {filtered.map((article) => (
                        <div
                            key={article.id}
                            className="rounded-xl border border-border bg-card p-5"
                        >
                            <div className="text-xs font-medium text-muted-foreground">
                                {article.category} · {article.date}
                            </div>
                            <h2 className="mt-2 text-base font-semibold text-foreground">
                                {article.title}
                            </h2>
                            <p className="mt-2 text-sm text-muted-foreground">
                                {article.excerpt}
                            </p>
                            <button className="mt-4 text-sm font-medium text-primary">
                                Read article
                            </button>
                        </div>
                    ))}
                </div>
            </div>
        </AppLayout>
    );
}
