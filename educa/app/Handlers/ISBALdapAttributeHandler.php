<?php
/**
 * Created by PhpStorm.
 * User: blede
 * Date: 07.07.2017
 * Time: 17:34
 */

namespace App\Handlers;

use App\Console\Commands\CloudIDChecker;
use App\Schule;
use App\CloudID as EloquentUser;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ISBALdapAttributeHandler extends LdapAttributeHandler
{
    /**
     * Synchronizes ldap attributes to the specified model.
     *
     * @param LdapUser $ldapUser
     * @param EloquentUser $eloquentUser
     *
     * @return void
     */

    public function handle(LdapUser $ldapUser, EloquentUser $eloquentUser)
    {
        $eloquentUser->name = $ldapUser->getDisplayName();
        $eloquentUser->loginServer = env('ADLDAP_CONTROLLERS','ldaps.isba-studium.de');
        $eloquentUser->loginType = "ldap";
        $eloquentUser->save();

        $rechte = false;
        foreach ($ldapUser->getGroups() as $group) {
            $groupname = $group->mail[0];
            if(trim($groupname) == "verteiler.verwaltung.hd@isba-studium.de")
            {
                $rechte = true;
            }
        }
        if($rechte)
        {
          parent::createVerwaltungsNutzer($ldapUser, $eloquentUser);
        }

        CloudIDChecker::checkForSingleId($eloquentUser);
    }
}
