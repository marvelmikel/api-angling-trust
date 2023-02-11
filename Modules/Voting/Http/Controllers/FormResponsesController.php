<?php

namespace Modules\Voting\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Voting\Exceptions\VotingFormNotFoundForMemberException;
use Modules\Voting\Http\Responses\FormResponsesDownload;
use Modules\Voting\Jobs\SendConfirmations;
use Modules\Voting\Repositories\FormRepository;
use Modules\Voting\Http\Requests\FormResponsesRequest;
use Modules\Voting\Repositories\FormResponsesRepository;

class FormResponsesController extends Controller
{

    protected FormRepository $forms;
    protected FormResponsesRepository $responses;

    public function __construct(FormRepository $forms, FormResponsesRepository $responses)
    {
        $this->forms = $forms;
        $this->responses = $responses;
    }

    public function index(Request $request)
    {
        if($request->get('wp_id')) {
            $form = $this->forms->getByWordPressPostId($request->get('wp_id'));
        } elseif($request->get('id')) {
            $form = $this->forms->getById($request->get('wp_id'));
        } else {
            return response()->json([], 404);
        }

        if($request->has('download')) {
            $response = (new FormResponsesDownload($form))->make();
            return $response->send();
        }

        return response()->json([
            'responses' => $form->responses,
        ]);
    }

    public function store(FormResponsesRequest $request)
    {
        $member = current_member();

        try {
            $form = $request->getVotingForm();
        } catch (VotingFormNotFoundForMemberException $e) {
            return response()->json([
                'error' => 'Form Not Found'
            ], 404);
        }

        $this->responses->setMember(current_member());
        $this->responses->setForm($form);
        $this->responses->persist($request);

        $form->save();

        dispatch(new SendConfirmations($member, $form));

        return response()->json([
            'success' => true,
        ]);
    }

}
