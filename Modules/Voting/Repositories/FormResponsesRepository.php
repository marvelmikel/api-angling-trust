<?php

namespace Modules\Voting\Repositories;

use Modules\Members\Entities\Member;
use Modules\Voting\Entities\VotingForm;
use Modules\Voting\Entities\VotingFormQuestion;
use Modules\Voting\Entities\VotingFormResponse;
use Modules\Voting\Entities\VotingRegistration;
use Modules\Voting\Http\Requests\FormResponsesRequest;

class FormResponsesRepository
{

    protected VotingForm $form;
    protected Member $member;

    public function setMember(Member $member)
    {
        $this->member = $member;
    }

    public function setForm(VotingForm $form)
    {
        $this->form = $form;
    }

    public function persist(FormResponsesRequest $request)
    {
        $this->form
            ->questions
            ->filter(fn(VotingFormQuestion $question) => $question->type !== VotingFormQuestion::TYPE_TEXT)
            ->each(function (VotingFormQuestion $question) use ($request) {
                $response = $this->findResponse($request, $question);

                $method = sprintf("persist%sResponse", $question->type_studly);
                if ($response !== null) {
                    $this->{$method}($question, $response);
                }
            });
    }

    protected function persistForAgainstResponse(VotingFormQuestion $question, $responses)
    {
        collect($responses)->each(fn($response) => $this->persistResponse(
            $question->id,
            $question->order,
            $response['content'],
            $response['position'],
        ));
    }

    protected function persistMultipleChoiceResponse(VotingFormQuestion $question, $responses)
    {
        collect($responses)->each(fn($response) => $this->persistResponse(
            $question->id,
            $question->order,
            $question->id,
            $response,
        ));
    }


    protected function persistResponse(
        $questionId,
        $questionOrder,
        $question,
        $response
    ): void {
        VotingFormResponse::create([
            'voting_form_id' => $this->form->id,
            'member_id' => $this->member->id,
            'voting_form_question_id' => $questionId,
            'question' => $question,
            'question_order' => $questionOrder,
            'response' => $response,
        ]);
    }

    protected function findResponse(FormResponsesRequest $request, VotingFormQuestion $question)
    {
        return isset($request->get('responses')[$question->id])
            ? $request->get('responses')[$question->id]
            : null;
    }

}
