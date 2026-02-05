import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import { toUrl } from '@/lib/utils';
import { Head, router, usePage } from '@inertiajs/react';
import { useMemo, useState } from 'react';

type Project = { id: number | string; name: string };

type UploadResult = {
    path: string;
    file_name: string;
};

export default function FileUploadIndex() {
    const { props } = usePage<{ projects?: Project[] }>();
    const projects = props.projects ?? [];
    const [projectId, setProjectId] = useState('');
    const [file, setFile] = useState<File | null>(null);
    const [isUploading, setIsUploading] = useState(false);
    const [progress, setProgress] = useState(0);

    const canSubmit = useMemo(() => Boolean(projectId && file), [projectId, file]);

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        if (!file || !projectId || isUploading) {
            return;
        }

        setIsUploading(true);
        setProgress(0);

        try {
            const result = await uploadFileInChunks(file, projectId, setProgress);

            router.post(
                toUrl('/fileupload'),
                {
                    project_id: projectId,
                    upload_result: result.path,
                },
                {
                    onFinish: () => {
                        setIsUploading(false);
                        setProgress(0);
                        setFile(null);
                        setProjectId('');
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
                { title: 'File Upload', href: '/fileupload' },
            ]}
        >
            <Head title="File Upload" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div>
                    <h1 className="text-lg font-semibold text-foreground">Upload File</h1>
                    <p className="text-sm text-muted-foreground">Upload ZIP archives in chunks with progress tracking.</p>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="grid gap-4 md:grid-cols-2">
                        <div className="space-y-2">
                            <Label htmlFor="project_id">Project</Label>
                            <Select value={projectId} onValueChange={setProjectId}>
                                <SelectTrigger id="project_id">
                                    <SelectValue placeholder="Select project" />
                                </SelectTrigger>
                                <SelectContent>
                                    {projects.map((project) => (
                                        <SelectItem key={project.id} value={String(project.id)}>
                                            {project.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="file_name">File (ZIP only)</Label>
                            <Input
                                id="file_name"
                                type="file"
                                accept=".zip"
                                required
                                onChange={(event) => {
                                    const nextFile = event.target.files?.[0] ?? null;
                                    setFile(nextFile);
                                }}
                            />
                        </div>
                    </div>

                    <div className="space-y-2">
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

                    <Button type="submit" disabled={!canSubmit || isUploading}>
                        {isUploading ? 'Uploading...' : 'Upload'}
                    </Button>
                </form>
            </div>
        </AppLayout>
    );
}

async function uploadFileInChunks(
    file: File,
    projectId: string,
    onProgress: (value: number) => void,
): Promise<UploadResult> {
    const chunkSize = 5 * 1024 * 1024;
    const totalChunks = Math.ceil(file.size / chunkSize);
    const uploadId = `upload-${Date.now()}-${Math.random().toString(36).slice(2, 11)}`;
    const csrfToken = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content;

    if (!csrfToken) {
        throw new Error('Missing CSRF token');
    }

    for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex += 1) {
        const start = chunkIndex * chunkSize;
        const end = Math.min(start + chunkSize, file.size);
        const chunk = file.slice(start, end);

        const formData = new FormData();
        formData.append('file', chunk);
        formData.append('chunkIndex', String(chunkIndex));
        formData.append('totalChunks', String(totalChunks));
        formData.append('uploadId', uploadId);
        formData.append('originalName', file.name);
        formData.append('project_id', projectId);

        const response = await fetch(toUrl('/fileupload/chunked-upload'), {
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

        const percent = Math.round(((chunkIndex + 1) / totalChunks) * 100);
        onProgress(percent);
    }

    const finalizeResponse = await fetch(toUrl('/fileupload/finalize-upload'), {
        method: 'POST',
        body: JSON.stringify({
            uploadId,
            fileName: file.name,
            project_id: projectId,
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

    return finalizeResponse.json();
}
