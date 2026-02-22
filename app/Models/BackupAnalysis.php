<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackupAnalysis extends Model
{
    use HasUuid;

    public function backup(): BelongsTo
    {
        return $this->hasOne(Backup::class);
    }

    protected $casts = [
        'tags' => 'array',
    ];
}
