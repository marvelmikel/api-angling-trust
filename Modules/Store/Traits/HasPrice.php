<?php

namespace Modules\Store\Traits;

trait HasPrice
{
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $value * 100;
    }

    public function getFormattedPriceAttribute()
    {
        if ($this->price === 0) {
            return 'Free';
        }

        return "£" . number_format($this->price / 100, 2);
    }
}
