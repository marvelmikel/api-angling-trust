<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = [
        'key'
    ];

    public $timestamps = [];

    public function setValueAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['type'] = 'array';
            $this->attributes['value'] = json_encode($value);
            return;
        }

        $this->attributes['type'] = gettype($value);
        $this->attributes['value'] = $value;
    }

    public function getValueAttribute()
    {
        if ($this->type === 'array') {
            return json_decode($this->attributes['value'], true);
        }

        $value = $this->attributes['value'];
        settype($value, $this->type);

        return $value;
    }
}
