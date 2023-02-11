<?php

namespace Modules\Store\Services;

use GuzzleHttp\Client;

class StripeClient extends Client
{
    public function __construct(array $config = [])
    {
        parent::__construct(array_merge($config, [
            'base_uri' => 'https://api.stripe.com/v1/',
            'headers' => [
                'Authorization' => sprintf('Bearer %s', env('STRIPE_SECRET'))
            ],
        ]));
    }
}
