<?php

namespace Modules\Members\Traits\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Modules\Auth\Entities\User;
use Modules\Store\Entities\Donation;

trait TypeOfMember
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function incompleteDonations()
    {
        return $this->hasMany(Donation::class)
            ->whereIncomplete();
    }

    public function freeze()
    {
        return $this->update([
            'frozen_on' => Carbon::now()
        ]);
    }

    public function unfreeze()
    {
        return $this->update([
            'frozen_on' => null
        ]);
    }

    public function is_frozen()
    {
        return $this->frozen_on !== null;
    }

    public function has_completed_registration()
    {
        return $this->registered_at !== null;
    }

    public function scopeWhereActive(Builder $query)
    {
        $query->where('expires_at', '>', Carbon::now())
            ->whereNotNull('registered_at');
    }

    public function scopeWhereExpired(Builder $query)
    {
        $query->where('expires_at', '<=', Carbon::now())
            ->whereNotNull('registered_at');
    }

    public function scopeWhereIncomplete(Builder $query)
    {
        $query->whereNull('registered_at');
    }
}
