<?php

namespace App\Ldap\Rules;

use LdapRecord\Laravel\Auth\Rule;

class BBWRuleAD1 extends Rule
{
    /**
     * Check if the rule passes validation.
     *
     * @return bool
     */
    public function isValid()
    {
        $accounting = 'CN=SW_Educa_Admins,OU=_Groups_Software,DC=bbwsh,DC=intern';
        $member = 'CN=SW_Educa_User,OU=_Groups_Software,DC=bbwsh,DC=intern';
        return $this->user->groups()->exists(
            $accounting
        ) || $this->user->groups()->exists(
                $member
            );
    }
}
