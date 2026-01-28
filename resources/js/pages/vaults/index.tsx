import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
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
import { toUrl } from '@/lib/utils';
import { store } from '@/routes/vaults';
import { Head, useForm } from '@inertiajs/react';
import { useMemo, useState } from 'react';

type Step = 1 | 2;

type Vault = {
    id: string | number;
    name: string;
    worm: boolean;
    encryption: boolean;
    versioning: boolean;
    deleteProtection: boolean;
    location: string;
};

type Props = {
    vaults: Vault[];
};

const locations = [
    'Frankfurt (eu-central-1)',
    'Dublin (eu-west-1)',
    'Virginia (us-east-1)',
];
export default function DataVaults({ vaults = [] }: Props) {
    const [step, setStep] = useState<Step>(1);

    const {
        data,
        setData,
        post,
        processing,
        errors,
        reset,
        recentlySuccessful,
    } = useForm({
        name: '',
        location: '',
        region: '',
        wormProtection: false,
        encryption: false,
        deleteProtection: false,
    });

    const canContinue =
        step === 1 ? Boolean(data.name && data.location) : true;

    const summary = useMemo(() => {
        const items = [
            data.name && `Name: ${data.name}`,
            data.location && `Location: ${data.location}`,
            data.wormProtection && 'WORM Protection',
            data.encryption && 'Custom Encryption Key',
            data.deleteProtection && 'Delete Protection',
        ].filter(Boolean);

        return items.join(' · ');
    }, [
        data.name,
        data.location,
        data.wormProtection,
        data.encryption,
        data.deleteProtection,
    ]);

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();

        post(toUrl(store()), {
            preserveScroll: true,
            onSuccess: () => {
                reset();
                setStep(1);
            },
            onError: () => {
                // If validation fails on step 1 fields, jump back to step 1 automatically
                if (errors.name || errors.location || errors.region) setStep(1);
            },
        });
    }

    return (
        <AppLayout breadcrumbs={[{ title: 'Data Vaults', href: '/vaults' }]}>
            <Head title="Data Vaults" />

            <div className="flex flex-1 flex-col gap-6 p-6">
                <div>
                    <h1 className="text-lg font-semibold text-foreground">
                        Data Vaults
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Each vault maps to a dedicated S3 bucket.
                    </p>
                </div>

                <form
                    className="rounded-xl border border-border bg-card p-6"
                    onSubmit={handleSubmit}
                >
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
                        <span
                            className={
                                step === 1 ? 'font-medium' : 'text-muted-foreground'
                            }
                        >
                            Vault details
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
                        <span
                            className={
                                step === 2 ? 'font-medium' : 'text-muted-foreground'
                            }
                        >
                            Protections
                        </span>
                    </div>

                    <div className="mt-6">
                        {step === 1 && (
                            <div className="grid gap-4 md:grid-cols-2">
                                <div className="space-y-2">
                                    <Label>Vault name</Label>
                                    <Input
                                        name="name"
                                        value={data.name}
                                        onChange={(e) =>
                                            setData('name', e.target.value)
                                        }
                                        placeholder="e.g. Primary Vault"
                                    />
                                    {errors.name && (
                                        <p className="text-xs text-destructive">
                                            {errors.name}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label>Location</Label>
                                    <Select
                                        value={data.location}
                                        onValueChange={(value) => {
                                            setData('location', value);
                                            const match = value.match(/\(([^)]+)\)/);
                                            if (match?.[1]) {
                                                setData('region', match[1]);
                                            }
                                        }}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select location" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {locations.map((item) => (
                                                <SelectItem
                                                    key={item}
                                                    value={item}
                                                >
                                                    {item}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.location && (
                                        <p className="text-xs text-destructive">
                                            {errors.location}
                                        </p>
                                    )}
                                </div>

                            </div>
                        )}

                        {step === 2 && (
                            <div className="grid gap-4 md:grid-cols-2">
                                <label className="flex items-center gap-2 text-sm">
                                    <Checkbox
                                        checked={data.wormProtection}
                                        onCheckedChange={(v) =>
                                            setData(
                                                'wormProtection',
                                                Boolean(v),
                                            )
                                        }
                                    />
                                    <span>WORM Protection</span>
                                    <span className="text-xs text-muted-foreground">
                                        Recommended
                                    </span>
                                </label>

                                <label className="flex items-center gap-2 text-sm">
                                    <Checkbox
                                        checked={data.encryption}
                                        onCheckedChange={(v) =>
                                            setData('encryption', Boolean(v))
                                        }
                                    />
                                    <span>Custom Encryption Key</span>
                                    <span className="text-xs text-muted-foreground">
                                        Only for PRO · Recommended
                                    </span>
                                </label>

                                <label className="flex items-center gap-2 text-sm">
                                    <Checkbox
                                        checked={data.deleteProtection}
                                        onCheckedChange={(v) =>
                                            setData(
                                                'deleteProtection',
                                                Boolean(v),
                                            )
                                        }
                                    />
                                    <span>Delete Protection</span>
                                    <span className="text-xs text-muted-foreground">
                                        Recommended
                                    </span>
                                </label>

                                {/* show backend validation if you have it */}
                                {errors.wormProtection && (
                                    <p className="text-xs text-destructive">
                                        {errors.wormProtection}
                                    </p>
                                )}
                                {errors.encryption && (
                                    <p className="text-xs text-destructive">
                                        {errors.encryption}
                                    </p>
                                )}
                                {errors.deleteProtection && (
                                    <p className="text-xs text-destructive">
                                        {errors.deleteProtection}
                                    </p>
                                )}
                            </div>
                        )}
                    </div>

                    <div className="mt-6 flex flex-wrap items-center gap-3">
                        {step === 2 && (
                            <Button
                                variant="outline"
                                type="button"
                                onClick={() => setStep(1)}
                                disabled={processing}
                            >
                                Back
                            </Button>
                        )}

                        {step === 1 && (
                            <Button
                                type="button"
                                onClick={() => setStep(2)}
                                disabled={!canContinue || processing}
                            >
                                Continue
                            </Button>
                        )}

                        {step === 2 && (
                            <Button type="submit" disabled={processing}>
                                {processing ? 'Creating…' : 'Create data vault'}
                            </Button>
                        )}

                        {summary && (
                            <div className="text-xs text-muted-foreground">
                                {summary}
                            </div>
                        )}
                        {recentlySuccessful && (
                            <div className="text-xs text-muted-foreground">
                                Vault Created
                            </div>
                        )}
                    </div>
                </form>

                <div className="rounded-xl border border-border bg-card">
                    <div className="border-b border-border px-4 py-3 text-sm font-medium">
                        Existing vaults
                    </div>
                    <div className="max-h-[420px] overflow-auto">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Name</TableHead>
                                    <TableHead>WORM Protection</TableHead>
                                    <TableHead>Custom Encryption Key</TableHead>
                                    <TableHead>Versioning</TableHead>
                                    <TableHead>Delete Protection</TableHead>
                                    <TableHead>Location</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {vaults.map((vault) => (
                                    <TableRow key={vault.id}>
                                        <TableCell className="font-medium">
                                            {vault.name}
                                        </TableCell>
                                        <TableCell>
                                            {vault.worm_protection ? 'Enabled' : 'Off'}
                                        </TableCell>
                                        <TableCell>
                                            {vault.encryption
                                                ? 'Enabled'
                                                : 'Off'}
                                        </TableCell>
                                        <TableCell>
                                            {vault.versioning
                                                ? 'Enabled'
                                                : 'Off'}
                                        </TableCell>
                                        <TableCell>
                                            {vault.delete_protection
                                                ? 'Enabled'
                                                : 'Off'}
                                        </TableCell>
                                        <TableCell className="text-sm text-muted-foreground">
                                            {vault.location}
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
