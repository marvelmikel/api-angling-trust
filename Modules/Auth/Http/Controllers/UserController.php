<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function me()
    {
        return $this->success([
            'user' => current_user()
        ]);
    }
}
