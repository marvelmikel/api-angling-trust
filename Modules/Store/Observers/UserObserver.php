<?php

namespace Modules\Store\Observers;

use Modules\Auth\Entities\User;

class UserObserver
{
    public function created(User $user)
    {
        try {
            $user->createAsStripeCustomer();
        } catch (\Exception $exception) {
            // todo: notify admin of failure
        }
    }

    public function deleted(User $user)
    {
        try {
            $user->asStripeCustomer()->delete();
        } catch (\Exception $exception) {
            // todo: notify admin of failure
        }
    }
}
