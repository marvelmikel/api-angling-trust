<?php

namespace Modules\Map\Repositories;

use Modules\Map\Entities\StationReading;
use Modules\Map\Entities\Station;

class StationReadingRepository
{
    public static function updateOrCreateForStation(Station $station, array $data)
    {
        $reading = StationReading::firstOrNew([
            'station_id' => $station->id,
            'recorded_at' => $data['recorded_at']
        ]);

        $reading->value = $data['value'];
        $reading->save();

        return $reading;
    }
}
