<?php

namespace Modules\Map\Transformers\NationalResource;

use Modules\Map\Enums\StationType;

class StationTransformer
{
    public static function transformAll($stations)
    {
        $items = [];

        foreach ($stations as $station) {
            if ($item = self::transform($station)) {
                $items[] = $item;
            }
        }

        return $items;
    }

    public static function transform($station)
    {
        try {

            return [
                'ref' => $station['properties']['Location'],
                'type' => StationType::NATIONAL_RESOURCE,
                'name' => $station['properties']['NameEN'],
                'lat' => $station['geometry']['coordinates'][1],
                'lng' => $station['geometry']['coordinates'][0]
            ];

        } catch (\Exception $exception) {
            return null;
        }
    }
}
