<?php

namespace Modules\Store\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Modules\Auth\Entities\User;
use Modules\Core\Console\ProgressCommand;
use Modules\Members\Entities\Member;
use Modules\Store\Entities\Payment;
use Modules\Store\Enums\PaymentProvider;
use Modules\Store\Services\SmartDebit;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AuditSmartDebitUpdate extends Command
{
    protected $name = 'store:audit-smart-debit-update';

    private $smartDebit;
    private $failed = [];

    public function __construct(SmartDebit $smartDebit)
    {
        parent::__construct();

        $this->smartDebit = $smartDebit;
    }

    public function handle()
    {
        $members = Member::query()
            ->whereIn('id', [3555,6606,14031,17088,17102,17104,17105,17106,17110,17112,17193,17194,17203,17299,17523,17915,18336,18354,18790,18855,18873,18877,18977,30908,30961,31072,31290])
            ->get();

        foreach ($members as $member) {
            $this->line('"' . $member->id . '"' . "," . '"' . $member->user->reference . '"' . ',,' . '"' . $member->user->smart_debit_id . '"');
        }

        die();

        foreach ($members as $member) {
            try {
                $this->smartDebit->getPayerByReference($member->user->smart_debit_id);
            } catch (\Exception $exception) {
                $this->failed[] = $member;
            }

            sleep(2);
        }

        foreach ($this->failed as $member) {
            $this->line($member->id);
        }

        return true;
    }
}
