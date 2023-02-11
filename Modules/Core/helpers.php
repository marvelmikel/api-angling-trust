<?php

if (!function_exists('get_option')) {
    function get_option(string $key, $default = null)
    {
        $option = \Modules\Core\Entities\Option::query()
            ->where('key', $key)
            ->first();

        if ($option) {
            return $option->value;
        }

        return $default;
    }
}
