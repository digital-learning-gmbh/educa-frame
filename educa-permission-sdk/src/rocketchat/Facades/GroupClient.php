<?php

namespace StuPla\CloudSDK\rocketchat\Facades;

use Illuminate\Support\Facades\Facade;

class GroupClient extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rc-group-client';
    }
}