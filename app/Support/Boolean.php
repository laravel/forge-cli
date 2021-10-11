<?php

namespace App\Support;

class Boolean
{
    /**
     * Get boolean value from mixed value
     * @param $value
     * @return bool|null
     */
    public static function fromValue($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
}
