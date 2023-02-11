<?php

namespace Modules\Events\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Modules\Store\Traits\HasPrice;

/**
 * Modules\Events\Entities\Ticket
 *
 * @property int $id
 * @property string $ref
 * @property int $event_id
 * @property string $name
 * @property int $total_available
 * @property int $price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Event $event
 * @property-read Collection|FrozenTicket[] $frozenTickets
 * @property-read int|null $frozen_tickets_count
 * @property-read mixed $formatted_price
 * @property-read mixed $total_remaining
 * @property-read Collection|PurchasedTicket[] $purchasedTickets
 * @property-read int|null $purchased_tickets_count
 * @method static Builder|Ticket newModelQuery()
 * @method static Builder|Ticket newQuery()
 * @method static Builder|Ticket query()
 * @method static Builder|Ticket whereCreatedAt($value)
 * @method static Builder|Ticket whereEventId($value)
 * @method static Builder|Ticket whereId($value)
 * @method static Builder|Ticket whereName($value)
 * @method static Builder|Ticket wherePrice($value)
 * @method static Builder|Ticket whereRef($value)
 * @method static Builder|Ticket whereTotalAvailable($value)
 * @method static Builder|Ticket whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Ticket extends Model
{
    use HasPrice;

    protected $fillable = [
        'ref',
        'name',
        'total_available',
        'price'
    ];

    public function frozenTickets()
    {
        return $this->hasMany(FrozenTicket::class);
    }

    public function purchasedTickets()
    {
        return $this->hasMany(PurchasedTicket::class)
            ->whereNotNull('purchased_at');
    }

    public function getTotalRemainingAttribute()
    {
        return $this->total_available - $this->frozenTickets()->count() - $this->purchasedTickets()->whereNull('canceled_at')->count();
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
