<?php

namespace App\Ldap\Student;

use LdapRecord\Models\Model;

class User extends \LdapRecord\Models\ActiveDirectory\User
{

    protected $connection = 'bbwstudents';

    /**
     * The object classes of the LDAP model.
     *
     * @var array
     */
    public static $objectClasses = [];
}
