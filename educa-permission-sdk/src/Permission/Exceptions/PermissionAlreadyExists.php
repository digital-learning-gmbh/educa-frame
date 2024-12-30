<?php

namespace StuPla\CloudSDK\Permission\Exceptions;

use InvalidArgumentException;

class PermissionAlreadyExists extends InvalidArgumentException
{
    public static function create(string $permissionName, string $guardName, string $scope)
    {
        return new static("A `{$permissionName}` permission already exists for guard `{$guardName}` and scope `{$scope}`.");
    }
}
