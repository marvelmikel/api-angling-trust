<?php

namespace Modules\Members\Entities;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Members\Entities\MemberMeta
 *
 * @property int $id
 * @property int $member_id
 * @property string $key
 * @property string|null $cast
 * @property string|null $value
 * @method static Builder|MemberMeta newModelQuery()
 * @method static Builder|MemberMeta newQuery()
 * @method static Builder|MemberMeta query()
 * @method static Builder|MemberMeta whereCast($value)
 * @method static Builder|MemberMeta whereId($value)
 * @method static Builder|MemberMeta whereKey($value)
 * @method static Builder|MemberMeta whereMemberId($value)
 * @method static Builder|MemberMeta whereValue($value)
 * @mixin Eloquent
 */
class MemberMeta extends Model
{
    protected $table = 'member_meta';

    public $timestamps = false;

    protected $fillable = [];

    public function setValue($value)
    {
        if (is_array($value)) {
            $this->value = json_encode($value);
            $this->cast = 'array';
            return;
        }

        if (is_int($value)) {
            $this->value = $value;
            $this->cast = 'integer';
            return;
        }

        if (is_float($value)) {
            $this->value = $value;
            $this->cast = 'float';
            return;
        }

        $this->value = $value;
    }

    public function getValue()
    {
        if ($this->cast) {
            $method = "castTo" . ucwords($this->cast);

            if (method_exists($this, $method)) {
                return $this->$method();
            }
        }

        return $this->value;
    }

    public function getRawValue()
    {
        return $this->value;
    }

    private function castToBoolean()
    {
        return (boolean) $this->value;
    }

    private function castToArray()
    {
        return json_decode($this->value, true);
    }

    private function castToInteger()
    {
        return (int) $this->value;
    }

    private function castToFloat()
    {
        return (float) $this->value;
    }

    private function castToDate()
    {
        return Carbon::createFromFormat('Y-m-d', $this->value);
    }

    private function castToDateTime()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->value);
    }
}
