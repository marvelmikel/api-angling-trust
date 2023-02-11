<?php

namespace Modules\Members\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modules\Members\Entities\MemberIndex
 *
 * @property int $id
 * @property int $member_id
 * @property int $at_member
 * @property int $fl_member
 * @property string $reference
 * @property int $membership_type_id
 * @property string $membership_type_slug
 * @property string $membership_type_name
 * @property string|null $full_name
 * @property string $email
 * @property string|null $address_postcode
 * @property string|null $primary_contact
 * @property int $is_suspended
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $registered_at
 * @property string|null $updated_at
 * @property-read Member $member
 * @method static Builder|MemberIndex newModelQuery()
 * @method static Builder|MemberIndex newQuery()
 * @method static Builder|MemberIndex query()
 * @method static Builder|MemberIndex whereActive()
 * @method static Builder|MemberIndex whereAddressPostcode($value)
 * @method static Builder|MemberIndex whereAtMember($value)
 * @method static Builder|MemberIndex whereEmail($value)
 * @method static Builder|MemberIndex whereExpired()
 * @method static Builder|MemberIndex whereExpiresAt($value)
 * @method static Builder|MemberIndex whereFlMember($value)
 * @method static Builder|MemberIndex whereFullName($value)
 * @method static Builder|MemberIndex whereId($value)
 * @method static Builder|MemberIndex whereIncomplete()
 * @method static Builder|MemberIndex whereIsSuspended($value)
 * @method static Builder|MemberIndex whereMemberId($value)
 * @method static Builder|MemberIndex whereMembershipTypeId($value)
 * @method static Builder|MemberIndex whereMembershipTypeName($value)
 * @method static Builder|MemberIndex whereMembershipTypeSlug($value)
 * @method static Builder|MemberIndex wherePrimaryContact($value)
 * @method static Builder|MemberIndex whereReference($value)
 * @method static Builder|MemberIndex whereRegisteredAt($value)
 * @method static Builder|MemberIndex whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MemberIndex extends Model
{
    protected $table = 'members_index';
    protected $guarded = ['id'];
    public $timestamps = false;

    protected $dates = [
        'expires_at',
        'registered_at'
    ];

    public function scopeWhereActive(Builder $query)
    {
        $query
            ->where('expires_at', '>=', today())
            ->whereNotNull('registered_at')
            ->orWhereNull('expires_at')
            ->whereNotNull('registered_at');
    }

    public function scopeWhereExpired(Builder $query)
    {
        $query
            ->where('expires_at', '<', today())
            ->whereNotNull('registered_at')
            ->whereNotNull('expires_at');
    }

    public function scopeWhereIncomplete(Builder $query)
    {
        $query->whereNull('registered_at');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
