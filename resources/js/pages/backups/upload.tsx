import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import { Head, usePage } from '@inertiajs/react';
import { FileText, Image as ImageIcon, Trash2 } from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';

type Step = 1 | 2;

const storageClasses = ['Standard', 'Infrequent', 'Archive'];
export default function UploadBackup() {
    const { props } = usePage<{
        vaults?: Array<{ id: number | string; name: string }>;
        folders?: Array<{
            id: number | string;
            name: string;
            folderable_type: string;
            folderable_id: number | string;
        }>;
    }>();
    const dataVaults = props.vaults ?? [];
    const allFolders = props.folders ?? [];
    const [step, setStep] = useState<Step>(1);
    const [files, setFiles] = useState<File[]>([]);
    const [storageClass, setStorageClass] = useState('');
    const [dataVault, setDataVault] = useState('');
    const [folder, setFolder] = useState('');
    const [isCreateFolderOpen, setIsCreateFolderOpen] = useState(false);

    const hasFiles = useMemo(() => files.length > 0, [files]);
    const canContinue =
        step === 1 ? hasFiles : storageClass && dataVault && folder;
    const totalBytes = useMemo(
        () => files.reduce((sum, file) => sum + file.size, 0),
        [files],
    );
    const totalGB = totalBytes / (1024 * 1024 * 1024);
    const formattedTotalGB = totalGB.toFixed(2);
    const formatCurrency = (value: number) =>
        new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            maximumFractionDigits: 2,
        }).format(value);

    const previews = useMemo(() => {
        return files.map((file) => {
            if (file.type.startsWith('image/')) {
                return URL.createObjectURL(file);
            }
            return null;
        });
    }, [files]);

    useEffect(() => {
        return () => {
            previews.forEach((preview) => {
                if (preview) {
                    URL.revokeObjectURL(preview);
                }
            });
        };
    }, [previews]);

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Backups', href: '/backups' },
                { title: 'Upload Backups', href: '/backups/upload' },
            ]}
        >
            <Head title="Upload Backups" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div>
                    <h1 className="text-lg font-semibold text-foreground">
                        Upload Backups
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Complete each step before uploading your backup files.
                    </p>
                </div>

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
                        Choose files
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
                        Configure upload
                    </span>
                </div>

                {step === 1 && (
                    <div className="rounded-xl border border-border bg-card p-6">
                        <div className="space-y-2">
                            <div className="flex items-center gap-3">
                                <Button variant="outline" asChild>
                                    <label htmlFor="backup-files">
                                        Choose files
                                    </label>
                                </Button>
                                {hasFiles ? (
                                    <span className="text-xs text-muted-foreground">
                                        {files.length} selected
                                    </span>
                                ) : null}
                                <Input
                                    id="backup-files"
                                    type="file"
                                    multiple
                                    className="sr-only"
                                    onChange={(event) => {
                                        const nextFiles = Array.from(
                                            event.target.files ?? [],
                                        );
                                        setFiles((current) => [
                                            ...current,
                                            ...nextFiles,
                                        ]);
                                        event.target.value = '';
                                    }}
                                />
                            </div>
                            <p className="text-xs text-muted-foreground">
                                Select one or more files to upload.
                            </p>
                        </div>
                        {hasFiles && (
                            <div className="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                {files.map((file, index) => (
                                    <div
                                        key={`${file.name}-${file.lastModified}`}
                                        className="flex items-center gap-3 rounded-lg border border-border bg-muted/40 p-3"
                                    >
                                        <div className="flex h-14 w-14 items-center justify-center rounded-md bg-background">
                                            {previews[index] ? (
                                                <img
                                                    src={
                                                        previews[index] ??
                                                        undefined
                                                    }
                                                    alt={file.name}
                                                    className="h-12 w-12 rounded object-cover"
                                                />
                                            ) : (
                                                <span className="text-muted-foreground">
                                                    {file.type ? (
                                                        <FileText className="h-6 w-6" />
                                                    ) : (
                                                        <ImageIcon className="h-6 w-6" />
                                                    )}
                                                </span>
                                            )}
                                        </div>
                                        <div className="min-w-0 flex-1">
                                            <div className="truncate text-sm font-medium">
                                                {file.name}
                                            </div>
                                            <div className="text-xs text-muted-foreground">
                                                {(
                                                    file.size /
                                                    (1024 * 1024)
                                                ).toFixed(2)}{' '}
                                                MB
                                            </div>
                                        </div>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            onClick={() => {
                                                setFiles((current) =>
                                                    current.filter(
                                                        (_, fileIndex) =>
                                                            fileIndex !== index,
                                                    ),
                                                );
                                            }}
                                        >
                                            <Trash2 className="h-4 w-4" />
                                            <span className="sr-only">
                                                Remove file
                                            </span>
                                        </Button>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                )}

                {step === 2 && (
                    <div className="rounded-xl border border-border bg-card p-6">
                        <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                            <div className="space-y-2">
                                <Label>Data vault</Label>
                                <Select
                                    value={dataVault}
                                    onValueChange={setDataVault}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select vault" />
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
                            <div className="space-y-2">
                                <Label>Folder</Label>
                                <Select
                                    value={folder}
                                    onValueChange={setFolder}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select folder" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {allFolders
                                            .filter((item) =>
                                                dataVault
                                                    ? item.folderable_type ===
                                                          'App\\Models\\Vault' &&
                                                      String(item.folderable_id) ===
                                                          dataVault
                                                    : true,
                                            )
                                            .map((item) => (
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
                            <div className="space-y-2">
                                <Label>Storage class</Label>
                                <Select
                                    value={storageClass}
                                    onValueChange={setStorageClass}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select class" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {storageClasses.map((item) => (
                                            <SelectItem key={item} value={item}>
                                                {item}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                        <div className="mt-6 rounded-lg border border-dashed border-border bg-muted/40 p-4 text-sm text-muted-foreground">
                            Files selected: {files.length} · Total size:{' '}
                            {formattedTotalGB} GB
                        </div>
                    </div>
                )}

                <div className="flex flex-wrap items-center gap-3">
                    {step === 2 && (
                        <Button variant="outline" onClick={() => setStep(1)}>
                            Back
                        </Button>
                    )}
                    {step === 1 && (
                        <Button
                            onClick={() => setStep(2)}
                            disabled={!canContinue}
                        >
                            Continue
                        </Button>
                    )}
                    {step === 2 && (
                        <Button disabled={!canContinue}>Upload backup</Button>
                    )}
                </div>
                <Dialog
                    open={isCreateFolderOpen}
                    onOpenChange={setIsCreateFolderOpen}
                >
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Create folder</DialogTitle>
                            <DialogDescription>
                                Add a name and set a folder destination.
                            </DialogDescription>
                        </DialogHeader>
                        <div className="grid gap-4">
                            <div className="space-y-2">
                                <Label htmlFor="upload-folder-name">
                                    Folder name
                                </Label>
                                <Input
                                    id="upload-folder-name"
                                    placeholder="e.g. Q2 backup set"
                                    required
                                />
                            </div>
                            <div className="space-y-2">
                                <Label>Sub folder / vault</Label>
                                <Select required>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select destination" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {allFolders.map((item) => (
                                            <SelectItem
                                                key={`folder-${item.id}`}
                                                value={`folder:${item.id}`}
                                            >
                                                {item.name}
                                            </SelectItem>
                                        ))}
                                        {dataVaults.map((item) => (
                                            <SelectItem
                                                key={`vault-${item.id}`}
                                                value={`vault:${item.id}`}
                                            >
                                                {item.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="space-y-2">
                                <Label>Storage class (optional)</Label>
                                <Select>
                                    <SelectTrigger>
                                        <SelectValue placeholder="No preference" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {storageClasses.map((item) => (
                                            <SelectItem key={item} value={item}>
                                                {item}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                        <DialogFooter>
                            <Button
                                variant="outline"
                                onClick={() => setIsCreateFolderOpen(false)}
                            >
                                Cancel
                            </Button>
                            <Button>Create folder</Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>
            </div>
        </AppLayout>
    );
}
