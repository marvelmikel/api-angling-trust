<?php

namespace Modules\Voting\Repositories;

use Illuminate\Support\Str;
use Modules\Voting\Entities\VotingForm;
use Modules\Voting\Entities\VotingFormQuestion;
use Modules\Voting\Entities\VotingFormQuestionOption;
use function Symfony\Component\String\s;

class FormQuestionsRepository
{

    public function syncWordPress(VotingForm $form, array $questions)
    {
        $i = 0;
        $models = [];
        foreach($questions as $definition) {
            $question = VotingFormQuestion::query()->firstOrNew([
                'voting_form_id' => $form->id,
                'order' => $i,
            ]);

            $method = Str::camel('sync_' . $definition['acf_fc_layout'] . '_question');
            $this->{$method}($question, $definition);

            $i++;
        }

        $form->questions()->saveMany($models);
    }

    protected function syncTextQuestion(VotingFormQuestion $question, array $definition)
    {
        $question->type = VotingFormQuestion::TYPE_TEXT;
        $question->content = $definition['text'];
        $question->show_if_attending = $this->showIfAttending($definition['show_if_member_is'] ?? null);
        $question->save();

        return $question;
    }

    protected function syncForAgainstQuestion(VotingFormQuestion $question, array $definition)
    {
        $question->type = VotingFormQuestion::TYPE_FOR_AGAINST;
        $question->content = $definition['text'];
        $question->show_if_attending = $this->showIfAttending($definition['show_if_member_is'] ?? null);
        $question->save();

        $i = 0;
        foreach($definition['options'] as $o) {
            $option = VotingFormQuestionOption::query()->firstOrNew([
                'voting_form_question_id' => $question->id,
                'order' => $i,
            ]);
            $option->content = $o['option'];
            $question->options()->save($option);
            $i++;
        }

        return $question;
    }

    protected function syncMultipleChoiceQuestion(VotingFormQuestion $question, array $definition)
    {
        $question->type = VotingFormQuestion::TYPE_MULTIPLE_CHOICE;
        $question->max = $definition['maximum_selections'];
        $question->content = $definition['text'];
        $question->show_if_attending = $this->showIfAttending($definition['show_if_member_is'] ?? null);
        $question->save();

        $i = 0;
        foreach($definition['options'] as $o) {
            $option = VotingFormQuestionOption::query()->firstOrNew([
                'voting_form_question_id' => $question->id,
                'order' => $i,
            ]);
            $option->content = $o['text'];
            $question->options()->save($option);
            $i++;
        }

        return $question;
    }

    private function showIfAttending(?string $wpValue = null): ?bool
    {
        switch ($wpValue) {
            case 'attending':
                return true;

            case 'absent':
                return false;

            case 'always':
            default:
                return null;
        }
    }
}
