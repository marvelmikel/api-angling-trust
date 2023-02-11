<?php

namespace Modules\Map\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Modules\Map\Entities\StationReading
 *
 * @property int $id
 * @property int $station_id
 * @property string $value
 * @property string $recorded_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Station|null $station
 * @method static Builder|StationReading newModelQuery()
 * @method static Builder|StationReading newQuery()
 * @method static Builder|StationReading query()
 * @method static Builder|StationReading whereCreatedAt($value)
 * @method static Builder|StationReading whereId($value)
 * @method static Builder|StationReading whereRecordedAt($value)
 * @method static Builder|StationReading whereStationId($value)
 * @method static Builder|StationReading whereUpdatedAt($value)
 * @method static Builder|StationReading whereValue($value)
 * @mixin Eloquent
 */
class StationReading extends Model
{
    protected $fillable = [
        'station_id',
        'recorded_at'
    ];

    public function station()
    {
        return $this->hasOne(Station::class);
    }
}
