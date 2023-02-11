<?php

namespace Modules\Members\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Modules\Members\Entities\Region
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Region newModelQuery()
 * @method static Builder|Region newQuery()
 * @method static Builder|Region query()
 * @method static Builder|Region whereCreatedAt($value)
 * @method static Builder|Region whereId($value)
 * @method static Builder|Region whereName($value)
 * @method static Builder|Region whereType($value)
 * @method static Builder|Region whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Region extends Preference
{
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('type', function(Builder $query) {
            $query->where('type', self::class);
        });
    }
}
