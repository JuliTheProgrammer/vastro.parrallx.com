<?php

namespace App\Console\Commands\Backup;

use Illuminate\Console\Command;

class CreateLinkBackupCommand extends Command
{
    protected $signature = 'dispatch:create-link {$backupId}';

    protected $description = 'Command description';

    public function handle(): void {}
}
