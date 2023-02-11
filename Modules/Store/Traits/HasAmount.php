<?php

namespace Modules\Store\Traits;

trait HasAmount
{
    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value * 100;
    }

    public function getFormattedAmountAttribute()
    {
        if ($this->amount === 0) {
            return 'None';
        }

        return "£" . number_format($this->amount / 100, 2);
    }
}
