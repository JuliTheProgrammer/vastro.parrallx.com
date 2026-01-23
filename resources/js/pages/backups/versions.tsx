import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Lock, RotateCcw } from 'lucide-react';

const versions = [
    {
        id: 'v7',
        label: 'Version 7',
        created: '2024-11-12 09:14',
        size: '2.4 GB',
        status: 'versioned',
    },
    {
        id: 'v6',
        label: 'Version 6',
        created: '2024-11-10 14:02',
        size: '2.1 GB',
        status: 'versioned',
    },
    {
        id: 'v5',
        label: 'Version 5',
        created: '2024-11-08 18:31',
        size: '2.0 GB',
        status: 'versioned',
    },
    {
        id: 'v4',
        label: 'Version 4',
        created: '2024-11-05 11:45',
        size: '1.9 GB',
        status: 'versioned',
    },
];

export default function BackupVersions() {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Backups', href: '/backups' },
                { title: 'Versions', href: '/backups/versions' },
            ]}
        >
            <Head title="Backup Versions" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div className="flex flex-col gap-2">
                    <h1 className="text-lg font-semibold text-foreground">
                        Versions
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Review and restore previous versions of this backup.
                    </p>
                </div>

                <div className="rounded-xl border border-border bg-card">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Version</TableHead>
                                <TableHead>Created</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Size</TableHead>
                                <TableHead className="text-right">
                                    Actions
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {versions.map((version) => (
                                <TableRow key={version.id}>
                                    <TableCell className="font-medium">
                                        {version.label}
                                    </TableCell>
                                    <TableCell className="text-sm text-muted-foreground">
                                        {version.created}
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex items-center gap-2 text-sm text-muted-foreground">
                                            <Lock className="h-4 w-4" />
                                            <span className="capitalize">
                                                {version.status}
                                            </span>
                                        </div>
                                    </TableCell>
                                    <TableCell className="text-sm text-muted-foreground">
                                        {version.size}
                                    </TableCell>
                                    <TableCell className="text-right">
                                        <Button variant="outline" size="sm">
                                            <RotateCcw className="mr-2 h-4 w-4" />
                                            Restore
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>
            </div>
        </AppLayout>
    );
}
