<?php

namespace App\Models;

use App\Observers\BackupAnalysisObserver;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(BackupAnalysisObserver::class)]
class BackupAnalysis extends Model
{
    use HasUuid;

    public function backup(): BelongsTo
    {
        return $this->hasOne(Backup::class);
    }

    protected $casts = [
        'tags' => 'array',
        'extra_information' => 'array',
    ];
}
