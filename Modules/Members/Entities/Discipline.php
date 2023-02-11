<?php

namespace Modules\Members\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Modules\Members\Entities\Discipline
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Discipline newModelQuery()
 * @method static Builder|Discipline newQuery()
 * @method static Builder|Discipline query()
 * @method static Builder|Discipline whereCreatedAt($value)
 * @method static Builder|Discipline whereId($value)
 * @method static Builder|Discipline whereName($value)
 * @method static Builder|Discipline whereType($value)
 * @method static Builder|Discipline whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Discipline extends Preference
{
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('type', function(Builder $query) {
            $query->where('type', self::class);
        });
    }
}
