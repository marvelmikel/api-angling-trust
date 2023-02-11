<?php declare(strict_types=1);

namespace Modules\Store\Console;

use Generator;
use Modules\Store\Services\StripeClient;
use Illuminate\Console\Command;
use stdClass;

abstract class StripeSubscriptionsBaseCommand extends Command
{
    protected StripeClient $client;
    protected string $lastProcessedSubscription = '';

    protected function subscriptionParams(): array
    {
        return [];
    }

    public function __construct(StripeClient $client)
    {
        parent::__construct();

        $this->client = $client;
    }

    protected function getSubscriptions(): Generator
    {
        do {
            $params = $this->subscriptionParams();

            if ($this->lastProcessedSubscription) {
                $params['starting_after'] = $this->lastProcessedSubscription;
            }

            $query = http_build_query($params);

            $response = $this->client->get(sprintf('subscriptions?%s', $query));
            $body = json_decode($response->getBody()->getContents());

            if ($body->data) {
                yield collect($body->data);
            }
        } while (!!$body->data);
    }

    protected function oneSubscription(stdClass $subscription): void
    {
        $this->lastProcessedSubscription = $subscription->id;
    }

}
