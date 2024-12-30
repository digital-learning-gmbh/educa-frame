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

class Academy24LdapAttributeHandler extends LdapAttributeHandler
{
    /**
     * Synchronizes ldap attributes to the specified model.
     *
     * @param LdapUser $ldapUser
     * @param EloquentUser $eloquentUser
     *
     * @return void
     */
    private static $adminKurswahlen = ["benjamin.ledel@extern.fuu.de","vera.heugel@fuu.de","katrin.schober@fuu.de","julija.pranjic@stud.fuu.de","magdalene.faehndrich@fuu.de","carmen.hortig@fuu.de","rebecca.koester@fuu.de"];

    public function handle(LdapUser $ldapUser, EloquentUser $eloquentUser)
    {
        $eloquentUser->name = $ldapUser->getDisplayName();
        $eloquentUser->loginServer = env('ADLDAP_CONTROLLERS','ldaps.fuu.de');
        $eloquentUser->loginType = "ldap";
        $eloquentUser->save();

        if(in_array(strtolower($eloquentUser->email), Academy24LdapAttributeHandler::$adminKurswahlen))
        {
            parent::createVerwaltungsNutzer($ldapUser, $eloquentUser);
        }

        CloudIDChecker::checkForSingleId($eloquentUser);
    }
}
