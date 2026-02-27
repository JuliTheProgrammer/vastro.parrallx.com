<?php

namespace App\Actions;

use App\Jobs\Folder\CreateFolderJob;
use App\Models\Vault;

class FolderAction
{
    public function createFolder(string $name, string $storageClass, string $folderableType, int $folderableId, string $location, Vault $vault): void
    {
        dispatch(new CreateFolderJob($name, $storageClass, $folderableType, $folderableId, $location, $vault));
    }
}
