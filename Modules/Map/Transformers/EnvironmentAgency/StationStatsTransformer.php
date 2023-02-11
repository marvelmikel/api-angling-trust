<?php

namespace Modules\Map\Transformers\EnvironmentAgency;

use Carbon\Carbon;

class StationStatsTransformer
{
    public static function transform(array $station)
    {
        if (!isset($station['stageScale'])) {
            return [];
        }

        $records = $station['stageScale'];

        $data = [];

        if (isset($records['highestRecent'])) {
            $data['highest_recent'] = self::getRecord($records['highestRecent']);
        }

        if (isset($records['maxOnRecord'])) {
            $data['max_on_record'] = self::getRecord($records['maxOnRecord']);
        }

        if (isset($records['minOnRecord'])) {
            $data['min_on_record'] = self::getRecord($records['minOnRecord']);
        }

        if (isset($records['typicalRangeHigh']) && isset($records['typicalRangeLow'])) {
            $data['typical_range'] = [
                'high' => $records['typicalRangeHigh'],
                'low' => $records['typicalRangeLow']
            ];
        }

        return $data;
    }

    private static function getRecord($data)
    {
        return [
            'value' => $data['value'],
            'recorded_at' => Carbon::createFromTimeString($data['dateTime'])->format('Y-m-d H:i')
        ];
    }
}
