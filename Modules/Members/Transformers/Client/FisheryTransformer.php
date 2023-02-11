<?php

namespace Modules\Members\Transformers\Client;

use EliPett\EntityTransformer\Contracts\EntityTransformer;
use Modules\Auth\Transformers\UserTransformer;
use Modules\Members\Entities\Fishery;
use Modules\Store\Transformers\DonationTransformer;

class FisheryTransformer implements EntityTransformer
{
    /**
     * @param Fishery $fishery
     * @return array
     */
    public function data($fishery): array
    {
        $data = [
            'id' => $fishery->id,
            'has_completed_registration' => $fishery->has_completed_registration(),
            'auto_renew' => $fishery->auto_renew,
            'is_frozen' => $fishery->is_frozen(),
            'registered_at' => $fishery->registered_at,
            'expires_at' => $fishery->expires_at,
            'created_at' => $fishery->created_at
        ];

        return $data;
    }

    public function relations(): array
    {
        return [
            'user' => UserTransformer::class,
            'donations' => DonationTransformer::class
        ];
    }
}
