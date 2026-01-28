<?php

namespace App\Models;

use App\Actions\LinkAction;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Backup extends Model
{
    /** @use HasFactory<\Database\Factories\BackupFactory> */
    use HasFactory, HasUuid, LogsActivity, SoftDeletes;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vault(): BelongsTo
    {
        return $this->belongsTo(Vault::class);
    }

    public function linkable(): MorphMany
    {
        return $this->MorphMany(Link::class, 'linkable');
    }

    public function folderable(): MorphOne
    {
        return $this->morphOne(Folder::class, 'folderable');
    }

    public function storageClass(): HasOne
    {
        return $this->hasOne(StorageClass::class);
    }

    public function getTemporarySignedUrlAtribute(): string
    {
        return app(LinkAction::class)->createLinkForBackup($this);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll();
    }
}
