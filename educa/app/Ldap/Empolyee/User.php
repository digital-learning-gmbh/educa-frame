<?php

namespace App\Ldap\Empolyee;

use LdapRecord\Models\Model;

class User extends \LdapRecord\Models\ActiveDirectory\User
{

    protected $connection = 'default';

    /**
     * The object classes of the LDAP model.
     *
     * @var array
     */
    public static $objectClasses = [];
}
