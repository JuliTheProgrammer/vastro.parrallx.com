import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import { toUrl } from '@/lib/utils';
import { Head, router, useForm, usePage } from '@inertiajs/react';
import { FileText, Image as ImageIcon, Trash2 } from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';

type Step = 1 | 2;

export default function UploadBackup() {
    const { props } = usePage<{
        vaults?: Array<{ id: number | string; name: string }>;
        storageClasses?: Array<{ id: number | string; name: string; storage_class: string }>;
        folders?: Array<{
            id: number | string;
            name: string;
            folderable_type: string;
            folderable_id: number | string;
        }>;
    }>();
    const dataVaults = props.vaults ?? [];
    const storageClasses = props.storageClasses ?? [];
    const allFolders = props.folders ?? [];
    const [step, setStep] = useState<Step>(1);
    const [storageClass, setStorageClass] = useState('');
    const [dataVault, setDataVault] = useState('');
    const [folder, setFolder] = useState('');
    const [selectedFiles, setSelectedFiles] = useState<File[]>([]);
    const [isUploading, setIsUploading] = useState(false);
    const [progress, setProgress] = useState(0);
    const [isCreateFolderOpen, setIsCreateFolderOpen] = useState(false);
    const { data, setData, post, processing, reset } = useForm<{
        vault_id: string;
        folder_id: string | null;
        storage_class: string | null;
        uploads: Array<{
            path: string;
            original_name: string;
            size: number;
            mime_type: string;
        }>;
    }>({
        vault_id: '',
        folder_id: null,
        storage_class: null,
        uploads: [],
    });

    const hasFiles = useMemo(() => selectedFiles.length > 0, [selectedFiles]);
    const canContinue = step === 1 ? hasFiles : Boolean(dataVault);
    const totalBytes = useMemo(
        () => selectedFiles.reduce((sum, file) => sum + file.size, 0),
        [selectedFiles],
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
        return selectedFiles.map((file) => {
            if (file.type.startsWith('image/')) {
                return URL.createObjectURL(file);
            }
            return null;
        });
    }, [selectedFiles]);

    useEffect(() => {
        return () => {
            previews.forEach((preview) => {
                if (preview) {
                    URL.revokeObjectURL(preview);
                }
            });
        };
    }, [previews]);

    const handleSubmit = async () => {
        if (isUploading) {
            return;
        }

        setData('vault_id', dataVault);
        setData('folder_id', folder || null);
        setData('storage_class', storageClass || null);

        setIsUploading(true);
        setProgress(0);

        try {
            const uploads = await uploadFilesInChunks(selectedFiles, dataVault, setProgress);
            setData('uploads', uploads);

            router.post(
                toUrl('/backups'),
                {
                    vault_id: dataVault,
                    folder_id: folder || null,
                    storage_class: storageClass || null,
                    uploads,
                },
                {
                    preserveScroll: true,
                    onFinish: () => {
                        setIsUploading(false);
                        setProgress(0);
                    },
                    onSuccess: () => {
                        reset();
                        setSelectedFiles([]);
                        setStorageClass('');
                        setDataVault('');
                        setFolder('');
                        setStep(1);
                    },
                },
            );
        } catch (error) {
            const message = error instanceof Error ? error.message : 'Upload failed';
            alert(message);
            setIsUploading(false);
            setProgress(0);
        }
    };

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
                    <h1 className="text-lg font-semibold text-foreground">Upload Backups</h1>
                    <p className="text-sm text-muted-foreground">Complete each step before uploading your backup files.</p>
                </div>

                <div className="flex items-center gap-2 text-sm">
                    <span
                        className={`flex h-7 w-7 items-center justify-center rounded-full border ${
                            step === 1 ? 'border-primary bg-primary text-primary-foreground' : 'border-border text-muted-foreground'
                        }`}
                    >
                        1
                    </span>
                    <span className={step === 1 ? 'font-medium' : 'text-muted-foreground'}>Choose files</span>
                    <div className="h-px w-8 bg-border" />
                    <span
                        className={`flex h-7 w-7 items-center justify-center rounded-full border ${
                            step === 2 ? 'border-primary bg-primary text-primary-foreground' : 'border-border text-muted-foreground'
                        }`}
                    >
                        2
                    </span>
                    <span className={step === 2 ? 'font-medium' : 'text-muted-foreground'}>Configure upload</span>
                </div>

                {step === 1 && (
                    <div className="rounded-xl border border-border bg-card p-6">
                        <div className="space-y-2">
                            <div className="flex items-center gap-3">
                                <Button variant="outline" asChild>
                                    <label htmlFor="backup-files">Choose files</label>
                                </Button>
                                {hasFiles ? (
                                    <span className="text-xs text-muted-foreground">{selectedFiles.length} selected</span>
                                ) : null}
                                <Input
                                    id="backup-files"
                                    type="file"
                                    multiple
                                    className="sr-only"
                                    onChange={(event) => {
                                        const nextFiles = Array.from(event.target.files ?? []);
                                        setSelectedFiles((current) => [...current, ...nextFiles]);
                                        event.target.value = '';
                                    }}
                                />
                            </div>
                            <p className="text-xs text-muted-foreground">Select one or more files to upload.</p>
                        </div>
                        {hasFiles && (
                            <div className="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                {selectedFiles.map((file, index) => (
                                    <div
                                        key={`${file.name}-${file.lastModified}`}
                                        className="flex items-center gap-3 rounded-lg border border-border bg-muted/40 p-3"
                                    >
                                        <div className="flex h-14 w-14 items-center justify-center rounded-md bg-background">
                                            {previews[index] ? (
                                                <img src={previews[index] ?? undefined} alt={file.name} className="h-12 w-12 rounded object-cover" />
                                            ) : (
                                                <span className="text-muted-foreground">
                                                    {file.type ? <FileText className="h-6 w-6" /> : <ImageIcon className="h-6 w-6" />}
                                                </span>
                                            )}
                                        </div>
                                        <div className="min-w-0 flex-1">
                                            <div className="truncate text-sm font-medium">{file.name}</div>
                                            <div className="text-xs text-muted-foreground">{(file.size / (1024 * 1024)).toFixed(2)} MB</div>
                                        </div>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            onClick={() => {
                                                setSelectedFiles((current) => current.filter((_, fileIndex) => fileIndex !== index));
                                            }}
                                        >
                                            <Trash2 className="h-4 w-4" />
                                            <span className="sr-only">Remove file</span>
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
                                    onValueChange={(value) => {
                                        setDataVault(value);
                                        setFolder('');
                                    }}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select vault" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {dataVaults.map((item) => (
                                            <SelectItem key={item.id} value={String(item.id)}>
                                                {item.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            {/*
                            <div className="space-y-2">
                                <Label>Folder</Label>
                                <Select
                                    value={folder}
                                    onValueChange={setFolder}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="No folder" />
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
                            */}
                            <div className="space-y-2">
                                <Label>Storage class (optional)</Label>
                                <Select value={storageClass} onValueChange={setStorageClass}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="No preference" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {storageClasses.map((item) => (
                                            <SelectItem key={item.id} value={item.storage_class}>
                                                {item.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                        <div className="mt-6 rounded-lg border border-dashed border-border bg-muted/40 p-4 text-sm text-muted-foreground">
                            Files selected: {selectedFiles.length} · Total size: {formattedTotalGB} GB
                        </div>
                        {isUploading && (
                            <div className="mt-4 space-y-2">
                                <div className="flex items-center justify-between text-xs text-muted-foreground">
                                    <span>Upload progress</span>
                                    <span>{progress}%</span>
                                </div>
                                <div className="h-2 w-full overflow-hidden rounded-full bg-muted">
                                    <div
                                        className="h-full rounded-full bg-primary transition-[width] duration-300"
                                        style={{ width: `${progress}%` }}
                                    />
                                </div>
                            </div>
                        )}
                    </div>
                )}

                <div className="flex flex-wrap items-center gap-3">
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
                        <Button disabled={!canContinue || processing || isUploading} onClick={handleSubmit}>
                            {isUploading ? 'Uploading...' : 'Upload backup'}
                        </Button>
                    )}
                </div>
                <Dialog open={isCreateFolderOpen} onOpenChange={setIsCreateFolderOpen}>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Create folder</DialogTitle>
                            <DialogDescription>Add a name and set a folder destination.</DialogDescription>
                        </DialogHeader>
                        <div className="grid gap-4">
                            <div className="space-y-2">
                                <Label htmlFor="upload-folder-name">Folder name</Label>
                                <Input id="upload-folder-name" placeholder="e.g. Q2 backup set" required />
                            </div>
                            <div className="space-y-2">
                                <Label>Sub folder / vault</Label>
                                <Select required>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select destination" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {allFolders.map((item) => (
                                            <SelectItem key={`folder-${item.id}`} value={`folder:${item.id}`}>
                                                {item.name}
                                            </SelectItem>
                                        ))}
                                        {dataVaults.map((item) => (
                                            <SelectItem key={`vault-${item.id}`} value={`vault:${item.id}`}>
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
                            <Button variant="outline" onClick={() => setIsCreateFolderOpen(false)}>
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

async function uploadFilesInChunks(
    files: File[],
    vaultId: string,
    onProgress: (value: number) => void,
): Promise<Array<{ path: string; original_name: string; size: number; mime_type: string }>> {
    const chunkSize = 5 * 1024 * 1024;
    const totalChunks = files.reduce((sum, file) => sum + Math.ceil(file.size / chunkSize), 0);
    const csrfToken = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content;

    if (!csrfToken) {
        throw new Error('Missing CSRF token');
    }

    let completedChunks = 0;
    const uploads = [];

    for (const file of files) {
        const fileChunks = Math.ceil(file.size / chunkSize);
        const uploadId = `upload-${Date.now()}-${Math.random().toString(36).slice(2, 11)}`;

        for (let chunkIndex = 0; chunkIndex < fileChunks; chunkIndex += 1) {
            const start = chunkIndex * chunkSize;
            const end = Math.min(start + chunkSize, file.size);
            const chunk = file.slice(start, end);

            const formData = new FormData();
            formData.append('file', chunk);
            formData.append('chunkIndex', String(chunkIndex));
            formData.append('totalChunks', String(fileChunks));
            formData.append('uploadId', uploadId);
            formData.append('originalName', file.name);
            formData.append('vault_id', vaultId);

            const response = await fetch(toUrl('/backups/chunked-upload'), {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json',
                },
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Chunk upload failed');
            }

            completedChunks += 1;
            onProgress(Math.round((completedChunks / totalChunks) * 100));
        }

        const finalizeResponse = await fetch(toUrl('/backups/finalize-upload'), {
            method: 'POST',
            body: JSON.stringify({
                uploadId,
                fileName: file.name,
                vault_id: vaultId,
            }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
            },
        });

        if (!finalizeResponse.ok) {
            const error = await finalizeResponse.json();
            throw new Error(error.message || 'Finalization failed');
        }

        uploads.push(await finalizeResponse.json());
    }

    return uploads;
}
