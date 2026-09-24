<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;

class PruneOldNotifications extends Command
{
    protected $signature = 'notifications:prune {--days=30 : Delete notifications older than this many days}';

    protected $description = 'Delete old notifications to prevent database bloat';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $count = DatabaseNotification::where('created_at', '<', $cutoff)->count();
        DatabaseNotification::where('created_at', '<', $cutoff)->delete();

        $this->info("Pruned {$count} notifications older than {$days} days.");

        return self::SUCCESS;
    }
}
