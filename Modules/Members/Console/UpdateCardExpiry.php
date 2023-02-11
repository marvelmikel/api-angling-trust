<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Core\Console\ProgressCommand;
use Modules\Members\Entities\Member;
use Modules\Members\Services\MemberCardExpiryUpdater;
use Modules\Store\Enums\PaymentProvider;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateCardExpiry extends ProgressCommand
{
    protected $signature = 'members:update-card-expiry {skip?}';

    public function handle()
    {
        $members = Member::query()
            ->where('payment_provider', PaymentProvider::STRIPE)
            ->where('payment_is_recurring', true)
            ->get();

        $skip = (int) $this->argument('skip') ?? 0;

        $this->setTotal(count($members));

        $this->start();

        foreach ($members as $index => $member) {
            $this->handleItem(function() use ($member, $skip, $index) {
                if ($index < $skip) {
                    return true;
                }

                return MemberCardExpiryUpdater::update($member);
            });
        }

        $this->stop();
    }
}
