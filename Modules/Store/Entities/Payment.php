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
 * Modules\Store\Entities\Payment
 *
 * @property int $id
 * @property int|null $member_id
 * @property string $reference
 * @property string $purpose
 * @property string|null $description
 * @property string $payment_provider
 * @property int $amount
 * @property bool $auto_renew
 * @property string|null $completed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $formatted_amount
 * @property-read Member|null $member
 * @method static Builder|Payment newModelQuery()
 * @method static Builder|Payment newQuery()
 * @method static Builder|Payment query()
 * @method static Builder|Payment whereAmount($value)
 * @method static Builder|Payment whereAutoRenew($value)
 * @method static Builder|Payment whereCompleted()
 * @method static Builder|Payment whereCompletedAt($value)
 * @method static Builder|Payment whereCreatedAt($value)
 * @method static Builder|Payment whereDescription($value)
 * @method static Builder|Payment whereId($value)
 * @method static Builder|Payment whereMemberId($value)
 * @method static Builder|Payment wherePaymentProvider($value)
 * @method static Builder|Payment wherePurpose($value)
 * @method static Builder|Payment whereReference($value)
 * @method static Builder|Payment whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Payment extends Model
{
    use HasAmount;

    protected $casts = [
        'auto_renew' => 'boolean'
    ];

    protected $fillable = [];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeWhereCompleted(Builder $query): void
    {
        $query->whereNotNull('completed_at');
    }

    public function complete(): bool
    {
        $this->completed_at = now();
        return $this->save();
    }
}
