<?php

namespace Modules\Voting\Entities;

use Illuminate\Database\Eloquent\Model;

class VotingFormQuestionOption extends Model
{

    protected $fillable = [
        'voting_form_question_id', 'order',
    ];


}
