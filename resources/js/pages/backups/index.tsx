import AppLayout from '@/layouts/app-layout';
import { Head, Link } from '@inertiajs/react';
import {
    ChevronRight,
    Folder as FolderIcon,
    MoreHorizontal,
    FileText,
    Image as ImageIcon,
    Lock,
    Search,
    Video,
} from 'lucide-react';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
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
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Breadcrumbs } from '@/components/breadcrumbs';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { useState } from 'react';

const storageClasses = ['Standard', 'Infrequent', 'Archive'];

const PAGE_SIZE = 50;

type Vault = { id: number | string; name: string };
type Folder = {
    id: number | string;
    name: string;
    folderable_type: string;
    folderable_id: number | string;
    storage_class?: string | null;
};
type Backup = {
    id: number | string;
    name: string;
    mime_type?: string | null;
    created_at?: string | null;
    backupable_type: string;
    backupable_id: number | string;
};

type TreeNode =
    | { kind: 'vault'; vault: Vault; children: TreeNode[] }
    | { kind: 'folder'; folder: Folder; children: TreeNode[] }
    | { kind: 'backup'; backup: Backup };

const nodeKey = (node: TreeNode) => {
    switch (node.kind) {
        case 'vault':
            return `vault:${node.vault.id}`;
        case 'folder':
            return `folder:${node.folder.id}`;
        case 'backup':
            return `backup:${node.backup.id}`;
        default:
            return 'unknown';
    }
};

const previewIcon = (mimeType?: string | null) => {
    if (!mimeType) return FileText;
    if (mimeType.startsWith('image/')) return ImageIcon;
    if (mimeType.startsWith('video/')) return Video;
    return FileText;
};

const parentKey = (type: string, id: number | string) => `${type}:${id}`;

const buildTree = (vaults: Vault[], folders: Folder[], backups: Backup[]) => {
    const folderMap = new Map<string, Folder[]>();
    const backupMap = new Map<string, Backup[]>();
    const parentMap = new Map<string, string | null>();

    folders.forEach((folder) => {
        const key = parentKey(folder.folderable_type, folder.folderable_id);
        const list = folderMap.get(key) ?? [];
        list.push(folder);
        folderMap.set(key, list);
    });

    backups.forEach((backup) => {
        const key = parentKey(backup.backupable_type, backup.backupable_id);
        const list = backupMap.get(key) ?? [];
        list.push(backup);
        backupMap.set(key, list);
    });

    const buildFolderChildren = (folder: Folder): TreeNode[] => {
        const key = parentKey('App\\Models\\Folder', folder.id);
        const childFolders = folderMap.get(key) ?? [];
        const childBackups = backupMap.get(key) ?? [];
        return [
            ...childFolders.map((child) => ({
                kind: 'folder' as const,
                folder: child,
                children: buildFolderChildren(child),
            })),
            ...childBackups.map((child) => ({
                kind: 'backup' as const,
                backup: child,
            })),
        ];
    };

    const tree = vaults.map((vault) => {
        const key = parentKey('App\\Models\\Vault', vault.id);
        const childFolders = folderMap.get(key) ?? [];
        const childBackups = backupMap.get(key) ?? [];
        const children: TreeNode[] = [
            ...childFolders.map((child) => ({
                kind: 'folder' as const,
                folder: child,
                children: buildFolderChildren(child),
            })),
            ...childBackups.map((child) => ({
                kind: 'backup' as const,
                backup: child,
            })),
        ];
        return { kind: 'vault' as const, vault, children };
    });

    tree.forEach((vaultNode) => {
        const vaultKey = nodeKey(vaultNode);
        parentMap.set(vaultKey, null);
        const walk = (node: TreeNode) => {
            if (node.kind !== 'backup') {
                node.children.forEach((child) => {
                    parentMap.set(nodeKey(child), nodeKey(node));
                    walk(child);
                });
            }
        };
        walk(vaultNode);
    });

    return { tree, parentMap };
};

type Props = {
    vaults?: Vault[];
    folders?: Folder[];
    backups?: Backup[];
};

