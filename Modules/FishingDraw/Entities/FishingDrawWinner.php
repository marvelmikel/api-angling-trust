<?php

namespace Modules\FishingDraw\Entities;

use Illuminate\Database\Eloquent\Model;

class FishingDrawWinner extends Model
{
    protected $fillable = [];

    public function draw()
    {
        return $this->belongsTo(FishingDraw::class);
    }

    public function prize()
    {
        return $this->belongsTo(FishingDrawPrize::class);
    }

    public function entry()
    {
        return $this->belongsTo(FishingDrawEntry::class);
    }
}
