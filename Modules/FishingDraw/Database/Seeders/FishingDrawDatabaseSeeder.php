<?php

namespace Modules\FishingDraw\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\FishingDraw\Database\Seeders\FishingDrawEntry\FishingDrawEntrySeeder;

class FishingDrawDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(FishingDrawEntrySeeder::class);
    }
}
