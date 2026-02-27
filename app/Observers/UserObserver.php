<?php

namespace App\Observers;

use App\Models\User;
use App\Models\UserStatistics;

class UserObserver
{
    public function created(User $user): void
    {
        UserStatistics::create([
            'user_id' => $user->id,
            'max_api_tokens' => 10000,
        ]);
    }
}
