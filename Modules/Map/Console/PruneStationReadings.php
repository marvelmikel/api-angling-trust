<?php

namespace Modules\Map\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Map\Entities\StationReading;

class PruneStationReadings extends Command
{
    protected $name = 'map:prune-station-readings';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $cutoff = Carbon::now()->startOfDay()->subDays(4);

        $readings = StationReading::query()
            ->where('recorded_at', '<', $cutoff)
            ->get();

        $count = count($readings);

        $this->info("Pruning $count readings.");

        foreach ($readings as $reading) {
            $reading->delete();
        }

        $this->info("Readings pruned.");
    }
}
