<?php

namespace Modules\Store\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Store\Enums\PaymentProvider;
use Modules\Store\Enums\PaymentPurpose;
use Modules\Store\Repositories\PaymentRepository;
use Modules\Store\Transformers\PaymentMethodTransformer;

class OfflinePaymentController extends Controller
{
    public function makeOfflinePayment(Request $request)
    {
        $member = current_member();

        $price = (int) $request->get('price', $member->category->price);
        $purpose = $request->get('purpose', PaymentPurpose::MEMBERSHIP . " ({$member->membershipType->name})");

        PaymentRepository::createOfflinePaymentRecord(
            $purpose,
            $price,
            $member,
            $request->get('description')
        );

        return $this->success();
    }
}
