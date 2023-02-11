<?php

namespace Modules\Voting\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Voting\Entities\VotingRegistration;
use Modules\Voting\Exceptions\VotingFormNotFoundForMemberException;
use Modules\Voting\Http\Requests\RegistrationRequest;

class RegistrationController extends Controller
{
    public function store(RegistrationRequest $request)
    {
        try {
            $form = $request->getVotingForm();
        } catch (VotingFormNotFoundForMemberException $e) {
            return response()->json([
                'error' => 'Form Not Found'
            ], 404);
        }

        VotingRegistration::create([
            'member_id' => current_member()->id,
            'voting_form_id' => $form->id,
            'registration_intention' => (int) $request->registration(),
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

}
