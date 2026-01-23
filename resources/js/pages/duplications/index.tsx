import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
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
import ReactFlow, { Background, Controls } from 'reactflow';
import 'reactflow/dist/style.css';

type Step = 1 | 2;

const vaults = [
    { id: 'vault-01', name: 'Primary Vault', location: 'Frankfurt' },
    { id: 'vault-02', name: 'Compliance Vault', location: 'Dublin' },
    { id: 'vault-03', name: 'Legacy Vault', location: 'Virginia' },
];

const duplications = [
    {
        id: 'dup-01',
        name: 'Primary to EU',
        source: 'Primary VaultAction',
        destination: 'Compliance VaultAction',
        lastSynced: '2024-11-12 09:32',
    },
    {
        id: 'dup-02',
        name: 'Legacy mirror',
        source: 'Legacy VaultAction',
        destination: 'Primary VaultAction',
        lastSynced: '2024-11-09 18:04',
    },
];

export default function DataDuplications() {
    const [step, setStep] = useState<Step>(1);
    const [sourceVault, setSourceVault] = useState('');
    const [destinations, setDestinations] = useState<string[]>([]);

    const availableDestinations = vaults.filter((vault) => vault.name !== sourceVault);
    const canContinue = step === 1 ? !!sourceVault : destinations.length > 0;

    const summary = useMemo(() => {
        if (!sourceVault) {
            return '';
        }
        if (destinations.length === 0) {
            return `Source: ${sourceVault}`;
        }
        return `Source: ${sourceVault} · Destinations: ${destinations.join(', ')}`;
    }, [sourceVault, destinations]);

    return (
        <AppLayout breadcrumbs={[{ title: 'Data Duplications', href: '/duplications' }]}>
            <Head title="Data Duplications" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div>
                    <h1 className="text-lg font-semibold text-foreground">
                        Data Duplications
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Create and monitor replication paths between vaults.
                    </p>
                </div>

                <div className="grid gap-6 lg:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)]">
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
                                Choose source vault
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
                                Select destinations
                            </span>
                        </div>

                        <div className="mt-6">
                            {step === 1 && (
                                <div className="space-y-2">
                                    <Label>Source vault</Label>
                                    <Select value={sourceVault} onValueChange={(value) => {
                                        setSourceVault(value);
                                        setDestinations([]);
                                    }}>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select source vault" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {vaults.map((vault) => (
                                                <SelectItem key={vault.id} value={vault.name}>
                                                    {vault.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>
                            )}

                            {step === 2 && (
                                <div className="space-y-3">
                                    <Label>Destination vaults</Label>
                                    <div className="grid gap-3 md:grid-cols-2">
                                        {availableDestinations.map((vault) => (
                                            <label
                                                key={vault.id}
                                                className="flex items-center gap-2 rounded-lg border border-border bg-muted/40 p-3 text-sm"
                                            >
                                                <Checkbox
                                                    checked={destinations.includes(vault.name)}
                                                    onCheckedChange={(value) => {
                                                        setDestinations((current) => {
                                                            if (value) {
                                                                return [...current, vault.name];
                                                            }
                                                            return current.filter((item) => item !== vault.name);
                                                        });
                                                    }}
                                                />
                                                <div>
                                                    <div className="font-medium text-foreground">
                                                        {vault.name}
                                                    </div>
                                                    <div className="text-xs text-muted-foreground">
                                                        {vault.location}
                                                    </div>
                                                </div>
                                            </label>
                                        ))}
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
                                <Button disabled={!canContinue}>Create duplication</Button>
                            )}
                            {summary && (
                                <div className="text-xs text-muted-foreground">{summary}</div>
                            )}
                        </div>
                    </div>

                    <div className="rounded-xl border border-border bg-card p-6">
                        <div className="text-sm font-medium">Connection map</div>
                        <div className="mt-4 h-[260px] overflow-hidden rounded-lg border border-border bg-muted/30">
                            <ReactFlow
                                nodes={[
                                    {
                                        id: 'primary',
                                        data: { label: 'Primary VaultAction' },
                                        position: { x: 40, y: 60 },
                                    },
                                    {
                                        id: 'compliance',
                                        data: { label: 'Compliance VaultAction' },
                                        position: { x: 220, y: 20 },
                                    },
                                    {
                                        id: 'legacy',
                                        data: { label: 'Legacy VaultAction' },
                                        position: { x: 220, y: 160 },
                                    },
                                    {
                                        id: 'archive',
                                        data: { label: 'Archive VaultAction' },
                                        position: { x: 420, y: 90 },
                                    },
                                ]}
                                edges={[
                                    { id: 'e1', source: 'primary', target: 'compliance', animated: true },
                                    { id: 'e2', source: 'primary', target: 'legacy', animated: true },
                                    { id: 'e3', source: 'compliance', target: 'archive' },
                                    { id: 'e4', source: 'legacy', target: 'archive' },
                                ]}
                                fitView
                                nodesDraggable={false}
                                nodesConnectable={false}
                                panOnDrag
                            >
                                <Background gap={24} size={1} />
                                <Controls showInteractive={false} />
                            </ReactFlow>
                        </div>
                        <p className="mt-3 text-xs text-muted-foreground">
                            Connections visualize replication paths between vault nodes.
                        </p>
                    </div>
                </div>

                <div className="rounded-xl border border-border bg-card">
                    <div className="border-b border-border px-4 py-3 text-sm font-medium">
                        Active duplications
                    </div>
                    <div className="max-h-[420px] overflow-auto">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Source Data Vault</TableHead>
                                    <TableHead>Destination Data Vault</TableHead>
                                    <TableHead>Last synced</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {duplications.map((row) => (
                                    <TableRow key={row.id}>
                                        <TableCell className="font-medium">
                                            {row.name}
                                        </TableCell>
                                        <TableCell>{row.source}</TableCell>
                                        <TableCell>{row.destination}</TableCell>
                                        <TableCell className="text-sm text-muted-foreground">
                                            {row.lastSynced}
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
