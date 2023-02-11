<?php

namespace Modules\FishingDraw\Http\Controllers\Any;

use App\Http\Controllers\Controller;
use Modules\Core\Services\WPNotification;
use Modules\FishingDraw\Entities\FishingDraw;
use Modules\FishingDraw\Entities\FishingDrawEntry;
use Modules\FishingDraw\Entities\FishingDrawPrize;
use Modules\FishingDraw\Http\Requests\FishingDrawEntry\StoreFishingDrawEntryRequest;

class FishingDrawEntryController extends Controller
{
    public function store(FishingDraw $fishingDraw, FishingDrawPrize $fishingDrawPrize, StoreFishingDrawEntryRequest $request)
    {
        $entry = new FishingDrawEntry();

        $entry->draw_id = $fishingDraw->id;
        $entry->prize_id = $fishingDrawPrize->id;
        $entry->reference = $this->getNewReference();
        $entry->fill($request->all());

        $entry->save();

        WPNotification::sendCustomerNotification('cm-fishing-draw-entry', $request->get('email'), [
            'entry_id' => $entry->id
        ]);

        return [
            'success' => true
        ];
    }

    private function getNewReference()
    {
        return mt_rand(100, 999) . now()->getTimestamp();
    }
}
