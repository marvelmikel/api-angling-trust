<?php

namespace Modules\Store\Console;

use Closure;
use Exception;
use Generator;
use stdClass;

class UpdateStripeSubscriptionPrices extends StripeSubscriptionsBaseCommand
{
    private const PER_PAGE = 100;

    protected $signature = 'store:update-stripe-subscription-prices
     {priceIdFrom : The price ID to search for}
     {priceIdTo : The price ID to replace}
     {--p|--prorate : If set, create prorations on changed plans, as appropriate}
     ';

    protected $description = 'Search through Stripe subscriptions for those on a given price plan, ' .
        'and update them to a new price plan. The `STRIPE_SECRET` env variable must be set.';

    private string $priceIdFrom;
    private string $priceIdTo;
    private bool $prorate;

    private int $processed = 0;

    public function handle(): void
    {
        $this->priceIdFrom = $this->argument('priceIdFrom');
        $this->priceIdTo = $this->argument('priceIdTo');
        $this->prorate = $this->option('prorate') !== null;

        $this->info(sprintf(
            'Updating Stripe Subscriptions with price ID \'%s\' to price ID \'%s\'.',
            $this->priceIdFrom,
            $this->priceIdTo
        ));

        foreach ($this->getSubscriptions() as $batch) {
            $this->info(sprintf('Processing %d subscriptions', $batch->count()));
            $batch->each(Closure::fromCallable([$this, 'oneSubscription']));
            $this->processed += $batch->count();
        }

        $this->info(sprintf('Processed %d subscriptions', $this->processed));
    }

    protected function subscriptionParams(): array
    {
        return [
            'limit' => UpdateStripeSubscriptionPrices::PER_PAGE,
            'price' => $this->priceIdFrom,
        ];
    }

    protected function oneSubscription(stdClass $subscription): void
    {
        collect($subscription->items->data)
            ->each(fn($subscriptionItem) => $this->oneSubscriptionItem($subscription->id, $subscriptionItem));

        parent::oneSubscription($subscription);
    }

    private function oneSubscriptionItem(string $subscriptionId, stdClass $subscriptionItem): void
    {
        $this->updatePrice($subscriptionId, $subscriptionItem->id);
    }

    private function updatePrice(string $subscriptionId, string $itemId): void
    {
        try {
            $this->client->post(sprintf('subscriptions/%s', $subscriptionId), [
                'form_params' => [
                    'cancel_at_period_end' => 'false',
                    'proration_behavior' => $this->prorate ? 'create_prorations' : 'none',
                    'items' => [
                        [
                            'id' => $itemId,
                            'price' => $this->priceIdTo,
                        ],
                    ],
                ],
            ]);
        } catch (Exception $exception) {
            $this->error(sprintf('Error (%s -- %s): %s', $subscriptionId, $itemId, $exception->getMessage()));
        }
    }
}
