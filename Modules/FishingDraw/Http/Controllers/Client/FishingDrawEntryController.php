<?php

namespace Modules\FishingDraw\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\FishingDraw\Entities\FishingDrawEntry;
use Modules\FishingDraw\Exports\FishingDrawEntryExport;

class FishingDrawEntryController extends Controller
{
    public function export(Request $request)
    {
        $query = FishingDrawEntry::query();

        if($request->get('draw_id') != null || $request->get('prize_id') != null || $request->get('reference') != null) {
            if ($draw_id = $request->get('draw_id')) {
                $query->where('draw_id', $draw_id);
            }

            if ($prize_id = $request->get('prize_id')) {
                $query->where('prize_id', $prize_id);
            }

            if ($reference = $request->get('reference')) {
                $query->where('reference', 'LIKE', "%{$reference}%");
            }
        }

        $entries = $query->orderBy($request->get('sort_by', 'created_at'), $request->get('sort', 'desc'))
            ->get();

        $export = new FishingDrawEntryExport();
        $export->run($entries);

        return $this->success([
            'data' => $export->get_base64()
        ]);
    }
}
