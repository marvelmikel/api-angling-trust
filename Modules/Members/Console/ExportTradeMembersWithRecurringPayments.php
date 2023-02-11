<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Members\Entities\Member;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ExportTradeMembersWithRecurringPayments extends Command
{
    protected $name = 'members:export-trade-members-with-recurring-payments';

    public function handle()
    {
        $members = Member::query()
            ->where('membership_type_id', '10')
            ->where('payment_is_recurring', 1)
            ->get();

        $file = fopen(storage_path('data/members.csv'), 'w');

        foreach ($members as $member) {
            fputcsv($file, [
                $member->user->reference,
                $member->full_name,
                $member->payment_provider
            ]);
        }

        fclose($file);
    }
}
