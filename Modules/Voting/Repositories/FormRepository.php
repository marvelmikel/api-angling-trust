<?php

namespace Modules\Voting\Repositories;

use Modules\Members\Entities\Member;
use Modules\Voting\Entities\VotingForm;
use Modules\Voting\Enums\FormType;

class FormRepository
{

    protected $questions;

    public function __construct(FormQuestionsRepository $questionsRepository)
    {
        $this->questions = $questionsRepository;
    }

    public function getByWordPressPostId(int $wpId)
    {
        return VotingForm::query()->where('wp_id', $wpId)->firstOrFail();
    }

    public function getById(int $id)
    {
        return VotingForm::query()->findOrFail($id);
    }

    public function syncWordPress(int $wpId, array $post, array $acf) : VotingForm
    {
        /** @var VotingForm $form */
        $form = VotingForm::query()->firstOrNew([
            'wp_id' => $wpId,
        ])->forceFill([
            'title' => $post['post_title'],
            'text' => $post['post_content'],
            'confirmation_text' => $acf['confirmation_text'],
            'rejection_text' => $acf['rejection_text'],
            'intro_text' => $acf['intro_text'],
            'intro_confirmation_text' => $acf['intro_confirmation_text'],
            'intro_rejection_text' => $acf['intro_rejection_text'],
            'membership_type' => $acf['permissions']['membership_type'],
            'at' => in_array('at', $acf['permissions']['member_of']),
            'fl' => in_array('fl', $acf['permissions']['member_of']),
        ]);

        $form->save();

        $this->questions->syncWordPress($form, $acf['questions']);

        return $form;
    }

    public function getForMember(Member $member) : ?VotingForm
    {
        $form = VotingForm::query();

        switch ($member->membershipType->slug) {
            case 'individual-member':
                $form->where('membership_type', FormType::INDIVIDUAL);
                break;
            default:
                $form->where('membership_type', FormType::ORGANISATION);
                break;
        }

        return $form->first();
    }

}
