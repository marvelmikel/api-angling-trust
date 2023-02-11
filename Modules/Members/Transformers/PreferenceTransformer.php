<?php

namespace Modules\Members\Transformers;

use EliPett\EntityTransformer\Contracts\EntityTransformer;

class PreferenceTransformer implements EntityTransformer
{
    public function data($preference): array
    {
        return [
            'id' => $preference->id,
            'name' => $preference->name
        ];
    }

    public function relations(): array
    {
        return [];
    }
}
