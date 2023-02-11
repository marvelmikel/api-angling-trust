<?php

namespace Modules\Core\Traits;

use Modules\Members\Entities\MemberMeta;

trait HasMeta
{
    public function meta()
    {
        return $this->hasMany(MemberMeta::class, 'member_id');
    }

    public function getMeta($key)
    {
        return $this->meta()
            ->where('key', $key)
            ->first();
    }

    public function getMetaValue($key)
    {
        if ($meta = $this->getMeta($key)) {
            return $meta->getValue();
        }

        return null;
    }

    public function hasMeta($key)
    {
        return $this->meta()
            ->where('key', $key)
            ->exists();
    }

    public function setMeta($key, $value, $cast = null)
    {
        if (!$meta = $this->getMeta($key)) {
            return $this->addMeta($key, $value, $cast);
        }

        $meta->setValue($value);

        if ($cast) {
            $meta->cast = $cast;
        }

        return $meta->save();
    }

    public function setMetaCast($key, $cast)
    {
        if (!$meta = $this->getMeta($key)) {
            return false;
        }

        $meta->cast = $cast;
        $meta->save();
    }

    private function addMeta($key, $value, $cast = null)
    {
        if ($this->hasMeta($key)) {
            return false;
        }

        $meta = new MemberMeta();
        $meta->member_id = $this->id;
        $meta->key = $key;

        $meta->setValue($value);

        if ($cast) {
            $meta->cast = $cast;
        }

        return $meta->save();
    }
}
