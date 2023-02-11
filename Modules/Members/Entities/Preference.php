<?php

namespace Modules\Members\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Modules\Members\Entities\Preference
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Preference newModelQuery()
 * @method static Builder|Preference newQuery()
 * @method static Builder|Preference query()
 * @method static Builder|Preference whereCreatedAt($value)
 * @method static Builder|Preference whereId($value)
 * @method static Builder|Preference whereName($value)
 * @method static Builder|Preference whereType($value)
 * @method static Builder|Preference whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Preference extends Model
{
    protected $table = 'preferences';

    protected $fillable = [];
}
