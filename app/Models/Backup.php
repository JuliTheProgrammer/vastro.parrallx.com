<?php

namespace App\Models;

use App\Actions\LinkAction;
use App\Enums\BackupClassificationEnum;
use App\Observers\BackupObserver;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[ObservedBy(BackupObserver::class)]
class Backup extends Model
{
    /** @use HasFactory<\Database\Factories\BackupFactory> */
    use HasFactory, HasUuid, LogsActivity, Searchable, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'storage_class_id',
        'user_id',
        'backup_analysis_id',
        'backupable_type',
        'backupable_id',
        'name',
        'path',
        'mime_type',
        'mime_type_readable',
        'size_megaBytes',
        'data_classifications',
    ];

    protected function casts(): array
    {
        return [
            'data_classifications' => BackupClassificationEnum::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function backupable(): MorphTo
    {
        return $this->morphTo();
    }

    public function vault(): BelongsTo
    {
        return $this->belongsTo(Vault::class);
    }

    public function storageClass(): BelongsTo
    {
        return $this->belongsTo(StorageClass::class);
    }

    public function linkable(): MorphMany
    {
        return $this->morphMany(Link::class, 'linkable');
    }

    public function folderable(): MorphOne
    {
        return $this->morphOne(Folder::class, 'folderable');
    }

    public function backupAnalysis(): BelongsTo
    {
        return $this->belongsTo(BackupAnalysis::class);
    }

    public function getTemporarySignedUrlAttribute(): string
    {
        return app(LinkAction::class)->createLinkForBackup($this);
    }

    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'mime_type' => $this->mime_type,
            'created_at' => $this->created_at,
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll();
    }
}
