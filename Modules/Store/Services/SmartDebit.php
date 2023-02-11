<?php

namespace Modules\Store\Services;

use Modules\Store\Structs\SmartDebitApiResponse;

class SmartDebit
{
    private SmartDebitClient $client;

    public function __construct(SmartDebitClient $client)
    {
        $this->client = $client;
    }

    public function validateRecurringPayment(array $data)
    {
        return $this->client->variable_ddi('validate', [
            'reference_number' => random_reference(),
            'frequency_type' => 'Y',
            'sort_code' => $data['sort_code'],
            'account_number' => $data['account_number'],
            'account_name' => $data['account_name'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name']
        ]);
    }

    public function createRecurringPayment($reference, $amount, array $data)
    {
        $args = [
            'reference_number' => $reference,
            'frequency_type' => 'Y',
            'regular_amount' => $amount,
            'sort_code' => $data['sort_code'],
            'account_number' => $data['account_number'],
            'account_name' => $data['account_name'],
            'payer_reference' => $data['payer_reference'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email_address' => $data['email'],
            'address_1' => $data['address_line_1'],
            'address_2' => $data['address_line_2'],
            'town' => $data['address_town'],
            'county' => $data['address_county'],
            'postcode' => $data['address_postcode']
        ];

        if (isset($data['start_date'])) {
            $args['start_date'] = $data['start_date'];
        }

        return $this->client->variable_ddi('create', $args);
    }

    public function createOneOffPayment($reference, $amount, array $data)
    {
        return $this->client->adhoc_ddi('create', [
            'reference_number' => $reference,
            'sort_code' => $data['sort_code'],
            'account_number' => $data['account_number'],
            'account_name' => $data['account_name'],
            'payer_reference' => $data['payer_reference'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email_address' => $data['email'],
            'address_1' => $data['address_line_1'],
            'address_2' => $data['address_line_2'],
            'town' => $data['address_town'],
            'county' => $data['address_county'],
            'postcode' => $data['address_postcode'],
            'debits' => [
                'debit' => [
                    'amount' => $amount,
                    'date' => now()->addDays(30)->format('Y-m-d'),
                ]
            ]
        ]);
    }

    public function updateRecurringPayment($reference, $data)
    {
        return $this->client->variable_ddi("{$reference}/update", $data);
    }

    public function cancelRecurringPayment($reference)
    {
        return $this->client->variable_ddi("{$reference}/cancel");
    }

    public function reinstateRecurringPayment($reference)
    {
        return $this->client->variable_ddi("{$reference}/reinstate");
    }

    public function getPaymentsTakenOn($collection_date): SmartDebitApiResponse
    {
        $query = http_build_query([
            'query' => [
                'service_user' => [
                    'pslid' => env("SMART_DEBIT_PSLID")
                ],
                'collection_date' => $collection_date
            ]
        ]);

        return $this->client->get(sprintf('api/get_successful_collection_report?%s', $query));
    }

    public function getPayerByReference($reference): SmartDebitApiResponse
    {
        $query = http_build_query([
            'query' => [
                'service_user' => [
                    'pslid' => env("SMART_DEBIT_PSLID"),
                ],
                'reference_number' => $reference,
            ],
        ]);

        return $this->client->post(sprintf('api/data/dump?%s', $query));
    }

    public function getAllPayers(): SmartDebitApiResponse
    {
        $query = http_build_query([
            'query' => [
                'service_user' => [
                    'pslid' => env("SMART_DEBIT_PSLID")
                ]
            ]
        ]);

        return $this->client->post(sprintf('api/data/dump?%s', $query));
    }

    public function updatePrice($reference, $amount)
    {
        return $this->client->variable_ddi($reference . '/update', [
            'default_amount' => $amount
        ]);
    }
}
