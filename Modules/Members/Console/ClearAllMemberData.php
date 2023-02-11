<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Auth\Entities\User;
use Modules\Members\Entities\Member;
use Modules\Members\Entities\MemberMeta;
use Modules\Store\Entities\Payment;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ClearAllMemberData extends Command
{
    protected $name = 'members:clear-all-member-data';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        User::query()
            ->where('id', '!=', 1)
            ->forceDelete();

        Member::truncate();
        MemberMeta::truncate();
        Payment::truncate();
    }
}
