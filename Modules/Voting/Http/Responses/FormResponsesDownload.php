<?php

namespace Modules\Voting\Http\Responses;

use League\Csv\Writer;
use Modules\Members\Entities\Member;
use Modules\Voting\Entities\VotingFormQuestion;
use Modules\Voting\Entities\VotingRegistration;
use SplTempFileObject;
use Modules\Voting\Entities\VotingForm;

class FormResponsesDownload
{

    protected $form;
    protected $csv;
    protected $members;
    protected $responses;
    protected $registrations;

    protected $headers = [];
    protected $rows = [];

    public function __construct(VotingForm $form)
    {
        $this->form = $form;
        $this->responses = $this->form->responses;
        $this->registrations = $this->form->registrations;
        $this->members = array_unique($this->registrations->pluck('member_id')->toArray());
        $this->csv = Writer::createFromFileObject(new SplTempFileObject());

        foreach($this->members as $member) {
            $this->rows[$member] = [];
        }
    }

    public function make()
    {
        // Add each member name as the first column and add the Name header
        $memberObjects = Member::withTrashed()->findMany($this->members);

        $this->headers[] = 'Name';
        $this->headers[] = 'Attending AGM';
        $this->headers[] = 'Member Number';
        $this->headers[] = 'Email';

        foreach($this->members as $member) {
            $this->rows[$member][] = $memberObjects->find($member)->full_name;
            $this->rows[$member][] = $this->getRegistrationIntention($member);
            $this->rows[$member][] = $memberObjects->find($member)->user->reference;
            $this->rows[$member][] = $memberObjects->find($member)->user->email;
        }

        foreach($this->form->questions as $question) {
            if(!$question->wantsResponse()) {
                continue;
            }

            $method = 'process' . $question->type_studly . 'Question';
            $this->{$method}($question);
        }

        $this->csv->insertOne($this->headers);

        $this->csv->insertAll($this->rows);

        return $this;
    }

    protected function getRegistrationIntention($member)
    {
        $votingRegistration = VotingRegistration::query()
            ->where('member_id', $member)
            ->where('voting_form_id', $this->form->id)
            ->firstOrFail();

        return $votingRegistration->registration_intention;
    }

    protected function processForAgainstQuestion(VotingFormQuestion $question)
    {
        $this->headers[] = $question->order . ': ' . $question->content;

        foreach($question->options as $option) {
            $this->headers[] = $option->content;
        }

        foreach($this->members as $member) {
            $responses = $this->responses->where('member_id', $member)->where('voting_form_question_id', $question->id);
            $this->rows[$member][] = '-';

            foreach($question->options as $option) {
                $response = $responses->where('question', $option->content)->first();
                $this->rows[$member][] = $response ? $response->response : 'none';
            }
        }
    }

    protected function processMultipleChoiceQuestion(VotingFormQuestion $question)
    {
        $this->headers[] = $question->order . ': ' . $question->content;

        foreach($question->options as $option) {
            $this->headers[] = $option->content;
        }

        foreach($this->members as $member) {
            $responses = $this->responses->where('member_id', $member)->where('voting_form_question_id', $question->id)->pluck('response')->toArray();
            $this->rows[$member][] = '-';

            foreach($question->options as $option) {
                $this->rows[$member][] = in_array($option->content, $responses) ? '1' : '';
            }
        }
    }

    public function send()
    {
        return response((string) $this->csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="form-responses.csv"',
        ]);
    }

}
