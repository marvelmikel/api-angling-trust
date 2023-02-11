<?php

namespace Modules\Members\Http\Controllers\Any;

use App\Http\Controllers\Controller;
use EliPett\EntityTransformer\Facades\Transform;
use Modules\Members\Entities\Discipline;
use Modules\Members\Entities\Division;
use Modules\Members\Entities\Region;
use Modules\Members\Transformers\PreferenceTransformer;

class PreferenceController extends Controller
{
    public function index()
    {
        return $this->success([
            'disciplines' => Transform::entities(Discipline::all(), PreferenceTransformer::class),
            'divisions' => Transform::entities(Division::all(), PreferenceTransformer::class),
            'regions' => Transform::entities(Region::all(), PreferenceTransformer::class)
        ]);
    }

    public function disciplines()
    {
        return $this->success([
            'disciplines' => Transform::entities(Discipline::all(), PreferenceTransformer::class),
        ]);
    }
}
