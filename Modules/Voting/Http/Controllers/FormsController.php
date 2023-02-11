<?php

namespace Modules\Voting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Voting\Entities\VotingFormResponse;
use Modules\Voting\Entities\VotingRegistration;
use Modules\Voting\Repositories\FormRepository;

class FormsController extends Controller
{

    /**
     * @var FormRepository
     */
    protected $repo;

    public function __construct(FormRepository $forms)
    {
        $this->repo = $forms;
    }

    public function show()
    {
        $member = current_member();
        $form = $this->repo->getForMember($member);

        if(!$form) {
            return response()->json([
                'error' => 'Form Not Found'
            ], 404);
        }

        $form->load('questions.options');

        $registration = VotingRegistration::query()
            ->where('member_id', $member->id)
            ->where('voting_form_id', $form->id)
            ->first();

        return response()->json([
            'form' => $form,
            'has_registered' => ($registration !== null),
            'registration_intention' => $registration ? (bool) $registration->registration_intention: null
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'wp_id' => ['required', 'integer'],
            'acf' => ['required', 'array'],
            'post' => ['required', 'array'],
        ]);

        $this->repo->syncWordPress(
            (int) $request->get('wp_id'),
            (array) $request->get('post'),
            (array) $request->get('acf')
        );

        return response()->json(['Done'], 200);
    }

}
