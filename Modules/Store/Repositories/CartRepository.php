<?php

namespace Modules\Store\Repositories;

use Modules\Auth\Entities\User;
use Modules\Store\Entities\Cart;

class CartRepository
{
    public static function create()
    {
        $cart = new Cart();
        $cart->save();

        return $cart;
    }

    public static function createForUser(User $user)
    {
        $cart = new Cart();
        $cart->user_id = $user->id;
        $cart->save();

        return $cart;
    }

    public static function findForUser(User $user)
    {
        return Cart::query()
            ->where('user_id', $user->id)
            ->first();
    }

    public static function findOrNewForUser(User $user)
    {
        return self::findForUser($user) ?? self::createForUser($user);
    }
}
