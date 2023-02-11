<?php

namespace Modules\Members\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Modules\Members\Entities\Division
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Division newModelQuery()
 * @method static Builder|Division newQuery()
 * @method static Builder|Division query()
 * @method static Builder|Division whereCreatedAt($value)
 * @method static Builder|Division whereId($value)
 * @method static Builder|Division whereName($value)
 * @method static Builder|Division whereType($value)
 * @method static Builder|Division whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Division extends Preference
{
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('type', function(Builder $query) {
            $query->where('type', self::class);
        });
    }
}
