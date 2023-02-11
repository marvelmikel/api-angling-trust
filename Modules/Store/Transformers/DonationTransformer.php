<?php

namespace Modules\Store\Transformers;

use Carbon\Carbon;
use EliPett\EntityTransformer\Contracts\EntityTransformer;
use Modules\Store\Entities\Donation;

class DonationTransformer implements EntityTransformer
{
    /**
     * @param Donation $donation
     * @return array
     * @noinspection PhpParameterNameChangedDuringInheritanceInspection
     */
    public function data($donation): array
    {
        return [
            'id' => $donation->id,
            'member_id' => $donation->member_id,
            'amount' => $donation->amount,
            'name' => $donation->name,
            'email' => $donation->email,
            'destination' => $donation->destination,
            'note' => $donation->note,
            'is_subscribed' => $donation->is_subscribed,
            'formatted_amount' => $donation->formatted_amount,
            'completed_at' => Carbon::parse($donation->completed_at)->format('d/m/Y H:i:s'),
        ];
    }

    public function relations(): array
    {
        return [];
    }
}
