<?php

namespace Modules\Map\Console;

use Illuminate\Console\Command;
use Modules\Map\Repositories\StationRepository;
use Modules\Map\Services\EnvironmentAgency;
use Modules\Map\Services\NationalResource;

class UpdateStations extends Command
{
    protected $name = 'map:update-stations';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->handleStations(EnvironmentAgency::class);
//        $this->handleStations(NationalResource::class);
    }

    private function handleStations($api)
    {
        try {

            $stations = $api::getAllStations();

            foreach ($stations as $station) {
                StationRepository::updateOrCreate($station);
            }

        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
