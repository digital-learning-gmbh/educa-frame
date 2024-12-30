<?php

namespace StuPla\CloudSDK\rocketchat\Facades;

use Illuminate\Support\Facades\Facade;

class UserClient extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rc-user-client';
    }
}