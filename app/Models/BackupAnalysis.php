<?php

namespace App\Models;

use App\Observers\BackupAnalysisObserver;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[ObservedBy(BackupAnalysisObserver::class)]
class BackupAnalysis extends Model
{
    /** @use HasFactory<\Database\Factories\BackupAnalysisFactory> */
    use HasFactory, HasUuid;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'description',
        'tags',
        'extra_information',
        'used_tokens',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'extra_information' => 'array',
        ];
    }

    public function backup(): HasOne
    {
        return $this->hasOne(Backup::class);
    }
}
