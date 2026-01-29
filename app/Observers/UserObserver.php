<?php

namespace App\Observers;

use App\Models\UserStatistics;

class UserObserver
{
    public function created(User $user): void
    {
        $userStatistics = new UserStatistics;
        $userStatistics->user_id = $user->id;
    }
}
