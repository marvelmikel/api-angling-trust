<?php

namespace Modules\Map\Entities;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Map\Entities\Station
 *
 * @property int $id
 * @property string $ref
 * @property string $type
 * @property string $name
 * @property string $water_type
 * @property string $lat
 * @property string $lng
 * @property array|null $stats
 * @property \Illuminate\Support\Carbon|null $stats_updated_at
 * @property \Illuminate\Support\Carbon|null $readings_updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read StationReading|null $latestReading
 * @property-read Collection|StationReading[] $readings
 * @property-read int|null $readings_count
 * @method static Builder|Station newModelQuery()
 * @method static Builder|Station newQuery()
 * @method static Builder|Station query()
 * @method static Builder|Station whereCreatedAt($value)
 * @method static Builder|Station whereId($value)
 * @method static Builder|Station whereLat($value)
 * @method static Builder|Station whereLatLngBetween(array $a, array $b)
 * @method static Builder|Station whereLng($value)
 * @method static Builder|Station whereName($value)
 * @method static Builder|Station whereReadingsUpdatedAt($value)
 * @method static Builder|Station whereRef($value)
 * @method static Builder|Station whereStats($value)
 * @method static Builder|Station whereStatsUpdatedAt($value)
 * @method static Builder|Station whereType($value)
 * @method static Builder|Station whereUpdatedAt($value)
 * @method static Builder|Station whereWaterType($value)
 * @mixin Eloquent
 */
class Station extends Model
{
    protected $fillable = [
        'type',
        'ref',
        'name',
        'water_type',
        'lat',
        'lng'
    ];

    protected $dates = [
        'stats_updated_at',
        'readings_updated_at'
    ];

    protected $casts = [
        'stats' => 'json'
    ];

    public function scopeWhereLatLngBetween(Builder $query, array $a, array $b)
    {
        $query
            ->whereBetween('lat', [$a['lat'], $b['lat']])
            ->whereBetween('lng', [$a['lng'], $b['lng']]);
    }

    public function statsHaveBeenFetchedRecently()
    {
        return $this->stats_updated_at >= Carbon::now()->subDay();
    }

    public function readings()
    {
        return $this->hasMany(StationReading::class)
            ->where('recorded_at', '>', Carbon::now()->startOfDay()->subDays(4))
            ->orderBy('recorded_at');
    }

    public function readingsHaveBeenFetchedRecently()
    {
        return $this->readings_updated_at >= Carbon::now()->subMinutes(15);
    }

    public function latestReading()
    {
        return $this->hasOne(StationReading::class)
            ->orderByDesc('recorded_at')
            ->limit(1);
    }
}
