<?php

namespace Modules\Members\Entities;

use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Modules\Auth\Enums\SiteOrigin;

/**
 * Modules\Members\Entities\OnboardRecord
 *
 * @property int $id
 * @property int $member_id
 * @property int $emails_sent
 * @property Carbon|null $email_last_sent
 * @property Carbon|null $completed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Member $member
 * @method static Builder|OnboardRecord newModelQuery()
 * @method static Builder|OnboardRecord newQuery()
 * @method static Builder|OnboardRecord query()
 * @method static Builder|OnboardRecord whereCompletedAt($value)
 * @method static Builder|OnboardRecord whereCreatedAt($value)
 * @method static Builder|OnboardRecord whereEmailLastSent($value)
 * @method static Builder|OnboardRecord whereEmailsSent($value)
 * @method static Builder|OnboardRecord whereId($value)
 * @method static Builder|OnboardRecord whereMemberId($value)
 * @method static Builder|OnboardRecord whereNotCompleted()
 * @method static Builder|OnboardRecord wherePass(int $pass)
 * @method static Builder|OnboardRecord whereUpdatedAt($value)
 * @mixin Eloquent
 */
class OnboardRecord extends Model
{
    protected $fillable = [];

    protected $dates = [
        'email_last_sent',
        'completed_at'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeWhereNotCompleted(Builder $query)
    {
        $query->whereNull('completed_at');
    }

    public function scopeWherePass(Builder $query, int $pass)
    {
        if ($pass === 1) {
            $query
                ->where('emails_sent', 0);
        } else {
            $query
                ->where('emails_sent', $pass - 1)
                ->where('email_last_sent', '<=', now()->subMonth());
        }
    }

    public function sendEmail(): bool
    {
        try {

            $member = $this->member;
            $to = $member->user->email;

            if ($member->at_member) {
                $origin = SiteOrigin::ANGLING_TRUST;
            } else {
                $origin = SiteOrigin::FISH_LEGAL;
            }

            $path = env('WP_PATH');
            $wp_cli = env('WP_CLI');

            shell_exec("{$wp_cli} --path='{$path}' send_onboarding_email {$to} {$origin}");

            $this->emails_sent = $this->emails_sent + 1;
            $this->email_last_sent = now();
            $this->save();

            return true;

        } catch (Exception $exception) {
            return false;
        }
    }
}
