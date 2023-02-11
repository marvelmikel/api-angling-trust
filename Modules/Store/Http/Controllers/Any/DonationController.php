<?php

namespace Modules\Store\Http\Controllers\Any;

use App\Http\Controllers\Controller;
use EliPett\EntityTransformer\Facades\Transform;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as FacadeResponse;
use Modules\Core\Services\WPNotification;
use Modules\Store\Enums\PaymentPurpose;
use Modules\Store\Http\Requests\Any\DonationRequest;
use Modules\Store\Repositories\DonationRepository;
use Modules\Store\Repositories\PaymentRepository;
use Modules\Store\Transformers\DonationTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;



class DonationController extends Controller
{
    public function store(DonationRequest $request): Response
    { 
        $is_subscribed = $request->input('is_subscribed');
        $member = null;
        $name = $request->name();
        $email = $request->email();

        if (can_get_current_user()) {
            $member = current_member();
            $name = $member->full_name;
            $email = $member->user->email;
        }

        $donation = DonationRepository::store(
            $request->amount(),
            $name,
            $email,
            $request->destination(),
            $request->note(),
            $member,
            $is_subscribed,
        );
         

        PaymentRepository::createPaymentRecordForStripe(
            PaymentPurpose::DONATION,
            (int) ($request->amount() * 100),
            $member
        );

        if ($email) {
            WPNotification::sendCustomerNotification('cm-donation', $email);
        }

        return $this->success([
            'donation' => $donation
        ]);
    }

    public function index(Request $request): Response
    {
        $donations = DonationRepository::quickSearch($request);

        return $this->success([
            'donations' => [
                'items' => Transform::entities($donations->items(), DonationTransformer::class),
                'current_page' => $donations->currentPage(),
                'total' => $donations->total(),
                'total_pages' => ceil($donations->total() / 20)
            ],
        ]);
    }

    public function export(Request $request)
   {
   
    $donations = DonationRepository::quickSearch($request);
    $donations = Transform::entities($donations->items(), DonationTransformer::class);

    $csvFile = 'donations-' . Carbon::now()->toDateString() . '.csv';

    $file = fopen(storage_path("exports/$csvFile"), 'w');
    fputcsv($file, ['id', 'amount', 'name', 'email', 'destination', 'note', 'is_subscribed']);

    foreach ($donations as $donation) {
        fputcsv($file, [
            $donation['id'],
            $donation['amount'],
            $donation['name'],
            $donation['email'],
            $donation['destination'],
            $donation['note'],
            $donation['is_subscribed'],
        ]);
    }

    fclose($file);

    $headers = [    'Content-Type' => 'text/csv',    'Content-Disposition' => 'attachment; filename=donations.csv',];

    return response()->download(storage_path("exports/$csvFile"), 'donations.csv', $headers)->deleteFileAfterSend();
    
}

    
    
    
}
