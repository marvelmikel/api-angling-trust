<?php

namespace Modules\Members\Services;

use Carbon\Carbon;
use Modules\Members\Entities\Member;
use Modules\Store\Enums\PaymentProvider;

class MemberExpiryDateCalculator
{
    public static function calculate(Member $member)
    {
        if ($member->category->slug === 'junior') {
            $date_of_birth = $member->getMetaValue('date_of_birth');
            $date_of_birth = Carbon::createFromFormat('Y-m-d', $date_of_birth['year'] . '-' . $date_of_birth['month'] . '-' . $date_of_birth['day']);
            return $date_of_birth->addYears(18);
        }

        if ($member->payment_provider === PaymentProvider::SMART_DEBIT) {
            $next_1st = null;
            $next_15th = null;

            $wait_period = 11;

            if (Carbon::now()->addDays($wait_period)->format('Y-m-d') < Carbon::now()->addDays($wait_period)->day(1)->format('Y-m-d')) {
                $next_1st = Carbon::now()->addDays($wait_period)->day(1);
            } else {
                $next_1st = Carbon::now()->addDays($wait_period)->day(1)->addMonth();
            }

            if (Carbon::now()->addDays($wait_period)->format('Y-m-d') < Carbon::now()->addDays($wait_period)->day(15)->format('Y-m-d')) {
                $next_15th = Carbon::now()->addDays($wait_period)->day(15);
            } else {
                $next_15th = Carbon::now()->addDays($wait_period)->day(15)->addMonth();
            }

            if ($next_1st < $next_15th) {
                return $next_1st->addYear();
            } else {
                return $next_15th->addYear();
            }
        }

        return Carbon::now()->addYear();
    }

    public static function calculateStartFrom(Member $member, $from)
    {
        if ($member->category->slug === 'junior') {
            $date_of_birth = $member->getMetaValue('date_of_birth');
            $date_of_birth = Carbon::createFromFormat('Y-m-d', $date_of_birth['year'] . '-' . $date_of_birth['month'] . '-' . $date_of_birth['day']);
            return $date_of_birth->addYears(18);
        }

        if ($member->payment_provider === PaymentProvider::SMART_DEBIT) {
            $next_1st = null;
            $next_15th = null;

            $wait_period = 11; // todo: may need to do something different here (i.e - days until start as payment could be taken earlier)

            if (Carbon::createFromFormat('Y-m-d', $from)->addDays($wait_period)->format('Y-m-d') < Carbon::createFromFormat('Y-m-d', $from)->addDays($wait_period)->day(1)->format('Y-m-d')) {
                $next_1st = Carbon::createFromFormat('Y-m-d', $from)->addDays($wait_period)->day(1);
            } else {
                $next_1st = Carbon::createFromFormat('Y-m-d', $from)->addDays($wait_period)->day(1)->addMonth();
            }

            if (Carbon::createFromFormat('Y-m-d', $from)->addDays($wait_period)->format('Y-m-d') < Carbon::createFromFormat('Y-m-d', $from)->addDays($wait_period)->day(15)->format('Y-m-d')) {
                $next_15th = Carbon::createFromFormat('Y-m-d', $from)->addDays($wait_period)->day(15);
            } else {
                $next_15th = Carbon::createFromFormat('Y-m-d', $from)->addDays($wait_period)->day(15)->addMonth();
            }

            if ($next_1st < $next_15th) {
                return $next_1st;
            } else {
                return $next_15th;
            }
        }

        return Carbon::createFromFormat('Y-m-d', $from);
    }
}
