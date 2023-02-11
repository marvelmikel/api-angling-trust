<?php

namespace Modules\Voting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VotingFormQuestion extends Model
{

    const TYPE_TEXT = 'text';
    const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    const TYPE_FOR_AGAINST = 'for_against';

    protected $fillable = [
        'order', 'voting_form_id',
    ];

    protected $appends = [
        'type_studly',
    ];

    public function wantsResponse()
    {
        return ($this->type !== static::TYPE_TEXT);
    }

    public function form()
    {
        return $this->belongsTo(VotingForm::class);
    }

    public function options() {
        return $this->hasMany(VotingFormQuestionOption::class);
    }

    public function getTypeStudlyAttribute()
    {
        return Str::studly($this->type);
    }

}
