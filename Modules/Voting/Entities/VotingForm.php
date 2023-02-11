<?php

namespace Modules\Voting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VotingForm extends Model
{
    protected $fillable = [
        'wp_id'
    ];

    protected $appends = [
        'has_submitted'
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(VotingRegistration::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(VotingFormResponse::class)->orderBy('id', 'asc');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(VotingFormQuestion::class)->orderBy('order', 'asc');
    }

    public function getHasSubmittedAttribute(): bool
    {
        return !!$this->responses()->count();
    }
}
