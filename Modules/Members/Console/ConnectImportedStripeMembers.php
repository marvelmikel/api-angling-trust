<?php

namespace Modules\Members\Console;

use Modules\Auth\Entities\User;
use Modules\Core\Console\ProgressCommand;
use Modules\Members\Entities\Member;
use Modules\Store\Enums\PaymentProvider;
use Stripe\StripeClient;

class ConnectImportedStripeMembers extends ProgressCommand
{
    protected $signature = 'members:connect-imported-stripe-members {file}';

    private $stripe;

    private $errorExport = [];
    private $successExport = [];

    const ERROR_NO_EMAIL = 'No Email';
    const ERROR_NO_ACCOUNTS = '0 Accounts';
    const ERROR_NO_SUBSCRIPTIONS = '0 Subscriptions';
    const ERROR_MULTIPLE_SUBSCRIPTIONS = '>1 Subscriptions';

    public function handle()
    {
        $this->stripe = new StripeClient(
            config('stripe.secret')
        );

        $path = storage_path('imports/' . $this->argument('file'));
        $refs = json_decode(file_get_contents($path));

        $users = User::query()
            ->whereIn('reference', $refs)
            ->get();

        $this->setTotal(count($users));

        $this->start();

        foreach ($users as $user) {
            $this->handleItem(function() use ($user) {
                return $this->connectMember($user);
            });
        }

        $this->stop();

        $this->exportResults();
    }

    private function logError(Member $member, string $code)
    {
        $this->errorExport[] = [
            $member->user->reference, $member->user->email, $code
        ];
    }

    private function logSuccess(Member $member, string $oldStripeId, string $newStripeId)
    {
        $this->successExport[] = [
            $member->user->reference, $member->user->email, $oldStripeId, $newStripeId
        ];
    }

    private function exportResults()
    {
        $success = fopen(storage_path('exports/success.csv'), 'w');

        foreach ($this->successExport as $export) {
            fputcsv($success, $export);
        }

        fclose($success);

        $error = fopen(storage_path('exports/error.csv'), 'w');

        foreach ($this->errorExport as $export) {
            fputcsv($error, $export);
        }

        fclose($error);
    }

    private function connectMember(User $user)
    {
        $member = Member::query()
            ->where('user_id', $user->id)
            ->firstOrFail();

        if (!$user->email) {
            $this->logError($member, self::ERROR_NO_EMAIL);
            return false;
        }

        $stripeAccounts = $this->getStripeAccountsByEmail($user->email);

        if (count($stripeAccounts) === 0) {
            $this->logError($member, self::ERROR_NO_ACCOUNTS);
            return false;
        }

        $activeStripeAccounts = $this->getStripeAccountsWithActiveSubscription($stripeAccounts);

        if (count($activeStripeAccounts) === 0) {
            $this->logError($member, self::ERROR_NO_SUBSCRIPTIONS);
            return false;
        }

        if (count($activeStripeAccounts) > 1) {
            $this->logError($member, self::ERROR_MULTIPLE_SUBSCRIPTIONS);
            return false;
        }

        $activeStripeAccount = $activeStripeAccounts[0];

        if ($user->stripe_id === $activeStripeAccount) {
            return true;
        }

        $this->logSuccess($member, $user->stripe_id, $activeStripeAccount);

        $user->stripe_id = $activeStripeAccount;
        $user->save();

        return true;
    }

    private function getStripeAccountsByEmail(string $email)
    {
        $accounts = [];

        $customers = $this->stripe->customers->all([
            'email' => $email
        ]);

        foreach ($customers as $customer) {
            $accounts[] = $customer->id;
        }

        return $accounts;
    }

    private function getStripeAccountsWithActiveSubscription($customers)
    {
        $activeCustomers = [];

        foreach ($customers as $id) {
            if ($this->customerHasActiveSubscription($id)) {
                $activeCustomers[] = $id;
            }
        }

        return $activeCustomers;
    }

    private function customerHasActiveSubscription($id)
    {
        $subscriptions = $this->stripe->subscriptions->all([
            'customer' => $id
        ]);

        return count($subscriptions) > 0;
    }
}
