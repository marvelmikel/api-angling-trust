<?php

namespace Modules\Map\Services;


use GuzzleHttp\Client;
use Modules\Map\Entities\Station;
use Modules\Map\Transformers\NationalResource\StationTransformer;

class NationalResource
{
    public static function getAllStations()
    {
        $client = new Client();

        $request  = $client->request('GET', 'https://api.naturalresources.wales/riverlevels/v1/all', [
            'headers' => [
                'Ocp-Apim-Subscription-Key' => '8ab5b4e92dc547d9b42f477118a9ab28'
            ]
        ]);

        $response = json_decode($request->getBody(), true);
        $stations = $response['features'];

        return StationTransformer::transformAll($stations);
    }

    public static function getStationStats(Station $station): array
    {
        return [];
    }

    public static function getStationReadings(Station $station)
    {
        return [];
    }
}
