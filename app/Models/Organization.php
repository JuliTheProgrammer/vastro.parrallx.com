<?php

namespace App\Models;

use App\Observers\OrganizationObserver;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(OrganizationObserver::class)]
class Organization extends Model
{
    use HasUuid;

    protected $fillable = [
        'name',
        // ask ai to make others fillable
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
