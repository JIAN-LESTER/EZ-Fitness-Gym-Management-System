<?php

namespace App\Console;

use App\Console\Commands\CheckMembershipExpirationCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     */
    protected $commands = [
        CheckMembershipExpirationCommand::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Run every minute for testing
        $schedule->command('membership:check-expiration')->everyMinute();

        $schedule->command('memberships:expire')
            ->daily()
            ->at('00:00');

        // Change to daily() in production
        // $schedule->command('membership:check-expiration')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
