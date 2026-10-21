<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;

class PruneActivityLogs extends Command
{
    protected $signature = 'activity:prune {--days=30 : Delete logs older than this many days}';

    protected $description = 'Delete old activity log records';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();

        $this->info("Deleted {$deleted} activity log(s) older than {$days} days.");

        return self::SUCCESS;
    }
}