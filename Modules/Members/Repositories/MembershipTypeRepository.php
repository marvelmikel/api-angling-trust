<?php

namespace Modules\Members\Repositories;

use Modules\Members\Entities\MembershipType;

class MembershipTypeRepository
{
    public static function findOrNew($wp_id): MembershipType
    {
        return MembershipType::query()
            ->where('wp_id', $wp_id)
            ->firstOrNew([]);
    }

    public static function createOrUpdate($wp_id, array $data): MembershipType
    {
        $membershipType = self::findOrNew($wp_id);
        $membershipType->fill($data);
        $membershipType->save();

        return $membershipType;
    }
}
