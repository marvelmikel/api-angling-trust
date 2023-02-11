<?php

namespace Modules\Map\Services;

use Carbon\Carbon;
use Modules\Map\Entities\Station;
use Modules\Map\Exceptions\EnvironmentAgencyApiException;
use Modules\Map\Transformers\EnvironmentAgency\StationReadingsTransformer;
use Modules\Map\Transformers\EnvironmentAgency\StationStatsTransformer;
use Modules\Map\Transformers\EnvironmentAgency\StationTransformer;

class EnvironmentAgency
{
    const STATIONS_ENDPOINT = 'https://environment.data.gov.uk/flood-monitoring/id/stations';

    public static function getAllStations(): array
    {
        $river_stations = self::getAllStationsWithQualifier('Stage');
        $sea_stations = self::getAllStationsWithQualifier('Tidal Level');

        return array_merge(
            StationTransformer::transformAll($river_stations, ['water_type' => 'river']),
            StationTransformer::transformAll($sea_stations, ['water_type' => 'sea'])
        );
    }

    public static function getAllStationsWithQualifier($qualifier): array
    {
        $parameters = http_build_query([
            'parameter' => 'level',
            'qualifier' => $qualifier
        ]);

        $url = self::STATIONS_ENDPOINT . "?{$parameters}";

        if (!$contents = @file_get_contents($url)) {
            throw new EnvironmentAgencyApiException("Could not retrieve data from: $url");
        }

        $data = json_decode($contents, true);
        return $data['items'];
    }

    public static function getStationStats(Station $station): array
    {
        $url = self::STATIONS_ENDPOINT . "/{$station->ref}";

        if (!$contents = @file_get_contents($url)) {
            throw new EnvironmentAgencyApiException("Could not retrieve data from: $url");
        }

        $data = json_decode($contents, true);
        $station = $data['items'];

        return StationStatsTransformer::transform($station);
    }

    public static function getStationReadings(Station $station)
    {
        $since = Carbon::now()->subDays(4)->format('Y-m-d');

        if ($latestReading = $station->latestReading) {
            if ($latestReading->recorded_at > $since) {
                $since = Carbon::createFromFormat('Y-m-d H:i:s', $latestReading->recorded_at)->format('Y-m-d\TH:i:s\Z');
            }
        }

        $parameters = http_build_query([
            'since' => $since
        ]);

        $url = self::STATIONS_ENDPOINT . "/{$station->ref}/readings?_sorted&{$parameters}";

        if (!$contents = @file_get_contents($url)) {
            throw new EnvironmentAgencyApiException("Could not retrieve data from: $url");
        }

        $data = json_decode($contents, true);
        $readings = $data['items'];

        return StationReadingsTransformer::transformAll($readings);
    }
}
