<?php

namespace Modules\Core\Traits;

use Modules\Core\Entities\Address;

trait HasAddress
{
    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }
}
