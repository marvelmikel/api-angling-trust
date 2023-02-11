<?php

namespace Modules\Store\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Modules\Store\Structs\SmartDebitApiResponse;

class SmartDebitClient extends Client
{
    public function __construct(array $config = [])
    {
        parent::__construct(array_merge($config, [
            'base_uri' => env('SMART_DEBIT_BASE_URL'),
            'auth' => [
                env('SMART_DEBIT_USERNAME'),
                env('SMART_DEBIT_PASSWORD')
            ]
        ]));
    }

    public function variable_ddi($url, $data = [])
    {
        $params = [
            'variable_ddi' => $data
        ];

        $params['variable_ddi']['service_user']['pslid'] = env('SMART_DEBIT_PSLID');

        return $this->post("api/ddi/variable/{$url}", [
            'form_params' => $params
        ]);
    }

    public function adhoc_ddi($url, $data = [])
    {
        $params = [
            'adhoc_ddi' => $data
        ];

        $params['adhoc_ddi']['service_user']['pslid'] = env('SMART_DEBIT_PSLID');

        return $this->post("api/ddi/adhoc/{$url}", [
            'form_params' => $params
        ]);
    }

    public function get($uri, array $options = []): SmartDebitApiResponse
    {
        try {
            $response = parent::get($uri, $options);

            return new SmartDebitApiResponse($response);
        }
         catch (ClientException $exception) {
             return new SmartDebitApiResponse($exception->getResponse());
         }

    }

    public function post($uri, array $options = [])
    {
        try {

            $response = parent::post($uri, $options);
            return new SmartDebitApiResponse($response);

        } catch (ClientException $exception) {
            return new SmartDebitApiResponse($exception->getResponse());
        }
    }
}
