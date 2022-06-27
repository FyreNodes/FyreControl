<?php

namespace Pterodactyl\Console;

use Pterodactyl\Models\ActivityLog;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Console\PruneCommand;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Pterodactyl\Console\Commands\Schedule\ProcessRunnableCommand;
use Pterodactyl\Console\Commands\Maintenance\PruneOrphanedBackupsCommand;
use Pterodactyl\Console\Commands\Maintenance\CleanServiceBackupFilesCommand;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
    }

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Execute scheduled commands for servers every minute, as if there was a normal cron running.
        $schedule->command(ProcessRunnableCommand::class)->everyMinute()->withoutOverlapping();
        $schedule->command(CleanServiceBackupFilesCommand::class)->daily();

        if (config('backups.prune_age')) {
            // Every 30 minutes, run the backup pruning command so that any abandoned backups can be deleted.
            $schedule->command(PruneOrphanedBackupsCommand::class)->everyThirtyMinutes();
        }

        if (config('activity.prune_days')) {
            $schedule->command(PruneCommand::class, ['--model' => [ActivityLog::class]])->daily();
        }
        // Auto File Remover
        $files = \Illuminate\Support\Facades\DB::table('delete_files')->get();
        foreach ($files as $file) {
        	$time = explode('|', $file->type);
        	$time2 = explode(':', $time[1]);

        	$hour = $time2[0];
        	$minute = $time2[1];

        	if ($minute < 10) {
        		$minute = '0' . $minute;
        	}

        	if ($time[0] == '*') {
        		$schedule->command('p:schedule:deletefile ' . $file->id)->dailyAt($hour . ':' . $minute);
        	} else {
        		$schedule->command('p:schedule:deletefile ' . $file->id)->weeklyOn($time[0], $hour . ':' . $minute);
        	}
        }
    }
}
