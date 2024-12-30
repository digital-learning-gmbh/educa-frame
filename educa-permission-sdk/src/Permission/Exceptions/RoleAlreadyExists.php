<?php

namespace StuPla\CloudSDK\Permission\Exceptions;

use InvalidArgumentException;

class RoleAlreadyExists extends InvalidArgumentException
{
    public static function create(string $roleName, string $guardName, string $scopeName, string $scopeId)
    {
        return new static("A role `{$roleName}` already exists for guard `{$guardName}` for scope `{$scopeName}` with ID `{$scopeId}`.");
    }
}
