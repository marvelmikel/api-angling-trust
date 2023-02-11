<?php

namespace Modules\Map\Transformers\EnvironmentAgency;

use Carbon\Carbon;

class StationReadingsTransformer
{
    public static function transformAll(array $readings)
    {
        $items = [];

        foreach ($readings as $reading) {
            if ($item = self::transform($reading)) {
                $items[] = $item;
            }
        }

        return $items;
    }

    public static function transform(array $reading)
    {
        return [
            'value' => $reading['value'],
            'recorded_at' => Carbon::createFromTimeString($reading['dateTime'])->format('Y-m-d H:i')
        ];
    }
}
