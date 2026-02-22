<?php

namespace App\Http\Controllers\Backup;

use App\Actions\BackupActions;
use App\Actions\LinkAction;
use App\Exceptions\BackupHasWORMProtectionException;
use App\Exceptions\InvalidUserShowBackup;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backup\BackupRequest;
use App\Http\Requests\Backup\FinalizeUploadRequest;
use App\Http\Requests\Backup\UploadChunkRequest;
use App\Models\Backup;
use App\Models\Folder;
use App\Models\StorageClass;
use App\Models\Vault;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class BackupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $backups = Backup::all();
        $vaults = $request->user()
            ?->vaults()
            ->get() ?? collect();
        $folders = Folder::all();

        return Inertia::render('backups/index', compact(['backups', 'vaults', 'folders']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        $vaults = $request->user()
            ?->vaults()
            ->get() ?? collect();
        $folders = Folder::all();
        $storageClasses = StorageClass::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('backups/upload', compact('vaults', 'folders', 'storageClasses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BackupRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();

        // $aiAnalyses = $validated['analyses'];

        // temp, change later in fronend ui
        $aiAnalyses = true;

        $vault = Vault::findOrFail($validated['vault_id']);
        $storageClass = $validated['storage_class'] ?? 'STANDARD';

        if ($request->hasFile('files')) {
            foreach ($request->file('files', []) as $file) {
                $storedPath = $file->store('backup-uploads');
                $meta = [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getClientMimeType(),
                    'storage_class' => $storageClass,
                ];
                app(BackupActions::class)->uploadBackup($storedPath, $vault, $meta, $aiAnalyses);
            }
        } else {
            foreach ($validated['uploads'] as $upload) {
                $meta = [
                    'original_name' => $upload['original_name'],
                    'size' => $upload['size'],
                    'mime_type' => $upload['mime_type'],
                    'storage_class' => $storageClass,
                ];

                app(BackupActions::class)->uploadBackup($upload['path'], $vault, $meta, $aiAnalyses);
            }
        }

        return redirect()
            ->route('backups.index')
            ->with('success', 'Backup upload started.');
    }

    /**
     * Display the specified resource.
     *
     * @throws Throwable
     */
    public function show($uuid)
    {
        $backup = Backup::where('uuid', $uuid)->firstOrFail();
        $link = app(LinkAction::class)->createLinkForBackup($backup);
        // check if user is logged in otherwise throw exception
        throw_if(! Auth::user(), InvalidUserShowBackup::class); // this can only be done later withing the logic in our applicaiton

        return redirect($link);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Backup $backup): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Backup $backup): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        $backup = Backup::where('uuid', $uuid)->firstOrFail();

        ray($backup->backupable_type);

        $vaultId = $backup->backupable_id;

        $vault = Vault::find($vaultId);

        ray($vault);

        throw_if($vault->worm_protection, BackupHasWORMProtectionException::class); // move to policy
        // delete it locally in the database
        app(BackupActions::class)->deleteBackup($backup);
        // delete the backup in AWS
        Backup::destroy($backup->id);

        return redirect(route('backups.index'))->with('success', 'Backup deleted.');
    }

    public function uploadChunk(UploadChunkRequest $request): JsonResponse
    {
        $vaultId = (int) $request->input('vault_id');
        if (! $request->user()?->vaults()->whereKey($vaultId)->exists()) {
            return response()->json(['message' => 'Unauthorized vault.'], 403);
        }

        $chunk = $request->file('file');
        $chunkIndex = (int) $request->input('chunkIndex');
        $uploadId = $request->input('uploadId');

        $tempPath = "temp/uploads/{$uploadId}";
        $chunkPath = "{$tempPath}/{$chunkIndex}";

        Storage::disk('local')->makeDirectory($tempPath);
        Storage::disk('local')->put($chunkPath, fopen($chunk->getRealPath(), 'r'));

        return response()->json([
            'message' => 'Chunk uploaded successfully',
            'chunkIndex' => $chunkIndex,
        ]);
    }

    public function finalizeUpload(FinalizeUploadRequest $request): JsonResponse
    {
        $vaultId = (int) $request->input('vault_id');
        if (! $request->user()?->vaults()->whereKey($vaultId)->exists()) {
            return response()->json(['message' => 'Unauthorized vault.'], 403);
        }

        $uploadId = $request->input('uploadId');
        $fileName = $request->input('fileName');

        $tempPath = "temp/uploads/{$uploadId}";
        $chunks = Storage::disk('local')->files($tempPath);

        if (empty($chunks)) {
            return response()->json([
                'error' => 'No chunks found',
            ], 500);
        }

        usort($chunks, function (string $left, string $right): int {
            return (int) basename($left) <=> (int) basename($right);
        });

        $timestamp = now()->format('Y-m-d_h_i_A');
        $finalPath = "backup-uploads/{$timestamp}/{$fileName}";

        Storage::disk('local')->makeDirectory(dirname($finalPath));

        $finalStoragePath = Storage::disk('local')->path($finalPath);
        $finalFile = fopen($finalStoragePath, 'wb');

        foreach ($chunks as $chunkPath) {
            $chunkStream = Storage::disk('local')->readStream($chunkPath);
            if ($chunkStream === false) {
                fclose($finalFile);

                return response()->json([
                    'error' => 'Failed to read upload chunk',
                ], 500);
            }

            stream_copy_to_stream($chunkStream, $finalFile);
            fclose($chunkStream);
        }

        fclose($finalFile);
        Storage::disk('local')->deleteDirectory($tempPath);

        return response()->json([
            'success' => true,
            'path' => $finalPath,
            'original_name' => $fileName,
            'size' => Storage::disk('local')->size($finalPath) ?? 0,
            'mime_type' => Storage::disk('local')->mimeType($finalPath) ?? 'application/octet-stream',
        ]);
    }
}
