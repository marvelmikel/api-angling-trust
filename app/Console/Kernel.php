<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Modules\Core\Console\ExportDatabasesForGoodForm;
use Modules\Core\Console\SendUnsentNotifications;
use Modules\Core\Console\UpdateTicketsForUpdatedEvents;
use Modules\Members\Console\AutoRenewMembers;
use Modules\Members\Console\RepairMembersIndex;
use Modules\Members\Console\SendDataToWarehouse;
use Modules\Members\Console\MembershipAgeFix;
use Modules\Store\Console\UpdateSmartDebitMemberships;
use Modules\Store\Console\UpdateSmartDebitPayments;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'Modules\Members\Console\MembershipAgeFix'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(UpdateSmartDebitPayments::class)
            ->daily()
            ->appendOutputTo(storage_path('logs/update_smart_debit_payments.log'));

        $schedule->command(SendDataToWarehouse::class)
            ->dailyAt('2:00')
            ->environments('production');

        $schedule->command(ExportDatabasesForGoodForm::class)
            ->dailyAt('1:00')
            ->environments('production');

        $schedule->command(UpdateSmartDebitMemberships::class)
            ->monthlyOn(6)
            ->at('21:00')
            ->appendOutputTo(storage_path('logs/update-smart-memberships.log'));

        $schedule->command(UpdateSmartDebitMemberships::class)
            ->monthlyOn(20)
            ->at('21:00')
            ->appendOutputTo(storage_path('logs/update-smart-memberships.log'));;

        $schedule->command(AutoRenewMembers::class)
            ->dailyAt('22:00')
            ->appendOutputTo(storage_path('logs/auto-renew-members.log'));

        $schedule->command(SendUnsentNotifications::class)
            ->everyMinute()
            ->withoutOverlapping();

        $schedule->command(UpdateTicketsForUpdatedEvents::class)
            ->everyMinute()
            ->withoutOverlapping();

        $schedule->command(RepairMembersIndex::class)
            ->everyFifteenMinutes();

        // $schedule->command(MembershipAgeFix::class)
        //     ->withoutOverlapping()
        //     ->everyMinute();

        $schedule->command('members:membership-age-fix')
        ->daily();
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
