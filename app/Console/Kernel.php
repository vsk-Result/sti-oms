<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         $schedule->command('oms-imports:contractor-debts-from-excel')->dailyAt('19:00');
         $schedule->command('oms-imports:dt-debts-from-excel')->dailyAt('19:30');
         $schedule->command('oms-imports:transfer-organizations-payments-from-excel')->twiceDaily(13, 18);
         $schedule->command('oms-imports:update-loans-history-payments')->twiceDaily(13, 18);
         $schedule->command('oms-imports:check-bank-guarantee-date-expired')->dailyAt('07:00');
         $schedule->command('oms-imports:check-avanses-received-from-payments')->twiceDaily(13, 18);
         $schedule->command('oms-imports:create-guarantee-for-contract')->twiceDaily(13, 18);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
