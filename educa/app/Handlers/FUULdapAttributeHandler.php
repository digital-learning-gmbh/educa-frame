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

class FUULdapAttributeHandler extends LdapAttributeHandler
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
        $eloquentUser->loginServer = env('ADLDAP_CONTROLLERS','ldaps.fuu.de');
        $eloquentUser->loginType = "ldap";
        $eloquentUser->save();

        $rechte = false;
        $schulen = [];
        foreach ($ldapUser->getGroups() as $group) {
            $groupname = $group->mail[0];
            if(trim($groupname) == "stupla.verwaltung.pflege.hd@fuu.de")
            {
                $rechte = true;
                $schulen = array_merge($schulen,[2]);
            }
            if(trim($groupname) == "verteiler.ap.da@fuu.de")
            {
                $rechte = true;
                $schulen = array_merge($schulen,[1]);
            }
            if(trim($groupname) == "stupla.verwaltung.hpc@fuu.de")
            {
                $rechte = true;
                $schulen = array_merge($schulen,[3,8,39,40,41]);
            }

            if(trim($groupname) == "stupla.verwaltung.lzr@fuu.de")
            {
                $rechte = true;
                $schulen = array_merge($schulen,[3,42,43]);
            }

            if(trim($groupname) == "stupla.verwaltung.fsjuhei@fuu.de")
            {
                $rechte = true;
                $schulen = array_merge($schulen,[26]);
            }

            if(trim($groupname) == "stupla.verwaltung.physio.da@fuu.de")
            {
                $rechte = true;
                $schulen = array_merge($schulen,[5]);
            }

            if(trim($groupname) == "stupla.verwaltung.ergo.da@fuu.de")
            {
                $rechte = true;
                $schulen = array_merge($schulen,[7]);
            }

            if(trim($groupname) == "stupla.verwaltung.pflege.da@fuu.de")
            {
                $rechte = true;
                $schulen = array_merge($schulen,[1]);
            }
        }
        if($rechte)
        {
          parent::createVerwaltungsNutzer($ldapUser, $eloquentUser, $schulen);
        }

        CloudIDChecker::checkForSingleId($eloquentUser);
    }
}
