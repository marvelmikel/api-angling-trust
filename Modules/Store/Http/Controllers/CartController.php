<?php

namespace Modules\Store\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Store\Repositories\CartRepository;

class CartController extends Controller
{
    public function get()
    {
        $user = current_user();
        $cart = CartRepository::findOrNewForUser($user);

        return $this->success([
            'cart_id' => $cart->id
        ]);
    }
}
