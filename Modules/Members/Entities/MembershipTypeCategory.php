<?php

namespace Modules\Members\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Modules\Store\Traits\HasPrice;

/**
 * Modules\Members\Entities\MembershipTypeCategory
 *
 * @property int $id
 * @property int $membership_type_id
 * @property string $name
 * @property string $slug
 * @property int $at_member
 * @property int $fl_member
 * @property int $price
 * @property int|null $price_recurring
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $formatted_price
 * @property-read mixed $formatted_price_recurring
 * @method static Builder|MembershipTypeCategory newModelQuery()
 * @method static Builder|MembershipTypeCategory newQuery()
 * @method static Builder|MembershipTypeCategory query()
 * @method static Builder|MembershipTypeCategory whereAtMember($value)
 * @method static Builder|MembershipTypeCategory whereCreatedAt($value)
 * @method static Builder|MembershipTypeCategory whereFlMember($value)
 * @method static Builder|MembershipTypeCategory whereId($value)
 * @method static Builder|MembershipTypeCategory whereMembershipTypeId($value)
 * @method static Builder|MembershipTypeCategory whereName($value)
 * @method static Builder|MembershipTypeCategory wherePrice($value)
 * @method static Builder|MembershipTypeCategory wherePriceRecurring($value)
 * @method static Builder|MembershipTypeCategory whereSlug($value)
 * @method static Builder|MembershipTypeCategory whereUpdatedAt($value)
 * @mixin Eloquent
 */
class MembershipTypeCategory extends Model
{
    use HasPrice;

    protected $fillable = [
        'membership_type_id',
        'name',
        'price',
        'price_recurring',
    ];

    public function setPriceRecurringAttribute($value): void
    {
        $this->attributes['price_recurring'] = $value * 100;
    }

    public function getPriceRecurringAttribute($value): int
    {
        return $value ?? $this->price;
    }

    public function getFormattedPriceRecurringAttribute(): string
    {
        if ($this->price_recurring === 0) {
            return 'Free';
        }

        return "£" . number_format($this->price_recurring / 100, 2);
    }
}
