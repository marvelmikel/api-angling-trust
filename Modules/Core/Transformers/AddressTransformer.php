<?php

namespace Modules\Core\Transformers;

use EliPett\EntityTransformer\Contracts\EntityTransformer;
use Modules\Core\Entities\Address;

class AddressTransformer implements EntityTransformer
{
    /**
     * @param Address $address
     * @return array
     */
    public function data($address): array
    {
        return [
            'id' => $address->id,
            'line_1' => $address->line_1,
            'line_2' => $address->line_2,
            'town' => $address->town,
            'county' => $address->county,
            'postcode' => $address->postcode,
            'full_address' => $address->full_address
        ];
    }

    public function relations(): array
    {
        return [];
    }
}
