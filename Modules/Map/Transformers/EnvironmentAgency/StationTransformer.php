<?php

namespace Modules\Map\Transformers\EnvironmentAgency;

use Modules\Map\Enums\StationType;
use Modules\Map\Services\EnvironmentAgency;

class StationTransformer
{
    public static function transformAll($stations, $data = [])
    {
        $items = [];

        foreach ($stations as $station) {
            if ($item = self::transform($station, $data)) {
                $items[] = $item;
            }
        }

        return $items;
    }

    public static function transform($station, $data = [])
    {
        try {

            return array_merge($data, [
                'ref' => self::getRef($station),
                'type' => StationType::ENVIRONMENT_AGENCY,
                'name' => $station['label'],
                'lat' => $station['lat'],
                'lng' => $station['long']
            ]);

        } catch (\Exception $exception) {
            return null;
        }
    }

    private static function getRef($station)
    {
        return substr($station['@id'], strlen(EnvironmentAgency::STATIONS_ENDPOINT));
    }
}
