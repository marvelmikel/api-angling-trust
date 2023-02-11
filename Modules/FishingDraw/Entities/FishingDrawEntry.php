<?php

namespace Modules\FishingDraw\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Members\Entities\Member;

class FishingDrawEntry extends Model
{
    use SoftDeletes;

    protected $guarded = [
        'id',
        'deleted_at',
        'updated_at',
        'created_at'
    ];

    public function draw()
    {
        return $this->belongsTo(FishingDraw::class, 'draw_id');
    }

    public function prize()
    {
        return $this->belongsTo(FishingDrawPrize::class, 'prize_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
