<?php

namespace Modules\Store\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Modules\Store\Entities\StripeSubscriptionPlan
 *
 * @property int $id
 * @property int $category_id
 * @property string $api_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|StripeSubscriptionPlan newModelQuery()
 * @method static Builder|StripeSubscriptionPlan newQuery()
 * @method static Builder|StripeSubscriptionPlan query()
 * @method static Builder|StripeSubscriptionPlan whereApiId($value)
 * @method static Builder|StripeSubscriptionPlan whereCategoryId($value)
 * @method static Builder|StripeSubscriptionPlan whereCreatedAt($value)
 * @method static Builder|StripeSubscriptionPlan whereId($value)
 * @method static Builder|StripeSubscriptionPlan whereUpdatedAt($value)
 * @mixin Eloquent
 */
class StripeSubscriptionPlan extends Model
{
    protected $fillable = [];
}
