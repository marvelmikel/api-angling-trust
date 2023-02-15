<?php



namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Members\Entities\Member;
use Modules\Members\Entities\MemberMeta;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MembershipAgeFix extends Command
{
    protected $name = 'members:membership-age-fix';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Member::chunk(100, function ($members) {
            foreach ($members as $member) {
                $date_of_birth = MemberMeta::where('member_id', $member->id)->where('key', 'date_of_birth')->first();
                if ($date_of_birth) {
                    $date = json_decode($date_of_birth->value);
                    $age = Carbon::createFromDate($date->year, $date->month, $date->day)->age;

                    // check for juniors
                    if ($age >= 0 && $age < 18) {
                        $member->category_id = 24;
                    }

                    // check for young adults
                    if ($age >= 18 && $age < 22) {
                        $member->category_id = 23;
                    }

                    // check for adults
                    if ($age >= 22) {
                        $member->category_id = 22;
                    }
                    $member->save();
                }
            }
        });

        return Command::SUCCESS;
    }

}
