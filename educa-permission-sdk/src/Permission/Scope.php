<?php


namespace StuPla\CloudSDK\Permission;


class Scope
{

    public static function getDefaultName(): string
    {
        return config('permission.scope');
    }

    public static function getDefaultId(): string
    {
        return -1;
    }
}