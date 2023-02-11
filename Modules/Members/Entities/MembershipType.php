<?php

namespace Modules\Members\Entities;

use Cviebrock\EloquentSluggable\Sluggable;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Modules\Store\Traits\HasPrice;

/**
 * Modules\Members\Entities\MembershipType
 *
 * @property int $id
 * @property int $wp_id
 * @property string $name
 * @property string $slug
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $formatted_price
 * @property-write mixed $price
 * @method static Builder|MembershipType findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static Builder|MembershipType newModelQuery()
 * @method static Builder|MembershipType newQuery()
 * @method static \Illuminate\Database\Query\Builder|MembershipType onlyTrashed()
 * @method static Builder|MembershipType query()
 * @method static Builder|MembershipType whereCreatedAt($value)
 * @method static Builder|MembershipType whereDeletedAt($value)
 * @method static Builder|MembershipType whereId($value)
 * @method static Builder|MembershipType whereName($value)
 * @method static Builder|MembershipType whereSlug($value)
 * @method static Builder|MembershipType whereUpdatedAt($value)
 * @method static Builder|MembershipType whereWpId($value)
 * @method static \Illuminate\Database\Query\Builder|MembershipType withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MembershipType withoutTrashed()
 * @mixin Eloquent
 */
class MembershipType extends Model
{
    use SoftDeletes, HasPrice, Sluggable;

    protected $fillable = [
        'wp_id',
        'name',
        'price'
    ];

    public function resolveRouteBinding($value)
    {
        return $this->withTrashed()
                ->where('wp_id', $value)
                ->first()
            ?? abort(404);
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
}
