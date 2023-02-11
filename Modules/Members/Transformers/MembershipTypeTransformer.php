<?php

namespace Modules\Members\Transformers;

use EliPett\EntityTransformer\Contracts\EntityTransformer;
use Modules\Members\Entities\MembershipType;

class MembershipTypeTransformer implements EntityTransformer
{
    /**
     * @param MembershipType $membershipType
     * @return array
     */
    public function data($membershipType): array
    {
        return [
            'id' => $membershipType->id,
            'name' => $membershipType->name,
            'slug' => $membershipType->slug
        ];
    }

    public function relations(): array
    {
        return [
            //
        ];
    }
}
