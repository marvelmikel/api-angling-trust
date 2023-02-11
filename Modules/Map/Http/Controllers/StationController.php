<?php

namespace Modules\Map\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Map\Entities\Station;
use Modules\Map\Enums\StationType;
use Modules\Map\Repositories\StationRepository;
use Modules\Map\Services\EnvironmentAgency;
use Modules\Map\Services\NationalResource;

class StationController extends Controller
{
    public function index(Request $request)
    {
        $bounds = json_decode($request->input('bounds'), true);

        $stations = Station::query()
            ->whereLatLngBetween($bounds['a'], $bounds['b'])
            ->get();

        return $this->success([
            'stations' => $stations
        ]);
    }

    public function show(Station $station)
    {
        if (!$station->statsHaveBeenFetchedRecently()) {
            if ($station->type === StationType::ENVIRONMENT_AGENCY) {
                $stats = EnvironmentAgency::getStationStats($station);
            }

            if ($station->type === StationType::NATIONAL_RESOURCE) {
                $stats = NationalResource::getStationStats($station);
            }

            StationRepository::updateStats(
                $station, $stats
            );
        }

        if (!$station->readingsHaveBeenFetchedRecently()) {
            if ($station->type === StationType::ENVIRONMENT_AGENCY) {
                $readings = EnvironmentAgency::getStationReadings($station);
            }

            if ($station->type === StationType::NATIONAL_RESOURCE) {
                $readings = NationalResource::getStationReadings($station);
            }

            StationRepository::updateReadings(
                $station, $readings
            );
        }

        $station->load(['readings', 'latestReading']);

        return $this->success([
            'station' => $station
        ]);
    }
}
