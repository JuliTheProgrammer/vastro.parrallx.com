import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Sparkles } from 'lucide-react';

const vaults = ['Primary VaultAction', 'Compliance VaultAction', 'Legacy VaultAction'];
const fileTypes = ['All types', 'Images', 'Documents', 'Archives', 'Videos'];
const dateRanges = ['Any time', 'Last 7 days', 'Last 30 days', 'This year'];

const messages = [
    {
        role: 'assistant',
        content:
            'Ask me to find backups, summarize changes, or locate items across vaults.',
    },
    {
        role: 'user',
        content: 'Show me all backups shared with write access last week.',
    },
];

export default function AiSearch() {
    const [mode, setMode] = useState<'search' | 'ai'>('search');
    const [query, setQuery] = useState('');
    const [vault, setVault] = useState('');
    const [fileType, setFileType] = useState('');
    const [dateRange, setDateRange] = useState('');

    const helperText = useMemo(() => {
        if (mode === 'ai') {
            return 'AI mode uses the same query bar. Ask a question to generate answers.';
        }
        return 'Search mode lets you filter and scan your backups quickly.';
    }, [mode]);

    return (
        <AppLayout breadcrumbs={[{ title: 'AI Search', href: '/ai' }]}>
            <Head title="AI Search" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div>
                    <h1 className="text-lg font-semibold text-foreground">
                        AI Search
                    </h1>
                    <p className="text-sm text-muted-foreground">{helperText}</p>
                </div>

                <div className="rounded-xl border border-border bg-card p-6">
                    <div className="grid gap-4">
                        <Label>Search</Label>
                        <div className="flex flex-wrap items-center gap-2 rounded-lg border border-border bg-background p-2">
                            <span className="inline-flex items-center gap-1 rounded-md bg-muted px-2 py-1 text-xs font-medium text-muted-foreground">
                                <Sparkles className="h-3 w-3" />
                                {mode === 'ai' ? 'AI Chat' : 'Search'}
                            </span>
                            <Input
                                value={query}
                                onChange={(event) => setQuery(event.target.value)}
                                placeholder={
                                    mode === 'ai'
                                        ? 'Ask AI to locate or summarize backups...'
                                        : 'Search backups by title, tag, or ID...'
                                }
                                className="h-9 min-w-[200px] flex-1 border-0 bg-transparent p-0 shadow-none focus-visible:ring-0"
                            />
                            <div className="flex items-center gap-1">
                                <Button
                                    variant={mode === 'search' ? 'default' : 'outline'}
                                    size="sm"
                                    onClick={() => setMode('search')}
                                >
                                    Search
                                </Button>
                                <Button
                                    variant={mode === 'ai' ? 'default' : 'outline'}
                                    size="sm"
                                    onClick={() => setMode('ai')}
                                >
                                    Ask AI
                                </Button>
                            </div>
                            <Button size="sm" disabled={!query}>
                                Run
                            </Button>
                        </div>

                        <div className="grid gap-4 md:grid-cols-3">
                            <div className="space-y-2">
                                <Label>Data vault</Label>
                                <Select value={vault} onValueChange={setVault}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Any vault" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {vaults.map((item) => (
                                            <SelectItem key={item} value={item}>
                                                {item}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="space-y-2">
                                <Label>File type</Label>
                                <Select value={fileType} onValueChange={setFileType}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="All types" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {fileTypes.map((item) => (
                                            <SelectItem key={item} value={item}>
                                                {item}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="space-y-2">
                                <Label>Date range</Label>
                                <Select value={dateRange} onValueChange={setDateRange}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Any time" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {dateRanges.map((item) => (
                                            <SelectItem key={item} value={item}>
                                                {item}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                    </div>
                </div>

                {mode === 'ai' ? (
                    <div className="rounded-xl border border-border bg-card p-6">
                        <div className="mb-4 text-sm font-medium">
                            AI conversation
                        </div>
                        <div className="space-y-3">
                            {messages.map((message, index) => (
                                <div
                                    key={`${message.role}-${index}`}
                                    className={`rounded-lg px-3 py-2 text-sm ${
                                        message.role === 'user'
                                            ? 'bg-primary text-primary-foreground'
                                            : 'bg-muted text-foreground'
                                    }`}
                                >
                                    {message.content}
                                </div>
                            ))}
                        </div>
                        <div className="mt-4 flex items-center gap-2">
                            <Input placeholder="Type a follow-up..." />
                            <Button size="sm">Send</Button>
                        </div>
                    </div>
                ) : (
                    <div className="rounded-xl border border-border bg-card p-6 text-sm text-muted-foreground">
                        No results yet. Use filters and run a search.
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
