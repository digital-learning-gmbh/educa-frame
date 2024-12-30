<?php

namespace StuPla\CloudSDK\rocketchat\Facades;

use Illuminate\Support\Facades\Facade;

class SettingClient extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rc-setting-client';
    }
}