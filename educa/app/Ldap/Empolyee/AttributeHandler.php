<?php
/**
 * Created by PhpStorm.
 * User: blede
 * Date: 07.07.2017
 * Time: 17:34
 */

namespace App\Ldap\Empolyee;

use App\Console\Commands\CloudIDChecker;
use App\Schule;
use App\CloudID as EloquentUser;
use App\Ldap\Empolyee\User as LdapUser;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttributeHandler
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

        $accounting = 'CN=SW_Educa_Admins,OU=_Groups_Software,DC=bbwsh,DC=intern';
        $member = 'CN=SW_Educa_User,OU=_Groups_Software,DC=bbwsh,DC=intern';
        if(!($ldapUser->groups()->exists(
                $accounting
            ) || $ldapUser->groups()->exists(
                $member
            ))) {
            return;
        }

        $eloquentUser->email = $ldapUser->getFirstAttribute("mail");
        $eloquentUser->name = $ldapUser->getFirstAttribute("cn");
        $eloquentUser->loginServer = config('ADLDAP_CONTROLLERS','172.16.0.1');
        $eloquentUser->loginType = "ldap";

        $rechte = false;
        foreach ($ldapUser->groups()->get() as $group) {
            $groupname = $group->getFirstAttribute("displayName");
            if(trim($groupname) == "SW_Educa_Admins")
            {
                $rechte = true;
            }
        }
        $eloquentUser->assignRole("Mitarbeiter");
        if($rechte)
        {
          $this->createVerwaltungsNutzer($ldapUser,$eloquentUser);
        }

        CloudIDChecker::checkForSingleId($eloquentUser);
    }

    protected function createVerwaltungsNutzer(LdapUser $ldapUser, EloquentUser $eloquentUser)
    {
        $eloquentUser->assignRole("Verwaltung");
        $checkUser = User::where('email', '=', $ldapUser->getEmail())->first();
        if($checkUser == null) {
            $user = new User();
            $user->firstname = $ldapUser->getFirstAttribute("givenName");
            $user->lastname = $ldapUser->getFirstAttribute("sn");
            $user->email = $ldapUser->getFirstAttribute("mail");
            $user->save();
            $schools = Schule::pluck('id')->toArray();
            $user->schulen()->sync($schools);
            $user->save();
        }

        if(\App\FavoriteFilter::where("cloudid","=",$eloquentUser->id)->where("key","=","filterExamAdministration2")->first() == null)
        {
            $fav = new \App\FavoriteFilter();
            $fav->cloudid= $eloquentUser->id;
            $fav->key = "filterExamAdministration2";
            $fav->config = '[{"id":"6398b73d-c7eb-485e-9754-ad06584d06d6","label":"BWL","mapping":{"studies":[1,14]}},{"id":"f92812b3-4591-4c49-953e-7612ed4e0927","label":"BLOCK","mapping":{"studies":[1,14],"course_types":["blocking_group"]}},{"id":"889f1795-0de4-4760-b3e7-9e40142c8a41","label":"SOZM","mapping":{"studies":[16,25,26]}},{"id":"cbc5fcc4-4804-4fc6-8fbb-c95d33cd8358","label":"PT","mapping":{"studies":[6]}},{"id":"30a0e611-4b8f-4f20-92fd-e7a4d4f2627f","label":"ET","mapping":{"studies":[15]}}]';
            $fav->save();
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
