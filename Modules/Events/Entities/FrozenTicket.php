<?php

namespace Modules\Events\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Events\Entities\FrozenTicket
 *
 * @property int $id
 * @property int $ticket_id
 * @property string $token
 * @property string $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Ticket $ticket
 * @method static Builder|FrozenTicket newModelQuery()
 * @method static Builder|FrozenTicket newQuery()
 * @method static Builder|FrozenTicket query()
 * @method static Builder|FrozenTicket whereCreatedAt($value)
 * @method static Builder|FrozenTicket whereExpiresAt($value)
 * @method static Builder|FrozenTicket whereId($value)
 * @method static Builder|FrozenTicket whereTicketId($value)
 * @method static Builder|FrozenTicket whereToken($value)
 * @method static Builder|FrozenTicket whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FrozenTicket extends Model
{
    const TOKEN_LIFETIME = 15;

    protected $fillable = [
       'token'
    ];

    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('not_expired', function (Builder $query) {
            $query->where('expires_at', '>', Carbon::now());
        });
    }

    public function basket()
    {
        return $this->belongsTo(TicketBasket::class, 'basket_id');
    }
}
