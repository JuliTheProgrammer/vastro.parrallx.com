<?php

namespace App\Observers;

use App\Enums\AIModelEnum;
use App\Models\UserStatistics;

class UserStatisticsObserver
{
    public function created(UserStatistics $userStatistics): void
    {
        $userStatistics->current_ai_model = AIModelEnum::CLAUDE_HAIKU_4;
    }
}
