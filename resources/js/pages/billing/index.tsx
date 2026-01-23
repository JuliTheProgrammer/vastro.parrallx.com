import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

const invoices = [
    { id: 'inv-021', date: '2024-11-01', amount: '$24.00', status: 'Paid' },
    { id: 'inv-020', date: '2024-10-01', amount: '$24.00', status: 'Paid' },
    { id: 'inv-019', date: '2024-09-01', amount: '$24.00', status: 'Paid' },
];

const pricingByRegion = {
    'us-east-1': {
        label: 'US East (N. Virginia)',
        prices: {
            standard: 0.028,
            infrequent: 0.015,
            instant: 0.0048,
            deepArchive: 0.0012,
        },
    },
    'us-west-2': {
        label: 'US West (Oregon)',
        prices: {
            standard: 0.030,
            infrequent: 0.016,
            instant: 0.0052,
            deepArchive: 0.0014,
        },
    },
    'eu-central-1': {
        label: 'EU (Frankfurt)',
        prices: {
            standard: 0.032,
            infrequent: 0.017,
            instant: 0.0056,
            deepArchive: 0.0016,
        },
    },
    'eu-west-1': {
        label: 'EU (Ireland)',
        prices: {
            standard: 0.031,
            infrequent: 0.0165,
            instant: 0.0054,
            deepArchive: 0.0015,
        },
    },
};

const storageClasses = [
    {
        key: 'standard',
        label: 'Standard',
        note: 'Frequent access',
    },
    {
        key: 'infrequent',
        label: 'Infrequent Access',
        note: 'Lower cost, higher retrieval fees',
    },
    {
        key: 'instant',
        label: 'Glacier Instant Retrieval',
        note: 'Archive, fast access',
    },
    {
        key: 'deepArchive',
        label: 'Glacier Deep Archive',
        note: 'Long-term archive',
    },
];

export default function BillingIndex() {
    const [pricingRegion, setPricingRegion] =
        useState<keyof typeof pricingByRegion>('us-east-1');
    const region = pricingByRegion[pricingRegion];

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Account', href: '#' },
                { title: 'Billing', href: '/billing' },
            ]}
        >
            <Head title="Billing" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div>
                    <h1 className="text-lg font-semibold text-foreground">
                        Billing
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Manage your subscription and payment details.
                    </p>
                </div>

                <div className="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                    <Card>
                        <CardHeader>
                            <CardTitle>Current plan</CardTitle>
                            <CardDescription>
                                Subscription status and renewal date.
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="flex items-center justify-between">
                                <div>
                                    <div className="text-lg font-semibold">
                                        Pro Plan
                                    </div>
                                    <div className="text-sm text-muted-foreground">
                                        Renews on Dec 1, 2024
                                    </div>
                                </div>
                                <Badge className="bg-emerald-500/10 text-emerald-600">
                                    Active
                                </Badge>
                            </div>
                            <div className="rounded-lg border border-border bg-muted/30 p-4 text-sm text-muted-foreground">
                                Next charge: $24.00 · Visa ending in 4242
                            </div>
                        </CardContent>
                        <CardFooter className="gap-3">
                            <Button variant="outline">Update card</Button>
                            <Button variant="ghost">Cancel plan</Button>
                        </CardFooter>
                    </Card>

                    <Card className="border-primary/30 bg-primary/5">
                        <CardHeader>
                            <CardTitle>Usage & limits</CardTitle>
                            <CardDescription>
                                Keep track of your Pro allocation.
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-3 text-sm text-muted-foreground">
                            <div className="flex items-center justify-between">
                                <span>Vaults</span>
                                <span className="font-medium text-foreground">
                                    5 / Unlimited
                                </span>
                            </div>
                            <div className="flex items-center justify-between">
                                <span>Shared links</span>
                                <span className="font-medium text-foreground">
                                    8 active
                                </span>
                            </div>
                            <div className="flex items-center justify-between">
                                <span>Encryption profile</span>
                                <span className="font-medium text-foreground">
                                    Custom policy
                                </span>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Storage pricing</CardTitle>
                        <CardDescription>
                            Pricing varies by storage location. Select a region
                            to preview rates.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div className="text-sm text-muted-foreground">
                                Prices can change based on where your data is
                                stored.
                            </div>
                            <div className="w-full sm:w-72">
                                <Select
                                    value={pricingRegion}
                                    onValueChange={(value) =>
                                        setPricingRegion(
                                            value as keyof typeof pricingByRegion,
                                        )
                                    }
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select region" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {Object.entries(pricingByRegion).map(
                                            ([key, data]) => (
                                                <SelectItem key={key} value={key}>
                                                    {data.label}
                                                </SelectItem>
                                            ),
                                        )}
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Storage class</TableHead>
                                    <TableHead>Price</TableHead>
                                    <TableHead>Notes</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {storageClasses.map((row) => (
                                    <TableRow key={row.key}>
                                        <TableCell className="font-medium">
                                            {row.label}
                                        </TableCell>
                                        <TableCell className="text-sm text-foreground">
                                            ${region.prices[row.key]} per GB-month
                                        </TableCell>
                                        <TableCell className="text-sm text-muted-foreground">
                                            {row.note}
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Invoices</CardTitle>
                        <CardDescription>
                            Download receipts for past payments.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Invoice</TableHead>
                                    <TableHead>Date</TableHead>
                                    <TableHead>Amount</TableHead>
                                    <TableHead className="text-right">
                                        Status
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {invoices.map((invoice) => (
                                    <TableRow key={invoice.id}>
                                        <TableCell className="font-medium">
                                            {invoice.id}
                                        </TableCell>
                                        <TableCell className="text-sm text-muted-foreground">
                                            {invoice.date}
                                        </TableCell>
                                        <TableCell className="text-sm text-muted-foreground">
                                            {invoice.amount}
                                        </TableCell>
                                        <TableCell className="text-right text-sm text-muted-foreground">
                                            {invoice.status}
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
