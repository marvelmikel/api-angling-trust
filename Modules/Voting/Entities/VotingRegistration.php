<?php

namespace Modules\Voting\Entities;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;

class VotingRegistration extends Model
{
    use HasTimestamps;

    protected $fillable = [
        'member_id',
        'voting_form_id',
        'registration_intention',
        'notified'
    ];

}
