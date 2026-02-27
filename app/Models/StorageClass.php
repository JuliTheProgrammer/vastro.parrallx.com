<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StorageClass extends Model
{
    /** @use HasFactory<\Database\Factories\StorageClassFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'storage_class',
    ];

    public function backups(): HasMany
    {
        return $this->hasMany(Backup::class);
    }
}
