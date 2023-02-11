<?php

namespace Modules\Auth\Entities;

use Carbon\Carbon;
use Cartalyst\Sentinel\Activations\EloquentActivation;
use Cartalyst\Sentinel\Persistences\EloquentPersistence;
use Cartalyst\Sentinel\Reminders\EloquentReminder;
use Cartalyst\Sentinel\Roles\EloquentRole;
use Cartalyst\Sentinel\Throttling\EloquentThrottle;
use Cartalyst\Sentinel\Users\EloquentUser;
use Eloquent;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Subscription;
use Laravel\Passport\Client;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Token;
use Modules\Members\Entities\Member;

/**
 * Modules\Auth\Entities\User
 *
 * @property int $id
 * @property string $email
 * @property string|null $reference
 * @property string $password
 * @property array|null $permissions
 * @property string|null $last_login
 * @property string|null $stripe_id
 * @property string|null $card_brand
 * @property string|null $card_last_four
 * @property string|null $trial_ends_at
 * @property string|null $smart_debit_id
 * @property string|null $smart_debit_frequency
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection|EloquentActivation[] $activations
 * @property-read int|null $activations_count
 * @property-read Collection|Client[] $clients
 * @property-read int|null $clients_count
 * @property-read Member $member
 * @property-read Collection|EloquentPersistence[] $persistences
 * @property-read int|null $persistences_count
 * @property-read Collection|EloquentReminder[] $reminders
 * @property-read int|null $reminders_count
 * @property-read Collection|EloquentRole[] $roles
 * @property-read int|null $roles_count
 * @property-read Collection|Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read Collection|EloquentThrottle[] $throttle
 * @property-read int|null $throttle_count
 * @property-read Collection|Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCardBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCardLastFour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSmartDebitFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSmartDebitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static Builder|User withTrashed()
 * @method static Builder|User withoutTrashed()
 * @mixin Eloquent
 */
class User extends EloquentUser
{
    use HasApiTokens, Authenticatable, Billable, SoftDeletes;

    protected $loginNames = ['reference'];

    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'email',
        'reference',
        'password'
    ];

    public function loginWithToken()
    {
        $token = $this->createToken('Personal Access Token');

        $this->last_login = Carbon::now();
        $this->save();

        return $token;
    }

    public function invalidateAllTokens()
    {
        foreach ($this->tokens as $token) {
            $token->revoke();
        }
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
