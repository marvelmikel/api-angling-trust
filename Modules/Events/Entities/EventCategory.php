<?php

namespace Modules\Events\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * Modules\Events\Entities\EventCategory
 *
 * @property int $id
 * @property int $wp_id
 * @property string $name
 * @property string|null $logo_url
 * @property int|null $ticket_limit
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory newQuery()
 * @method static Builder|EventCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory whereLogoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory whereTicketLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory whereWpId($value)
 * @method static Builder|EventCategory withTrashed()
 * @method static Builder|EventCategory withoutTrashed()
 * @mixin Eloquent
 */
class EventCategory extends Model
{
    use SoftDeletes;

    protected $guarded = [
        'id',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
