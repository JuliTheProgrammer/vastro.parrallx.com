<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileUpload\FinalizeUploadRequest;
use App\Http\Requests\FileUpload\StoreFileUploadRequest;
use App\Http\Requests\FileUpload\UploadChunkRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class FileUploadController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('fileupload/index', [
            'projects' => $this->projects(),
        ]);
    }

    public function uploadChunk(UploadChunkRequest $request): JsonResponse
    {
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
        $uploadId = $request->input('uploadId');
        $fileName = $request->input('fileName');
        $projectId = $request->input('project_id');

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
        $finalPath = "projects/project_{$projectId}/files/{$timestamp}/{$fileName}";

        Storage::disk('public')->makeDirectory(dirname($finalPath));

        $finalStoragePath = Storage::disk('public')->path($finalPath);
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
            'file_name' => $fileName,
        ]);
    }

    public function store(StoreFileUploadRequest $request): RedirectResponse
    {
        $request->validated();

        return redirect()
            ->route('fileupload.index')
            ->with('success', 'File uploaded successfully');
    }

    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    protected function projects(): Collection
    {
        return collect([
            ['id' => 101, 'name' => 'Vastro Alpha Intake'],
            ['id' => 102, 'name' => 'Mercury Archive Migration'],
            ['id' => 103, 'name' => 'Orion Media Batch 2026-01'],
            ['id' => 104, 'name' => 'Lumen Research Drop A'],
            ['id' => 105, 'name' => 'Helios Client Proofs'],
        ]);
    }
}
