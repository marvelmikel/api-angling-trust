<?php

namespace Modules\Events\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * Modules\Events\Entities\Event
 *
 * @property int $id
 * @property int $wp_id
 * @property int|null $category_id
 * @property string|null $post_type
 * @property string $name
 * @property string $slug
 * @property string $type
 * @property int|null $team_size
 * @property string $department_code
 * @property string $nominal_code
 * @property int|null $min_age
 * @property int|null $max_age
 * @property int $member_only
 * @property int $has_ticket_sales
 * @property int $has_pools_payments
 * @property array|null $pools_payments
 * @property array|null $details
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property Carbon|null $ticket_sales_open
 * @property Carbon|null $ticket_sales_close
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $resend_ticket_on_update
 * @property string|null $bursary_code
 * @property-read EventCategory|null $category
 * @property-read Collection|Ticket[] $tickets
 * @property-read int|null $tickets_count
 * @method static \Illuminate\Database\Eloquent\Builder|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event newQuery()
 * @method static Builder|Event onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereBursaryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDepartmentCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereHasPoolsPayments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereHasTicketSales($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereMaxAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereMemberOnly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereMinAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereNominalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event wherePoolsPayments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event wherePostType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereResendTicketOnUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereTeamSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereTicketSalesClose($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereTicketSalesOpen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereWpId($value)
 * @method static Builder|Event withTrashed()
 * @method static Builder|Event withoutTrashed()
 * @mixin Eloquent
 */
class Event extends Model
{
    use SoftDeletes;

    protected $guarded = [
        'id',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $dates = [
        'ticket_sales_open',
        'ticket_sales_close',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'pools_payments' => 'array',
        'details' => 'array'
    ];

    public function resolveRouteBinding($value)
    {
        return $this->withTrashed()
                ->where('wp_id', $value)
                ->first()
            ?? abort(404);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function category()
    {
        return $this->belongsTo(EventCategory::class);
    }
}
