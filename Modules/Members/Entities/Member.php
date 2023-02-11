<?php

namespace Modules\Members\Entities;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Entities\User;
use Modules\Core\Traits\HasMeta;
use Modules\Events\Entities\PurchasedTicket;
use Modules\Store\Entities\Donation;
use Modules\Store\Enums\PaymentProvider;

/**
 * Modules\Members\Entities\Member
 *
 * @property int $id
 * @property int $user_id
 * @property int $membership_type_id
 * @property int|null $category_id
 * @property int $at_member
 * @property int $fl_member
 * @property string|null $title
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $full_name
 * @property string|null $home_telephone
 * @property string|null $mobile_telephone
 * @property string|null $address_line_1
 * @property string|null $address_line_2
 * @property string|null $address_town
 * @property string|null $address_county
 * @property string|null $address_postcode
 * @property string|null $notes
 * @property string|null $payment_provider
 * @property int|null $payment_is_recurring
 * @property string|null $card_expires_month
 * @property string|null $card_expires_year
 * @property string|null $membership_pack_sent_at
 * @property int $is_suspended
 * @property int $is_imported
 * @property int $opt_in_1
 * @property int $opt_in_2
 * @property \Illuminate\Support\Carbon|null $registered_at
 * @property \Illuminate\Support\Carbon|null $renewed_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property string|null $created_via
 * @property string|null $created_by
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read MembershipTypeCategory|null $category
 * @property-read Collection|Donation[] $donations
 * @property-read int|null $donations_count
 * @property-read mixed $has_stripe_portal
 * @property-read Collection|Donation[] $incompleteDonations
 * @property-read int|null $incomplete_donations_count
 * @property-read MemberIndex|null $index
 * @property-read MembershipType $membershipType
 * @property-read Collection|MemberMeta[] $meta
 * @property-read int|null $meta_count
 * @property-read Collection|PurchasedTicket[] $purchasedTickets
 * @property-read int|null $purchased_tickets_count
 * @property-read User $user
 * @method static Builder|Member newModelQuery()
 * @method static Builder|Member newQuery()
 * @method static \Illuminate\Database\Query\Builder|Member onlyTrashed()
 * @method static Builder|Member query()
 * @method static Builder|Member whereActive()
 * @method static Builder|Member whereAddressCounty($value)
 * @method static Builder|Member whereAddressLine1($value)
 * @method static Builder|Member whereAddressLine2($value)
 * @method static Builder|Member whereAddressPostcode($value)
 * @method static Builder|Member whereAddressTown($value)
 * @method static Builder|Member whereAtMember($value)
 * @method static Builder|Member whereCardExpiresMonth($value)
 * @method static Builder|Member whereCardExpiresYear($value)
 * @method static Builder|Member whereCategoryId($value)
 * @method static Builder|Member whereCreatedAt($value)
 * @method static Builder|Member whereCreatedBy($value)
 * @method static Builder|Member whereCreatedVia($value)
 * @method static Builder|Member whereDeletedAt($value)
 * @method static Builder|Member whereExpired()
 * @method static Builder|Member whereExpiresAt($value)
 * @method static Builder|Member whereFirstName($value)
 * @method static Builder|Member whereFlMember($value)
 * @method static Builder|Member whereFullName($value)
 * @method static Builder|Member whereHasStripePortal()
 * @method static Builder|Member whereHomeTelephone($value)
 * @method static Builder|Member whereId($value)
 * @method static Builder|Member whereIncomplete()
 * @method static Builder|Member whereIsImported($value)
 * @method static Builder|Member whereIsSuspended($value)
 * @method static Builder|Member whereLastName($value)
 * @method static Builder|Member whereMembershipPackSentAt($value)
 * @method static Builder|Member whereMembershipTypeId($value)
 * @method static Builder|Member whereMobileTelephone($value)
 * @method static Builder|Member whereNotes($value)
 * @method static Builder|Member whereOptIn1($value)
 * @method static Builder|Member whereOptIn2($value)
 * @method static Builder|Member wherePaymentIsRecurring($value)
 * @method static Builder|Member wherePaymentProvider($value)
 * @method static Builder|Member whereRegisteredAt($value)
 * @method static Builder|Member whereRenewedAt($value)
 * @method static Builder|Member whereTitle($value)
 * @method static Builder|Member whereUpdatedAt($value)
 * @method static Builder|Member whereUpdatedRecently()
 * @method static Builder|Member whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Member withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Member withoutTrashed()
 * @mixin Eloquent
 */
