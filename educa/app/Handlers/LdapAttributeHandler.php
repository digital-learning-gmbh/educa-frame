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

class LdapAttributeHandler
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
        $eloquentUser->loginServer = config('ADLDAP_CONTROLLERS','fuu.ldaps.de');
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
          $this->createVerwaltungsNutzer($ldapUser,$eloquentUser);
        }

        CloudIDChecker::checkForSingleId($eloquentUser);
    }

    protected function createVerwaltungsNutzer(LdapUser $ldapUser, EloquentUser $eloquentUser, $schools = null)
    {
        $schools ?? Schule::pluck('id')->toArray();
        $eloquentUser->assignRole("Verwaltung");
        $checkUser = User::where('email', '=', $ldapUser->getEmail())->first();
        if($checkUser == null) {
            $user = new User();
            $user->firstname = $ldapUser->getFirstName();
            $user->lastname = $ldapUser->getLastName();
            $user->email = $ldapUser->getEmail();
            $user->save();
            $user->schulen()->sync($schools);
            $user->save();
        }
    }

    protected function startsWith($haystack, $needle)
    {
        return strpos($haystack, $needle) === 0;
    }

    protected function get_string_between($str, $from, $to)
    {

        $string = substr($str, strpos($str, $from) + strlen($from));

        if (strstr($string, $to, TRUE) != FALSE) {

            $string = strstr($string, $to, TRUE);

        }

        return $string;

    }
}
