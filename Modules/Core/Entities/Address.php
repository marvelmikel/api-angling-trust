<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'line_1',
        'line_2',
        'town',
        'county',
        'postcode'
    ];

    public function addressable()
    {
        return $this->morphTo();
    }

    public function getFullAddressAttribute()
    {
        $parts = [
            $this->line_1,
            $this->line_2,
            $this->town,
            $this->county,
            $this->postcode
        ];

        return implode_skip_empty(', ', $parts);
    }
}
