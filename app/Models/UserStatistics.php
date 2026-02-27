<?php

namespace App\Models;

use App\Enums\AIModelEnum;
use App\Observers\UserStatisticsObserver;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[ObservedBy(UserStatisticsObserver::class)]
class UserStatistics extends Model
{
    use HasFactory, HasUuid, LogsActivity;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'total_stored_bytes',
        'backup_count',
        'max_api_tokens',
        'used_api_tokens',
        'backup_analysis_count',
        'vault_count',
        'account_blocked',
        'current_ai_model',
        'max_allowed_bytes',
    ];

    protected function casts(): array
    {
        return [
            'account_blocked' => 'boolean',
            'current_ai_model' => AIModelEnum::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasEnoughTokens(): bool
    {
        return $this->used_api_tokens >= $this->max_api_tokens;
    }

    public function isAllowedToUplaod(): bool
    {
        return $this->total_stored_bytes >= $this->max_allowed_bytes;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll();
    }
}
