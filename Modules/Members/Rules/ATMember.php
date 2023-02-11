<?php

namespace Modules\Members\Rules;

use Illuminate\Contracts\Validation\Rule;

class ATMember implements Rule
{
    private $message;

    public function __construct()
    {
        //
    }

    public function passes($attribute, $value)
    {
        $membershipType = request()->get('membership_type_slug');

        $at = request()->get('at_member');
        $fl = request()->get('fl_member');

        if (!$at && !$fl) {
            return $this->error('At least one of AT or FL must be selected');
        }

        if ($membershipType === 'individual-member') {
            if ($at && $fl) {
                return $this->error('AT and FL cannot both be selected');
            }
        }

        if ($membershipType === 'fishery') {
            if (!$at || !$fl) {
                return $this->error('Both AT and FL must be selected');
            }
        }

        if ($membershipType === 'trade-member') {
            if (!$at) {
                return $this->error('AT must be selected');
            }

            if ($fl) {
                return $this->error('FL cannot be selected');
            }
        }

        if ($membershipType === 'affiliate') {
            if (!$at) {
                return $this->error('AT must be selected');
            }

            if ($fl) {
                return $this->error('FL cannot be selected');
            }
        }

        if ($membershipType === 'consultatives') {
            if (!$at) {
                return $this->error('AT must be selected');
            }

            if ($fl) {
                return $this->error('FL cannot be selected');
            }
        }

        if ($membershipType === 'caag') {
            if (!$at) {
                return $this->error('AT must be selected');
            }

            if ($fl) {
                return $this->error('FL cannot be selected');
            }
        }

        if ($membershipType === 'charter-boat') {
            if (!$at) {
                return $this->error('AT must be selected');
            }
        }

        if ($membershipType === 'federation') {
            if (!$at) {
                return $this->error('AT must be selected');
            }
        }

        if ($membershipType === 'salmon-fishery-board') {
            if ($at) {
                return $this->error('AT cannot be selected');
            }

            if (!$fl) {
                return $this->error('FL must be selected');
            }
        }

        return true;
    }

    public function error($message)
    {
        $this->message = $message;
        return false;
    }

    public function message()
    {
        return $this->message;
    }
}
