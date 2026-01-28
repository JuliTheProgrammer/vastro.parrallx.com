<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StorageClass extends Model
{
    /** @use HasFactory<\Database\Factories\StorageClassFactory> */
    use HasFactory;

    public function backups(): BelongsToMany
    {
        return $this->belongsToMany(Backup::class);
    }
}