class Member extends Model
{
    private const LIFETIME_CATEGORIES = [
        'life',
        'fl-life',
        'life-membership-premier',
    ];

    use SoftDeletes;
    use HasMeta;

    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'home_telephone',
        'mobile_telephone',
        'address_line_1',
        'address_line_2',
        'address_town',
        'address_county',
        'address_postcode',
        'notes',
        'opt_in_1',
        'opt_in_2'
    ];

    protected $dates = [
        'membership_pack_sent',
        'registered_at',
        'renewed_at',
        'expires_at',
        'created_at',
        'updated_at'
    ];

    public function membershipType(): BelongsTo
    {
        return $this->belongsTo(MembershipType::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MembershipTypeCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function incompleteDonations()
    {
        return $this->donations()->whereIncomplete();
    }

    public function suspend(): bool
    {
        $this->is_suspended = true;

        return $this->save();
    }

    public function unsuspend(): bool
    {
        $this->is_suspended = false;

        return $this->save();
    }

    public function markMembershipPackAsSent(): bool
    {
        $this->membership_pack_sent = now();

        return $this->save();
    }

    public function hasCompletedRegistration(): bool
    {
        return $this->registered_at !== null;
    }

    public function purchasedTickets(): HasMany
    {
        return $this->hasMany(PurchasedTicket::class)
            ->whereNotNull('purchased_at');
    }

    public function getJoinedEvents(): array
    {
        $events = [];

        foreach ($this->purchasedTickets as $purchasedTicket) {
            $events[] = $purchasedTicket->event;
        }

        $ids = [];

        foreach ($events as $event) {
            $ids[] = $event->wp_id;
        }

        return $ids;
    }

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

    public function hasExpired(?Carbon $on = null): bool
    {
        if ($this->expires_at === null) {
            return false;
        }

        return $this->expires_at < ($on ?? Carbon::now());
    }

    public function expiresSoon(?Carbon $on = null): bool
    {
        if ($this->expires_at === null) {
            return false;
        }

        return $this->expires_at < ($on ?? Carbon::now())->addMonth();
    }

    public function scopeWhereIncomplete(Builder $query)
    {
        $query->whereNull('registered_at');
    }

    public function getTotalDonated(): int
    {
        $donations = $this->donations()
            ->whereComplete()
            ->get();

        $total = 0;

        foreach ($donations as $donation) {
            $total += $donation->amount;
        }

        return $total;
    }

    public function renew()
    {
        if ($this->expires_at >= today()) {
            $this->expires_at = $this->expires_at->addYear();
        } else {
            $this->expires_at = today()->addYear();
        }

        $this->renewed_at = now();
        $this->updated_at = now();
        $this->save();
    }

    public function updateFullName(): bool
    {
        $membershipType = $this->membershipType;

        if (in_array($membershipType->slug, ['individual-member', 'coach'])) {
            $this->full_name = $this->first_name . ' ' . $this->last_name;
        } else {
            $this->full_name = $this->getMetaValue('club_name');
        }

        return $this->save();
    }

    public function scopeWhereUpdatedRecently(Builder $query)
    {
        $query->where('updated_at', '>=', now()->subHours(26));
    }

    public function index(): HasOne
    {
        return $this->hasOne(MemberIndex::class);
    }

    public function hasStripePortal(): bool
    {
        return $this->payment_provider === PaymentProvider::STRIPE && ((bool) $this->payment_is_recurring) === true;
    }

    public function getHasStripePortalAttribute(): bool
    {
        return $this->payment_provider === PaymentProvider::STRIPE && ((bool) $this->payment_is_recurring) === true;
    }

    public function scopeWhereHasStripePortal(Builder $query): Builder
    {
        return $query->where('payment_provider', PaymentProvider::STRIPE)->where('payment_is_recurring', true );
    }


    public function shouldAlertAboutStripe(): bool
    {
        if ($this->is_imported) {
            return false;
        }

        if ($this->payment_provider !== PaymentProvider::STRIPE || ((bool) $this->payment_is_recurring) !== true) {
            return false;
        }

        if (!$this->card_expires_year || !$this->card_expires_month) {
            return false;
        }

        $expiry = Carbon::createFromFormat('Y-n-d', $this->card_expires_year . '-' . $this->card_expires_month . '-01')
            ->endOfMonth();

        return $expiry <= Carbon::now()->addMonth();
    }

    public function isLifetimeMember(): bool
    {
        return !!$this->categeory && in_array($this->category->slug, self::LIFETIME_CATEGORIES);
    }
}
