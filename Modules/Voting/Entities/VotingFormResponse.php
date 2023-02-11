<?php

namespace Modules\Voting\Entities;

use Illuminate\Database\Eloquent\Model;

class VotingFormResponse extends Model
{

    protected $fillable = [
        'voting_form_id',
        'question',
        'question_order',
        'response',
        'member_id',
        'voting_form_question_id',
    ];

}
