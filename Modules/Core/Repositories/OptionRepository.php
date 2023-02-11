<?php

namespace Modules\Core\Repositories;

use Modules\Core\Entities\Option;

class OptionRepository
{
    public static function findOrNew(string $key)
    {
        return Option::query()
            ->where('key', $key)
            ->firstOrNew([
                'key' => $key
            ]);
    }

    public static function createOrUpdate(string $key, $value)
    {
        $option = self::findOrNew($key);
        $option->value = $value;
        $option->save();

        return $option;
    }
}
