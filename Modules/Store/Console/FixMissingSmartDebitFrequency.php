<?php

namespace Modules\Store\Console;

use Illuminate\Console\Command;
use Modules\Auth\Entities\User;
use Modules\Members\Entities\Member;
use Modules\Store\Services\SmartDebit;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FixMissingSmartDebitFrequency extends Command
{
    protected $name = 'store:fix-missing-smart-debit-frequency';

    private $smartDebit;

    public function __construct(SmartDebit $smartDebit)
    {
        parent::__construct();

        $this->smartDebit = $smartDebit;
    }

    public function handle()
    {
        $users = User::query()
            ->whereNotNull('smart_debit_id')
            ->whereNull('smart_debit_frequency')
            ->get();

        foreach ($users as $user) {
            $this->fix($user);
            sleep(2);
        }
    }

    private function fix(User $user)
    {
        $ref = $user->smart_debit_id;

        try {

            $xml = $this->smartDebit->getPayerByReference($ref)
                ->parsed();

            $frequency = (string) $xml['Data']->PayerDetails->attributes()->frequency_type;

            $user->smart_debit_frequency = $frequency;
            $user->save();

            $this->line($ref);

        } catch (\Exception $exception) {
            $this->error($ref);
        }
    }
}
