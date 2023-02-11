<?php declare(strict_types=1);

namespace Modules\Members\Console;

use Closure;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Modules\Members\Entities\Member;
use Modules\Store\Enums\PaymentProvider;

class AutoRenewMembers extends Command
{
    protected $signature = 'members:auto-renew-members {--expires-at=}';
    private string $expiresAt;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $expiresAt = $this->option('expires-at');
        try {
            $this->expiresAt = ($expiresAt ? Carbon::parse($expiresAt) : today())->format('Y-m-d');
        } catch (Exception $e) {
            $this->error('Invalid `expires-at`');
            die();
        }

        $this->info('Running AutoRenewMembers');
        $this->info(sprintf('Looking for members who expired on %s', $this->expiresAt));

        $members = $this->getMembers();

        $this->info(sprintf('Found %d members', $members->count()));

        $members->each(Closure::fromCallable([$this, 'oneMember']));
    }

    private function getMembers(): Collection
    {
        return Member::with('index')
            ->where('payment_is_recurring', true)
            ->where('membership_type_id', '!=', 18)
            ->where(function (Builder $query) {
                return $query
                    ->where('payment_provider', '!=', PaymentProvider::STRIPE)
                    ->orWhereNull('payment_provider');
            })
            ->where('expires_at', $this->expiresAt)
            ->get();
    }

    private function oneMember(Member $member): void
    {
        $newExpiry = $member->expires_at->addYear();
        $this->info(
            sprintf(
                'Auto-renewing member \'%s\' from \'%s\' to \'%s\'',
                $member->index->reference,
                $member->expires_at,
                $newExpiry,
            )
        );

        $member->expires_at = $newExpiry;
        $member->renewed_at = now();
        $member->updated_at = now();
        $member->save();
    }
}
