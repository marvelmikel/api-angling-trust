<?php

namespace Modules\Store\Entities;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Modules\Auth\Entities\User;
use Modules\Events\Entities\Ticket;

/**
 * Modules\Store\Entities\Cart
 *
 * @property int $id
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection|Ticket[] $tickets
 * @property-read int|null $tickets_count
 * @property-read User|null $user
 * @method static Builder|Cart newModelQuery()
 * @method static Builder|Cart newQuery()
 * @method static Builder|Cart query()
 * @method static Builder|Cart whereCreatedAt($value)
 * @method static Builder|Cart whereId($value)
 * @method static Builder|Cart whereUpdatedAt($value)
 * @method static Builder|Cart whereUserId($value)
 * @mixin Eloquent
 */
class Cart extends Model
{
    protected $fillable = [];

    const DAYS_TILL_EXPIRY = 30;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tickets()
    {
        return $this->morphedByMany(Ticket::class, 'item', 'cart_items');
    }

    public function hasExpired()
    {
        return $this->created_at < Carbon::now()->subDays(self::DAYS_TILL_EXPIRY);
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('whereNotExpired', function(Builder $query) {
            $query->where('created_at', '>=', Carbon::now()->subDays(self::DAYS_TILL_EXPIRY));
        });
    }
}
