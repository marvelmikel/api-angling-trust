<?php

namespace Modules\Map\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Map\Entities\Station;

class MarkerController extends Controller
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
}
