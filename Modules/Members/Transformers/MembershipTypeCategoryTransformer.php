<?php

namespace Modules\Members\Transformers;

use EliPett\EntityTransformer\Contracts\EntityTransformer;
use Modules\Members\Entities\MembershipTypeCategory;

class MembershipTypeCategoryTransformer implements EntityTransformer
{
    /**
     * @param MembershipTypeCategory $entity
     * @return array
     */
    public function data($entity): array
    {
        return [
            'id' => $entity->id,
            'at_member' => $entity->at_member,
            'fl_member' => $entity->fl_member,
            'name' => $entity->name,
            'slug' => $entity->slug,
            'price' => $entity->price,
            'price_recurring' => $entity->price_recurring,
            'formatted_price' => $entity->formatted_price,
            'formatted_price_recurring' => $entity->formatted_price_recurring,
        ];
    }

    public function relations(): array
    {
        return [
            //
        ];
    }
}
