<?php

namespace App\Actions;

use App\Jobs\Folder\CreateFolderJob;

class FolderAction
{
    public function createFolder(string $name, $storageClass, $folderable_type, $folderable_id, $location)
    {
        dispatch(new CreateFolderJob($name, $storageClass, $folderable_type, $folderable_id, $location));
    }
}
