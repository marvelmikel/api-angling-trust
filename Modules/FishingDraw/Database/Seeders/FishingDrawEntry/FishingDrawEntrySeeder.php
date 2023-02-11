<?php

namespace Modules\FishingDraw\Database\Seeders\FishingDrawEntry;

use Illuminate\Database\Seeder;
use Modules\FishingDraw\Entities\FishingDraw;
use Modules\FishingDraw\Entities\FishingDrawEntry;
use Modules\FishingDraw\Entities\FishingDrawPrize;

class FishingDrawEntrySeeder extends Seeder
{
    public function run()
    {
        /** @var array $members */
        $entries = factory(FishingDrawEntry::class, (int) $this->command->ask('Number of Entries', 0))->raw();

        foreach ($entries as $data) {
            $draw = FishingDraw::inRandomOrder()->limit(1)->first();
            $prize = FishingDrawPrize::inRandomOrder()->where('draw_id', $draw->id)->limit(1)->first();

            $entry = new FishingDrawEntry();
            $entry->draw_id = $draw->id;
            $entry->prize_id = $prize->id;
            $entry->fill($data);

            $entry->save();
        }
    }
}
