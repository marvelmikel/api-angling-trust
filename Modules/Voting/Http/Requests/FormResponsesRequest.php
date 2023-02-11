<?php

namespace Modules\Voting\Http\Requests;

use Modules\Voting\Entities\VotingFormQuestion;

class FormResponsesRequest extends HasFormRequest
{
    public function rules(): array
    {
        return $this->votingForm ? collect($this->votingForm->questions)->mapWithKeys(
            function (VotingFormQuestion $question) {
                $key = sprintf("responses.%s", $question->id);
                $rule = [];

                if($question->max) {
                    $rule[] = sprintf('max:%d', $question->max);
                }

                return [$key => $rule];
            }
        )->all() : [];
    }

    public function messages(): array
    {
        return [
            'responses.*.max' => 'Please select up to :max',
        ];
    }

}
