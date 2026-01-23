import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
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

const severityStyles: Record<string, string> = {
    info: 'bg-muted text-muted-foreground',
    success: 'bg-emerald-500/10 text-emerald-600',
    warning: 'bg-amber-500/10 text-amber-700',
};

const PAGE_SIZE = 100;

type AuditLog = {
    id: number | string;
    description: string;
    subject_type?: string | null;
    subject_id?: number | string | null;
    created_at?: string | null;
    log_name?: string | null;
};

type Props = {
    auditLogs?: AuditLog[];
};

export default function AuditsIndex({ auditLogs = [] }: Props) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Backup Management', href: '/backups' },
                { title: 'Audit Logs', href: '/audits' },
            ]}
        >
            <Head title="Audit Logs" />
            <div className="flex min-h-0 flex-1 flex-col gap-6 p-6">
                <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 className="text-lg font-semibold text-foreground">
                            Audit Logs
                        </h1>
                        <p className="text-sm text-muted-foreground">
                            All system activity and vault actions are stored
                            here.
                        </p>
                    </div>
                    <div className="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                        <Input
                            placeholder="Search logs..."
                            className="w-full sm:w-64"
                        />
                        <Select>
                            <SelectTrigger className="w-full sm:w-44">
                                <SelectValue placeholder="Filter vault" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">All vaults</SelectItem>
                                <SelectItem value="primary">
                                    Primary VaultAction
                                </SelectItem>
                                <SelectItem value="compliance">
                                    Compliance VaultAction
                                </SelectItem>
                                <SelectItem value="legacy">
                                    Legacy VaultAction
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <div className="flex min-h-[calc(100vh-220px)] flex-1 flex-col rounded-xl border border-border bg-card">
                    <div className="border-b border-border px-4 py-3 text-sm font-medium">
                        Latest activity
                    </div>
                    <div className="flex-1 overflow-auto">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Event</TableHead>
                                    <TableHead>Actor</TableHead>
                                    <TableHead>Time</TableHead>
                                    <TableHead className="text-right">
                                        Status
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {auditLogs.map((log) => (
                                    <TableRow key={log.id}>
                                        <TableCell className="font-medium">
                                            {log.description}
                                        </TableCell>
                                        <TableCell className="text-sm text-muted-foreground">
                                            You
                                        </TableCell>
                                        <TableCell className="text-sm text-muted-foreground">
                                            {log.created_at ?? '—'}
                                        </TableCell>
                                        <TableCell className="text-right">
                                            <Badge
                                                className={
                                                    severityStyles[log.log_name ?? 'info'] ??
                                                    severityStyles.info
                                                }
                                            >
                                                {log.log_name ?? 'info'}
                                            </Badge>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </div>
                    <div className="flex flex-col items-start gap-3 border-t border-border px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <div className="text-xs text-muted-foreground">
                            Showing {Math.min(PAGE_SIZE, auditLogs.length)} of{' '}
                            {auditLogs.length} logs
                        </div>
                        <div className="flex items-center gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={auditLogs.length <= PAGE_SIZE}
                            >
                                Previous
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={auditLogs.length <= PAGE_SIZE}
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
