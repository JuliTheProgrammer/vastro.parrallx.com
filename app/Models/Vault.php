<?php

namespace App\Models;

use App\Observers\VaultObserver;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[ObservedBy(VaultObserver::class)]
class Vault extends Model
{
    /** @use HasFactory<\Database\Factories\VaultFactory> */
    use HasFactory, HasUuid, LogsActivity, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'location_id',
        'name',
        'aws_bucket_name',
        'aws_bucket_arn',
        'worm_protection',
        'delete_protection',
        'kms_encryption',
        'kms_arn',
    ];

    protected function casts(): array
    {
        return [
            'worm_protection' => 'boolean',
            'delete_protection' => 'boolean',
            'kms_encryption' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function folders(): HasMany
    {
        return $this->hasMany(Folder::class);
    }

    public function linkable(): MorphOne
    {
        return $this->morphOne(Link::class, 'linkable');
    }

    public function folderable(): MorphMany
    {
        return $this->morphMany(Folder::class, 'folderable');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll();
    }
}
