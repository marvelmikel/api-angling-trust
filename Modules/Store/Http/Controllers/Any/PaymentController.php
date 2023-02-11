<?php

namespace Modules\Store\Http\Controllers\Any;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function intent(Request $request)
    {
        $data = [
            'amount' => $request->get('amount'),
            'currency' => 'gbp',
        ];

        if ($request->has('description')) {
            $data['description'] = $request->get('description');
        }
        if ($request->get('stripe') === 'draw') {
            $intent = PaymentIntent::create($data, Cashier::stripeOptions([
                'api_key' => env('DRAW_STRIPE_SECRET')
            ]));
        } else {
            $intent = PaymentIntent::create($data, Cashier::stripeOptions([]));
        }

        return $this->success([
            'client_secret' => $intent->client_secret
        ]);
    }
}
