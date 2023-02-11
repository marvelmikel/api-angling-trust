<?php

namespace Modules\Store\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use EliPett\EntityTransformer\Facades\Transform;
use Modules\Members\Entities\Member;
use Modules\Store\Entities\Payment;
use Modules\Store\Transformers\PaymentTransformer;

class PaymentController extends Controller
{
    public function index(Member $member)
    {
        $payments = Payment::query()
            ->where('member_id', $member->id)
            ->orderBy('created_at')
            ->get();

        return $this->success([
            'payments' => Transform::entities($payments, PaymentTransformer::class)
        ]);
    }
}
