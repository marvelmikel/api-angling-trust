<?php

namespace Modules\Store\Console;

use Closure;
use Illuminate\Support\Collection;
use stdClass;

class RemoveProrationLineItems extends StripeSubscriptionsBaseCommand
{
    private const PER_PAGE = 100;

    protected $signature = 'store:remove-proration-line-items
        {priceId : The price ID to search for}
        ';

    protected $description = 'Delete ALL proration entries from UPCOMING invoices related to a given price ID';

    private string $priceId;

    private int $processed = 0;

    private Collection $prorations;

    public function handle(): void
    {
        $this->priceId = $this->argument('priceId');
        $this->prorations = new Collection();

        foreach ($this->getSubscriptions() as $batch) {
            $this->info(sprintf('Processing %d subscriptions', $batch->count()));
            $batch->each(Closure::fromCallable([$this, 'oneSubscription']));
            $this->processed += $batch->count();
        }

        $this->deleteProrations();

        $this->info(sprintf('Processed %d subscriptions', $this->processed));
    }

    protected function oneSubscription(stdClass $subscription): void
    {
        $lines = $this->getUpcomingInvoiceLines($subscription->id);
        $prorations = $lines
            ->filter(fn(stdClass $line) => $line->proration)
            ->map(fn(stdClass $line) => $line->id);

        $this->info(sprintf('Found %d prorations for \'%s\'', $prorations->count(), $subscription->id));

        $this->prorations = $this->prorations->concat($prorations);

        parent::oneSubscription($subscription);
    }

    protected function subscriptionParams(): array
    {
        return [
            'limit' => RemoveProrationLineItems::PER_PAGE,
            'price' => $this->priceId,
        ];
    }

    private function getUpcomingInvoiceLines(string $subscriptionId): Collection
    {
        $params = [
            'limit' => RemoveProrationLineItems::PER_PAGE,
            'subscription' => $subscriptionId,
        ];

        $query = http_build_query($params);

        $response = $this->client->get(sprintf('invoices/upcoming/lines?%s', $query));

        return collect(json_decode($response->getBody()->getContents())->data);
    }

    private function deleteProrations(): void
    {
        $count = $this->prorations->count();
        $this->info(sprintf('Found %d prorations to delete', $count));

        $this->prorations->each(
            function ($itemID, $ix) use ($count): void {
                $this->client->delete(sprintf('invoiceitems/%s', $itemID));
                $this->info(sprintf('Deleting proration %d of %d', $ix + 1, $count));
            }
        );
    }
}
