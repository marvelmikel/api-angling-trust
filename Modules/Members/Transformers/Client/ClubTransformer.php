<?php

namespace Modules\Members\Transformers\Client;

use EliPett\EntityTransformer\Contracts\EntityTransformer;
use Modules\Auth\Transformers\UserTransformer;
use Modules\Members\Entities\Club;
use Modules\Store\Transformers\DonationTransformer;

class ClubTransformer implements EntityTransformer
{
    /**
     * @param Club $club
     * @return array
     */
    public function data($club): array
    {
        $data = [
            'id' => $club->id,
            'has_completed_registration' => $club->has_completed_registration(),
            'auto_renew' => $club->auto_renew,
            'is_frozen' => $club->is_frozen(),
            'registered_at' => $club->registered_at,
            'expires_at' => $club->expires_at,
            'created_at' => $club->created_at
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
