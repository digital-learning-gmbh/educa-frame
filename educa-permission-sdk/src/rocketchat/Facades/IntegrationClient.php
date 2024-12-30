<?php

namespace StuPla\CloudSDK\rocketchat\Facades;

use Illuminate\Support\Facades\Facade;

class IntegrationClient extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rc-integration-client';
    }
}