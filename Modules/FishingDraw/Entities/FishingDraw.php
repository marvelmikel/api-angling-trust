<?php

namespace Modules\FishingDraw\Entities;

use Illuminate\Database\Eloquent\Model;

class FishingDraw extends Model
{
    protected $guarded = [
        'id',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public function prizes()
    {
        return $this->hasMany(FishingDrawPrize::class, 'draw_id');
    }
}
