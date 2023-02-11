<?php

namespace Modules\Events\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Modules\Members\Entities\Member;

/**
 * Modules\Events\Entities\PurchasedTicket
 *
 * @property int $id
 * @property int|null $member_id
 * @property int $event_id
 * @property int $ticket_id
 * @property string $reference
 * @property array $data
 * @property string|null $payment_id
 * @property int $price
 * @property Carbon|null $purchased_at
 * @property Carbon|null $canceled_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Event $event
 * @property-read Member|null $member
 * @property-read Ticket $ticket
 * @method static Builder|PurchasedTicket newModelQuery()
 * @method static Builder|PurchasedTicket newQuery()
 * @method static Builder|PurchasedTicket query()
 * @method static Builder|PurchasedTicket whereCanceledAt($value)
 * @method static Builder|PurchasedTicket whereCreatedAt($value)
 * @method static Builder|PurchasedTicket whereData($value)
 * @method static Builder|PurchasedTicket whereEventId($value)
 * @method static Builder|PurchasedTicket whereId($value)
 * @method static Builder|PurchasedTicket whereMemberId($value)
 * @method static Builder|PurchasedTicket wherePaymentId($value)
 * @method static Builder|PurchasedTicket wherePrice($value)
 * @method static Builder|PurchasedTicket wherePurchasedAt($value)
 * @method static Builder|PurchasedTicket whereReference($value)
 * @method static Builder|PurchasedTicket whereTicketId($value)
 * @method static Builder|PurchasedTicket whereUpdatedAt($value)
 * @method static Builder|PurchasedTicket whereUpdatedRecently()
 * @mixin Eloquent
 */
class PurchasedTicket extends Model
{
    protected $fillable = [
        'event_id',
        'ticket_id',
        'your_details'
    ];

    protected $casts = [
        'data' => 'json'
    ];

    protected $dates = [
        'purchased_at',
        'canceled_at'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class)->withTrashed();
    }

    public function member()
    {
        return $this->belongsTo(Member::class)->withTrashed();
    }

    public function basket()
    {
        return $this->belongsTo(TicketBasket::class, 'basket_id');
    }

    public function scopeWhereUpdatedRecently(Builder $query)
    {
        $query->where('updated_at', '>=', now()->subHours(26));
    }

    public function cancel()
    {
        $this->canceled_at = now();

        return $this->save();
    }
}
