<?php

namespace Modules\Members\Transformers\Client;

use EliPett\EntityTransformer\Contracts\EntityTransformer;
use Modules\Auth\Transformers\UserTransformer;
use Modules\Members\Entities\Club;
use Modules\Members\Entities\TradeMember;
use Modules\Store\Transformers\DonationTransformer;

class TradeMemberTransformer implements EntityTransformer
{
    /**
     * @param TradeMember $tradeMember
     * @return array
     */
    public function data($tradeMember): array
    {
        $data = [
            'id' => $tradeMember->id,
            'has_completed_registration' => $tradeMember->has_completed_registration(),
            'auto_renew' => $tradeMember->auto_renew,
            'is_frozen' => $tradeMember->is_frozen(),
            'registered_at' => $tradeMember->registered_at,
            'expires_at' => $tradeMember->expires_at,
            'created_at' => $tradeMember->created_at
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
