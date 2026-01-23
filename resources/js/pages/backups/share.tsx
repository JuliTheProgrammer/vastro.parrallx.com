import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { Head, usePage } from '@inertiajs/react';
import { MoreHorizontal } from 'lucide-react';
import { useMemo, useState } from 'react';

type Step = 1 | 2;

const PAGE_SIZE = 50;

export default function ShareBackups() {
    const { props } = usePage<{
        links?:
            | Array<{
                  id: number | string;
                  uuid?: string;
                  name: string;
                  expires_at?: string | null;
              }>
            | {
                  data?: Array<{
                      id: number | string;
                      uuid?: string;
                      name: string;
                      expires_at?: string | null;
                  }>;
                  current_page?: number;
                  last_page?: number;
                  total?: number;
                  meta?: {
                      current_page?: number;
                      last_page?: number;
                      total?: number;
                  };
              };
        vaults?: Array<{ id: number | string; name: string }>;
    }>();
    const dataVaults = props.vaults ?? [];
    const paginated =
        props.links && !Array.isArray(props.links) ? props.links : null;
    const paginatedData = Array.isArray(paginated?.data)
        ? paginated?.data
        : null;
    const rawLinks =
        paginatedData && paginatedData.length
            ? paginatedData
            : Array.isArray(props.links) && props.links.length
              ? props.links
              : [];
    const links = rawLinks.map((link) => ({
        id: typeof link.id !== 'undefined' ? link.id : link.uuid ?? '',
        name: link.name,
        expires: link.expires_at ?? '—',
    }));
    const [step, setStep] = useState<Step>(1);
    const [vault, setVault] = useState('');
    const [name, setName] = useState('');
    const [expiresAt, setExpiresAt] = useState('');
    const [page, setPage] = useState(
        paginated?.current_page ?? paginated?.meta?.current_page ?? 1,
    );

    const canContinue = step === 1 ? !!vault : !!(name && expiresAt);
    const totalPages = paginated?.last_page ?? paginated?.meta?.last_page
        ? (paginated?.last_page ?? paginated?.meta?.last_page)
        : Math.max(1, Math.ceil(links.length / PAGE_SIZE));
    const pageStart = (page - 1) * PAGE_SIZE;
    const paginatedLinks = paginated
        ? links
        : links.slice(pageStart, pageStart + PAGE_SIZE);

    const summary = useMemo(() => {
        return [
            vault && `Vault: ${vault}`,
            name && `Name: ${name}`,
            expiresAt && `Expires: ${expiresAt}`,
        ]
            .filter(Boolean)
            .join(' · ');
    }, [vault, name, expiresAt]);

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Backups', href: '/backups' },
                { title: 'Share Backups', href: '/backups/share' },
            ]}
        >
            <Head title="Share Backups" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div>
                    <h1 className="text-lg font-semibold text-foreground">
                        Share Backups
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Create controlled links and manage existing access.
                    </p>
                </div>

                <div className="rounded-xl border border-border bg-card p-6">
                    <div className="flex items-center gap-2 text-sm">
                        <span
                            className={`flex h-7 w-7 items-center justify-center rounded-full border ${
                                step === 1
                                    ? 'border-primary bg-primary text-primary-foreground'
                                    : 'border-border text-muted-foreground'
                            }`}
                        >
                            1
                        </span>
                        <span className={step === 1 ? 'font-medium' : 'text-muted-foreground'}>
                            Choose data vault
                        </span>
                        <div className="h-px w-8 bg-border" />
                        <span
                            className={`flex h-7 w-7 items-center justify-center rounded-full border ${
                                step === 2
                                    ? 'border-primary bg-primary text-primary-foreground'
                                    : 'border-border text-muted-foreground'
                            }`}
                        >
                            2
                        </span>
                        <span className={step === 2 ? 'font-medium' : 'text-muted-foreground'}>
                            Configure access
                        </span>
                    </div>

                    <div className="mt-6">
                        {step === 1 && (
                            <div className="space-y-2">
                                <Label>Data vault</Label>
                                <Select value={vault} onValueChange={setVault}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select data vault" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {dataVaults.map((item) => (
                                            <SelectItem
                                                key={item.id}
                                                value={String(item.id)}
                                            >
                                                {item.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                        )}

                        {step === 2 && (
                            <div className="grid gap-4 md:grid-cols-2">
                                <div className="space-y-2">
                                    <Label>Name</Label>
                                    <Input
                                        value={name}
                                        onChange={(event) => setName(event.target.value)}
                                        placeholder="e.g. Partner review"
                                    />
                                </div>
                                <div className="space-y-2">
                                    <Label>Expires</Label>
                                    <Input
                                        type="date"
                                        value={expiresAt}
                                        onChange={(event) => setExpiresAt(event.target.value)}
                                    />
                                </div>
                            </div>
                        )}
                    </div>

                    <div className="mt-6 flex flex-wrap items-center gap-3">
                        {step === 2 && (
                            <Button variant="outline" onClick={() => setStep(1)}>
                                Back
                            </Button>
                        )}
                        {step === 1 && (
                            <Button onClick={() => setStep(2)} disabled={!canContinue}>
                                Continue
                            </Button>
                        )}
                        {step === 2 && (
                            <Button disabled={!canContinue}>Create share link</Button>
                        )}
                        {summary && (
                            <div className="text-xs text-muted-foreground">{summary}</div>
                        )}
                    </div>
                </div>

                <div className="rounded-xl border border-border bg-card">
                    <div className="border-b border-border px-4 py-3 text-sm font-medium">
                        Active share links
                    </div>
                    <div className="max-h-[520px] overflow-auto">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Name / ID</TableHead>
                                    <TableHead>Expires</TableHead>
                                    <TableHead className="text-right">
                                        Actions
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {paginatedLinks.map((link) => (
                                    <TableRow key={link.id}>
                                        <TableCell className="font-medium">
                                            <div>{link.name}</div>
                                        </TableCell>
                                        <TableCell className="text-sm text-muted-foreground">
                                            {link.expires}
                                        </TableCell>
                                        <TableCell className="text-right">
                                            <DropdownMenu>
                                                <DropdownMenuTrigger asChild>
                                                    <Button variant="ghost" size="icon">
                                                        <MoreHorizontal className="h-4 w-4" />
                                                        <span className="sr-only">
                                                            Link actions
                                                        </span>
                                                    </Button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent align="end">
                                                    <DropdownMenuItem>
                                                        Revoke access
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem>
                                                        Delete link
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </div>
                    <div className="flex flex-col items-start gap-3 border-t border-border px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <div className="text-xs text-muted-foreground">
                            Showing {Math.min(PAGE_SIZE, links.length - pageStart)} of{' '}
                            {paginated?.total ?? paginated?.meta?.total ?? links.length}{' '}
                            links
                        </div>
                        <div className="flex items-center gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={page <= 1}
                                onClick={() => setPage((current) => Math.max(1, current - 1))}
                            >
                                Previous
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={page >= totalPages}
                                onClick={() =>
                                    setPage((current) =>
                                        Math.min(
                                            Number(totalPages) || 1,
                                            current + 1,
                                        ),
                                    )
                                }
                            >
                                Next
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
