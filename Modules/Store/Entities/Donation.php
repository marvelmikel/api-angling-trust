<?php

namespace Modules\Store\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Modules\Members\Entities\Member;
use Modules\Store\Traits\HasAmount;

/**
 * Modules\Store\Entities\Donation
 *
 * @property int $id
 * @property int|null $member_id
 * @property int $amount
 * @property string $name
 * @property string $email
 * @property string $destination
 * @property string|null $note
 * @property string|null $completed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $formatted_amount
 * @property-read Member|null $member
 * @method static Builder|Donation newModelQuery()
 * @method static Builder|Donation newQuery()
 * @method static Builder|Donation query()
 * @method static Builder|Donation whereAmount($value)
 * @method static Builder|Donation whereComplete()
 * @method static Builder|Donation whereCompletedAt($value)
 * @method static Builder|Donation whereCreatedAt($value)
 * @method static Builder|Donation whereId($value)
 * @method static Builder|Donation whereIncomplete()
 * @method static Builder|Donation whereMemberId($value)
 * @method static Builder|Donation whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Donation extends Model
{
    use HasAmount;

    protected $fillable = [
        'amount',
        'destination',
        'notes',
        'is_subscribed'
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeWhereComplete(Builder $query): void
    {
        $query->whereNotNull('completed_at');
    }

    public function scopeWhereIncomplete(Builder $query): void
    {
        $query->whereNull('completed_at');
    }

    public function complete(): bool
    {
        $this->completed_at = now();

        return $this->save();
    }
    public function getNoteAttribute(?string $note): string
    {
        return $note ?? '';
    }
}
