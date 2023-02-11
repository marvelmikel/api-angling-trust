<?php

namespace Modules\Store\Repositories;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Members\Entities\Member;
use Modules\Store\Entities\Donation;

class DonationRepository
{
    public static function findIncompleteForMemberOrNew(Member $member): Donation
    {
        $donation = Donation::query()
            ->where('member_id', $member->id)
            ->whereIncomplete()
            ->firstOrNew([]);

        $donation->member_id = $member->id;

        return $donation;
    }

    public static function createOrUpdateForMember(Member $member, float $amount, string $destination, $note, bool $is_subscribed)
    {
        $donation = self::findIncompleteForMemberOrNew($member);

        if (!$amount) {
            return $donation->delete();
        }

        $donation->amount = $amount;
        $donation->name = $member->full_name;
        $donation->email = $member->user->email;
        $donation->destination = $destination;
        $donation->note = $note;
        $donation->is_subscribed = $is_subscribed;

        $donation->save();

        return $donation;
    }

    public static function store(
        int $amount,
        string $name,
        string $email,
        string $destination,
        string $note,
        ?Member $member = null,
        bool $is_subscribed = false
    ): Donation {
        $donation = new Donation();

        $donation->amount = $amount;
        $donation->name = $name;
        $donation->email = $email;
        $donation->destination = $destination;
        $donation->note = $note;
        $donation->is_subscribed = $is_subscribed;
        $donation->completed_at = now();
        if ($member) {
            $donation->member_id = $member->id;
        }
       
        

        $donation->save();

        return $donation;
    }

    public static function quickSearch(Request $request)
    {
        $query = Donation::query();

        return $query->orderBy(
            $request->get('sort_by', 'completed_at'),
            $request->get('sort', 'desc')
        )->paginate(20, '*', 'paged');
    }

}