export default function BackupsIndex({ vaults = [], folders = [], backups = [] }: Props) {
    const [isCreateFolderOpen, setIsCreateFolderOpen] = useState(false);
    const [isUploadOpen, setIsUploadOpen] = useState(false);
    const [dropTarget, setDropTarget] = useState<string | null>(null);
    const [droppedFiles, setDroppedFiles] = useState<File[]>([]);
    const [storageClass, setStorageClass] = useState('');
    const { tree, parentMap } = buildTree(vaults, folders, backups);
    const parentLocations = ['All Backups', ...vaults.map((vault) => vault.name)];
    const rootNode: TreeNode = {
        kind: 'vault',
        vault: { id: 'all', name: 'All Vaults' },
        children: tree,
    };
    const [selectedKey, setSelectedKey] = useState(nodeKey(rootNode));
    const [expandedKeys, setExpandedKeys] = useState<Set<string>>(
        () => new Set([nodeKey(rootNode)]),
    );

    const expandPath = (key: string) => {
        const next = new Set(expandedKeys);
        let current: string | null | undefined = key;
        while (current) {
            next.add(current);
            current = parentMap.get(current) ?? null;
        }
        setExpandedKeys(next);
    };

    const findNode = (node: TreeNode, key: string): TreeNode | null => {
        if (nodeKey(node) === key) return node;
        if (node.kind === 'backup') return null;
        for (const child of node.children) {
            const found = findNode(child, key);
            if (found) return found;
        }
        return null;
    };

    const selectedNode = findNode(rootNode, selectedKey) ?? rootNode;
    const selectedChildren =
        selectedNode.kind === 'backup' ? [] : selectedNode.children;

    return (
        <AppLayout
            breadcrumbs={[
                {
                    title: 'All Vaults',
                    href: '/backups',
                },
            ]}
        >
            <Head title="All Vaults" />
            <div className="flex min-h-0 flex-1 flex-col gap-6 p-6">
                <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 className="text-lg font-semibold text-foreground">
                            All Vaults
                        </h1>
                        <p className="text-sm text-muted-foreground">
                            Review recent backups and manage exports.
                        </p>
                        <div className="mt-3 text-xs text-muted-foreground">
                            <Breadcrumbs
                                breadcrumbs={[{ title: 'All Vaults', href: '/backups' }]}
                            />
                        </div>
                    </div>
                    <div className="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                        <div className="relative w-full sm:w-72">
                            <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                placeholder="Search backups or folders..."
                                className="pl-9"
                            />
                        </div>
                        <Button
                            variant="outline"
                            onClick={() => setIsCreateFolderOpen(true)}
                        >
                            Create folder
                            </Button>
                        </div>
                </div>

                <div className="flex min-h-[calc(100vh-220px)] flex-1 flex-col rounded-xl border border-border bg-card">
                    <div className="flex-1 overflow-auto">
                        <div className="border-b border-border px-4 py-3 text-xs text-muted-foreground">
                            <Breadcrumbs
                                breadcrumbs={[{ title: 'All Vaults', href: '/backups' }]}
                            />
                        </div>
                        <div className="grid gap-0 border-b border-border lg:grid-cols-[280px_1fr]">
                            <div className="border-b border-border p-3 lg:border-b-0 lg:border-r">
                                <div className="text-xs font-medium text-muted-foreground">
                                    Locations
                                </div>
                                <div className="mt-3 space-y-1">
                                    {(() => {
                                        const renderTree = (
                                            node: TreeNode,
                                            depth = 0,
                                        ): JSX.Element => {
                                            const isSelected =
                                                selectedKey === nodeKey(node);
                                            const label =
                                                node.kind === 'vault'
                                                    ? node.vault.name
                                                    : node.kind === 'folder'
                                                      ? node.folder.name
                                                      : node.backup.name;
                                            const children =
                                                node.kind === 'backup'
                                                    ? []
                                                    : node.children.filter(
                                                          (child) =>
                                                              child.kind !==
                                                              'backup',
                                                      );
                                            const hasChildren = children.length > 0;
                                            const isExpanded =
                                                expandedKeys.has(nodeKey(node));
                                            return (
                                                <div
                                                    key={`${node.kind}-${label}-${depth}`}
                                                >
                                                    <button
                                                        type="button"
                                                        onClick={() => {
                                                            const key = nodeKey(node);
                                                            setSelectedKey(key);
                                                            expandPath(key);
                                                        }}
                                                        className={`flex w-full items-center gap-2 rounded-md px-2 py-1 text-left text-sm transition ${
                                                            isSelected
                                                                ? 'bg-primary/10 text-primary'
                                                                : 'text-foreground hover:bg-muted/60'
                                                        }`}
                                                        style={{
                                                            paddingLeft: `${depth * 12 + 8}px`,
                                                        }}
                                                    >
                                                        <span
                                                            className={`flex h-4 w-4 items-center justify-center text-muted-foreground ${
                                                                hasChildren
                                                                    ? ''
                                                                    : 'opacity-0'
                                                            }`}
                                                        >
                                                            <button
                                                                type="button"
                                                                onClick={(event) => {
                                                                    event.stopPropagation();
                                                                    setExpandedKeys((current) => {
                                                                        const next = new Set(current);
                                                                        const key = nodeKey(node);
                                                                        if (next.has(key)) {
                                                                            next.delete(key);
                                                                        } else {
                                                                            next.add(key);
                                                                        }
                                                                        return next;
                                                                    });
                                                                }}
                                                                className="rounded-sm p-0.5 hover:bg-muted"
                                                            >
                                                                <ChevronRight
                                                                    className={`h-3 w-3 transition ${
                                                                        isExpanded
                                                                            ? 'rotate-90'
                                                                            : ''
                                                                    }`}
                                                                />
                                                            </button>
                                                        </span>
                                                        <FolderIcon className="h-4 w-4 text-muted-foreground" />
                                                        <span className="truncate">
                                                            {label}
                                                        </span>
                                                    </button>
                                                    {isExpanded
                                                        ? children.map((child) =>
                                                              renderTree(
                                                                  child,
                                                                  depth + 1,
                                                              ),
                                                          )
                                                        : null}
                                                </div>
                                            );
                                        };

                                        return renderTree(rootNode);
                                    })()}
                                </div>
                            </div>
                            <div className="p-3">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Title</TableHead>
                                            <TableHead>Preview</TableHead>
                                            <TableHead>Status</TableHead>
                                            <TableHead>Created</TableHead>
                                            <TableHead className="text-right">
                                                More
                                            </TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {selectedChildren.map((child) => {
                                            if (child.kind === 'vault' || child.kind === 'folder') {
                                                const label =
                                                    child.kind === 'vault'
                                                        ? child.vault.name
                                                        : child.folder.name;
                                                return (
                                                    <TableRow
                                                        key={`${child.kind}-${label}`}
                                                        onClick={() => {
                                                            const key = nodeKey(child);
                                                            setSelectedKey(key);
                                                            expandPath(key);
                                                        }}
                                                        onDragOver={(event) => {
                                                            event.preventDefault();
                                                        }}
                                                        onDrop={(event) => {
                                                            if (child.kind !== 'folder') return;
                                                            event.preventDefault();
                                                            const files = Array.from(
                                                                event.dataTransfer.files,
                                                            );
                                                            if (!files.length) {
                                                                return;
                                                            }
                                                            setDropTarget(child.folder.name);
                                                            setDroppedFiles(files);
                                                            setStorageClass('');
                                                            setIsUploadOpen(true);
                                                        }}
                                                    >
                                                        <TableCell className="font-medium">
                                                            <div className="flex items-center gap-2">
                                                                <FolderIcon className="h-4 w-4 text-muted-foreground" />
                                                                <span>{label}</span>
                                                            </div>
                                                        </TableCell>
                                                        <TableCell className="text-sm text-muted-foreground">
                                                            Folder
                                                        </TableCell>
                                                        <TableCell className="text-sm text-muted-foreground">
                                                            —
                                                        </TableCell>
                                                        <TableCell className="text-sm text-muted-foreground">
                                                            —
                                                        </TableCell>
                                                        <TableCell className="text-right text-sm text-muted-foreground">
                                                            Open
                                                        </TableCell>
                                                    </TableRow>
                                                );
                                            }

                                            const Icon = previewIcon(child.backup.mime_type);
                                            return (
                                                <TableRow key={`backup-${child.backup.id}`}>
                                                    <TableCell className="font-medium">
                                                        {child.backup.name}
                                                    </TableCell>
                                                    <TableCell>
                                                        <div className="flex items-center gap-2 text-muted-foreground">
                                                            <span className="flex h-8 w-8 items-center justify-center rounded-md bg-muted">
                                                                <Icon className="h-4 w-4" />
                                                            </span>
                                                            <span className="text-xs font-medium">
                                                                {child.backup.mime_type ?? 'File'}
                                                            </span>
                                                        </div>
                                                    </TableCell>
                                                    <TableCell>
                                                        <div className="flex items-center gap-2 text-sm text-muted-foreground">
                                                            <Lock className="h-4 w-4" />
                                                            <span className="capitalize">
                                                                versioned
                                                            </span>
                                                        </div>
                                                    </TableCell>
                                                    <TableCell className="text-sm text-muted-foreground">
                                                        {child.backup.created_at ?? '—'}
                                                    </TableCell>
                                                    <TableCell className="text-right">
                                                        <DropdownMenu>
                                                            <DropdownMenuTrigger asChild>
                                                                <Button variant="ghost" size="icon">
                                                                    <MoreHorizontal className="h-4 w-4" />
                                                                    <span className="sr-only">
                                                                        More actions
                                                                    </span>
                                                                </Button>
                                                            </DropdownMenuTrigger>
                                                            <DropdownMenuContent align="end">
                                                                <DropdownMenuItem asChild>
                                                                    <Link href="#">
                                                                        Download
                                                                    </Link>
                                                                </DropdownMenuItem>
                                                                <DropdownMenuItem asChild>
                                                                    <Link href="#">
                                                                        Share
                                                                    </Link>
                                                                </DropdownMenuItem>
                                                                <DropdownMenuItem asChild>
                                                                    <Link
                                                                        href={`/backups/versions?backup=${child.backup.id}`}
                                                                    >
                                                                        View versions
                                                                    </Link>
                                                                </DropdownMenuItem>
                                                                <DropdownMenuItem asChild>
                                                                    <Link href="#">
                                                                        Delete
                                                                    </Link>
                                                                </DropdownMenuItem>
                                                            </DropdownMenuContent>
                                                        </DropdownMenu>
                                                    </TableCell>
                                                </TableRow>
                                            );
                                        })}
                                    </TableBody>
                                </Table>
                            </div>
                        </div>
                    </div>
                    <div className="flex flex-col items-start gap-3 border-t border-border px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <div className="text-xs text-muted-foreground">
                            Showing {Math.min(PAGE_SIZE, backups.length)} of {backups.length}{' '}
                            backups
                        </div>
                        <div className="flex items-center gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={backups.length <= PAGE_SIZE}
                            >
                                Previous
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={backups.length <= PAGE_SIZE}
                            >
                                Next
                            </Button>
                        </div>
                    </div>
                </div>
                <Dialog
                    open={isCreateFolderOpen}
                    onOpenChange={setIsCreateFolderOpen}
                >
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Create folder</DialogTitle>
                            <DialogDescription>
                                Choose a name and where the folder should live.
                            </DialogDescription>
                        </DialogHeader>
                        <div className="grid gap-4">
                            <div className="space-y-2">
                                <Label htmlFor="folder-name">Folder name</Label>
                                <Input
                                    id="folder-name"
                                    placeholder="e.g. May exports"
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
                                        {parentLocations.map((item) => (
                                            <SelectItem key={item} value={item}>
                                                {item}
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
                <Dialog
                    open={isUploadOpen}
                    onOpenChange={(open) => {
                        setIsUploadOpen(open);
                        if (!open) {
                            setDroppedFiles([]);
                            setDropTarget(null);
                            setStorageClass('');
                        }
                    }}
                >
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Upload to folder</DialogTitle>
                            <DialogDescription>
                                {dropTarget
                                    ? `Uploading to ${dropTarget}.`
                                    : 'Choose upload settings for these files.'}
                            </DialogDescription>
                        </DialogHeader>
                        <div className="grid gap-4">
                            <div className="space-y-2">
                                <Label>Storage class</Label>
                                <Select
                                    value={storageClass}
                                    onValueChange={setStorageClass}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select storage class" />
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
                            <div className="rounded-lg border border-border bg-muted/30 p-3 text-xs text-muted-foreground">
                                {droppedFiles.length} files ready to upload
                            </div>
                        </div>
                        <DialogFooter>
                            <Button
                                variant="outline"
                                onClick={() => setIsUploadOpen(false)}
                            >
                                Cancel
                            </Button>
                            <Button disabled={!storageClass || !droppedFiles.length}>
                                Upload files
                            </Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>
            </div>
        </AppLayout>
    );
}
