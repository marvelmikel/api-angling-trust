<?php

namespace Modules\FishingDraw\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FishingDrawPrize extends Model
{
    use SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function draw()
    {
        return $this->belongsTo(FishingDraw::class, 'draw_id');
    }
}
