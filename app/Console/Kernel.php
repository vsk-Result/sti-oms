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
        $schedule->command('oms:contractor-debts-from-excel')->dailyAt('19:00');
        $schedule->command('oms:dt-debts-from-excel')->dailyAt('19:30');
        $schedule->command('oms:transfer-organizations-payments-from-excel')->twiceDaily(13, 18);
        $schedule->command('oms:update-loans-history-payments')->twiceDaily(13, 18);
        $schedule->command('oms:check-bank-guarantee-date-expired')->dailyAt('07:00');
        $schedule->command('oms:check-avanses-received-from-payments')->twiceDaily(13, 18);
        $schedule->command('oms:create-guarantee-for-contract')->twiceDaily(13, 18);
        $schedule->command('oms:update-general-costs')->everyThirtyMinutes();
        $schedule->command('oms:check-objects-for-general-codes-to-customers-exist')->dailyAt('07:00');
        $schedule->command('oms:objects-debts-from-excel')->dailyAt('19:00');
        $schedule->command('oms:service-debts-from-excel')->dailyAt('19:00');
        $schedule->command('oms:make-finance-report-history')->everyTenMinutes();
        $schedule->command('oms:notify-to-email-about-object-balance')->dailyAt('15:00');
        $schedule->command('oms:update-wrong-payment-code')->dailyAt('20:00');
        $schedule->command('oms:update-object-organization-debt-pivot')->everyThirtyMinutes();
        $schedule->command('oms:update-crm-salary')->twiceDaily(12, 19);
        $schedule->command('oms:notify-to-bosses-email-about-object-balance')->mondays()->at('16:00');
        $schedule->command('oms:get-files-from-one-c')->hourly();
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
