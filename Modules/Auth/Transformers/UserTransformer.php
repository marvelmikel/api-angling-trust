<?php

namespace Modules\Auth\Transformers;

use EliPett\EntityTransformer\Contracts\EntityTransformer;
use Modules\Auth\Entities\User;

class UserTransformer implements EntityTransformer
{
    /**
     * @param User $user
     * @return array
     */
    public function data($user): array
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'reference' => $user->reference
        ];
    }

    public function relations(): array
    {
        return [];
    }
}
