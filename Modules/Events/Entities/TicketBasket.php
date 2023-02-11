<?php

namespace Modules\Events\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Members\Entities\Member;

class TicketBasket extends Model
{
    const LIFETIME = 15;

    protected $fillable = [];

    protected $dates = [
        'purchased_at',
        'expires_at'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class)->withTrashed();
    }

    public function frozenTickets()
    {
        return $this->hasMany(FrozenTicket::class, 'basket_id');
    }

    public function purchasedTickets()
    {
        return $this->hasMany(PurchasedTicket::class, 'basket_id');
    }

    public function updatePrice()
    {
        $price = 0;

        foreach ($this->purchasedTickets as $ticket) {
            $price += $ticket->price;
        }

        $this->price = $price;
        return $this->save();
    }
}
