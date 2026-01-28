<?php

namespace App\Http\Controllers\Backup;

use App\Actions\BackupActions;
use App\Http\Controllers\Controller;
use App\Models\Backup;
use App\Models\Folder;
use App\Models\StorageClass;
use App\Models\Vault;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BackupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $backups = Backup::all();
        $vaults = Vault::all();
        $folders = Folder::all();

        return Inertia::render('backups/index', compact(['backups', 'vaults', 'folders']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vaults = Vault::all();
        $folders = Folder::all();
        $storageClasses = StorageClass::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('backups/upload', compact('vaults', 'folders', 'storageClasses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        ray($request);
        ray('Backup Controller Store');
        $validated = $request->validate([
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['file'],
            'vault_id' => ['required', 'exists:vaults,id'],
            'folder_id' => ['nullable', 'exists:folders,id'],
            'storage_class' => ['nullable', 'string', 'max:100'],
        ]);
        ray($validated);

        $vault = Vault::findOrFail($validated['vault_id']);

        foreach ($request->file('files', []) as $file) {
            ray($file);
            $storedPath = $file->store('backup-uploads');
            $meta = [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getClientMimeType(),
                'storage_class' => $validated['storage_class'],
            ];
            app(BackupActions::class)->uploadBackup($storedPath, $vault, $meta);
        }

        return redirect()
            ->route('backups.index')
            ->with('success', 'Backup upload started.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Backup $backup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Backup $backup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Backup $backup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Backup $backup)
    {
        //
    }
}
