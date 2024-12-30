<?php namespace SoapBox\Formatter;

use Illuminate\Support\Arr;

class ArrayHelpers
{
    public static function isAssociative($array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    public static function dotKeys(array $data)
    {
        return array_keys(Arr::dot($data));
    }

    public static function dot(array $data)
    {
        return Arr::dot($data);
    }

    public static function set(array &$data, $key, $value)
    {
        Arr::set($data, $key, $value);
    }

}
