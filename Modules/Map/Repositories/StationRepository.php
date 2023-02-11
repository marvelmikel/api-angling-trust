<?php

namespace Modules\Map\Repositories;

use Carbon\Carbon;
use Modules\Map\Entities\Station;

class StationRepository
{
    public static function updateOrCreate(array $data)
    {
        $station = Station::firstOrNew([
            'ref' => $data['ref']
        ]);

        $station->fill($data);
        $station->save();

        return $station;
    }

    public static function updateStats(Station $station, array $stats)
    {
        $station->stats = $stats;
        $station->stats_updated_at = Carbon::now();

        return $station->save();
    }

    public static function updateReadings(Station $station, array $readings)
    {
        $cutoff = Carbon::now()->startOfDay()->subDays(4);

        $station->readings()
            ->where('recorded_at', '<', $cutoff)
            ->delete();

        foreach ($readings as $reading) {
            StationReadingRepository::updateOrCreateForStation($station, $reading);
        }

        $station->readings_updated_at = Carbon::now();

        return $station->save();
    }
}
