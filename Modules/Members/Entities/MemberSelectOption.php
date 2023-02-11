<?php

namespace Modules\Members\Entities;

use Cviebrock\EloquentSluggable\Sluggable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Members\Entities\MemberSelectOption
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property string $slug
 * @method static Builder|MemberSelectOption findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static Builder|MemberSelectOption newModelQuery()
 * @method static Builder|MemberSelectOption newQuery()
 * @method static Builder|MemberSelectOption query()
 * @method static Builder|MemberSelectOption whereId($value)
 * @method static Builder|MemberSelectOption whereName($value)
 * @method static Builder|MemberSelectOption whereSlug($value)
 * @method static Builder|MemberSelectOption whereType($value)
 * @mixin Eloquent
 */
class MemberSelectOption extends Model
{
    use Sluggable;

    public $timestamps = false;

    protected $fillable = [
        'type',
        'name'
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'unique' => false
            ]
        ];
    }
}
