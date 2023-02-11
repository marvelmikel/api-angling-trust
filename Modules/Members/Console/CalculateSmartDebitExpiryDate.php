<?php

namespace Modules\Members\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;

class CalculateSmartDebitExpiryDate  extends Command
{
    protected $signature = 'members:calculate-smart-debit-expiry-date {renewed_at}';

    public function handle()
    {
        $renewed_at = $this->argument('renewed_at');

        $next_1st = null;
        $next_15th = null;

        $wait_period = 11;

        if (Carbon::createFromFormat('Y-m-d', $renewed_at)->addDays($wait_period)->format('Y-m-d') < Carbon::createFromFormat('Y-m-d', $renewed_at)->addDays($wait_period)->day(1)->format('Y-m-d')) {
            $next_1st = Carbon::createFromFormat('Y-m-d', $renewed_at)->addDays($wait_period)->day(1);
        } else {
            $next_1st = Carbon::createFromFormat('Y-m-d', $renewed_at)->addDays($wait_period)->day(1)->addMonth();
        }

        if (Carbon::createFromFormat('Y-m-d', $renewed_at)->addDays($wait_period)->format('Y-m-d') < Carbon::createFromFormat('Y-m-d', $renewed_at)->addDays($wait_period)->day(15)->format('Y-m-d')) {
            $next_15th = Carbon::createFromFormat('Y-m-d', $renewed_at)->addDays($wait_period)->day(15);
        } else {
            $next_15th = Carbon::createFromFormat('Y-m-d', $renewed_at)->addDays($wait_period)->day(15)->addMonth();
        }

        if ($next_1st < $next_15th) {
            echo $next_1st->addYear()->format('Y-m-d') . "\n";
        } else {
            echo $next_15th->addYear()->format('Y-m-d') . "\n";
        }
    }
}
