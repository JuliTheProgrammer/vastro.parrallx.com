<?php

namespace App\Observers;

use App\Models\BackupAnalysis;
use Illuminate\Support\Facades\Auth;

class BackupAnalysisObserver
{
    public function created(BackupAnalysis $backupAnalysis): void
    {
        $user = Auth::user();

        $user->userStatistics()->increment('backup_analysis_count');

        $user->userStatistics()->increment('used_api_tokens', $backupAnalysis->used_tokens);
    }
}
