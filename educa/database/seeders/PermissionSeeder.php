<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use StuPla\CloudSDK\Permission\Scope;
use App\PermissionConstants;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createPermission();
        // Global Roles
        \StuPla\CloudSDK\Permission\Models\Role::create(['guard_name' => 'cloud', 'name' => 'Super-Administrator']);
        self::superAdminNutzerPermission();
        self::teilnehmerNutzerPermission();
        self::adminNutzerPermission();
        self::groupDefaultPermission();
    }

    public static function createRolesForTenantCompany($tenant_id)
    {
        \StuPla\CloudSDK\Permission\Models\Role::create(['guard_name' => 'cloud', 'name' => 'Tenant-Administrator','scope_id'=>$tenant_id]);
        \StuPla\CloudSDK\Permission\Models\Role::create(['guard_name' => 'cloud', 'name' => 'Mitarbeiter','scope_id'=> $tenant_id]);
        \StuPla\CloudSDK\Permission\Models\Role::create(['guard_name' => 'cloud', 'name' => 'Teilnehmer','scope_id'=> $tenant_id]);
    }

    public static function createRolesForTenantSchool($tenant_id)
    {
        \StuPla\CloudSDK\Permission\Models\Role::create(['guard_name' => 'cloud', 'name' => 'Tenant-Administrator','scope_id'=> $tenant_id]);
        \StuPla\CloudSDK\Permission\Models\Role::create(['guard_name' => 'cloud', 'name' => 'Mitarbeiter','scope_id'=> $tenant_id]);
        \StuPla\CloudSDK\Permission\Models\Role::create(['guard_name' => 'cloud', 'name' => 'Dozent','scope_id'=> $tenant_id]);
        \StuPla\CloudSDK\Permission\Models\Role::create(['guard_name' => 'cloud', 'name' => 'Teilnehmer','scope_id'=> $tenant_id]);
        \StuPla\CloudSDK\Permission\Models\Role::create(['guard_name' => 'cloud', 'name' => 'Bot','scope_id'=> $tenant_id]);
    }

    private function createPermission()
    {
        // Verwaltung
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::ADMINISTRATION_DOZENT_STUPLA_SCHOOL, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::ADMINISTRATION_STUPLA_EDIT_AFTER_TIME, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::ADMINISTRATION_TEACHING_PLAN_EDIT, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::ADMINISTRATION_CONTACTS_EDIT, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::ADMINISTRATION_CONTACTS_ACCESS_EDIT, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::ADMINISTRATION_CONTACTS_PRACTICAL_CAPACITY_EDIT, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::ADMINISTRATION_CONTACTS_RELATION_EDIT, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::ADMINISTRATION_EMPLOYEES_ALL_EDIT, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_LOGIN, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_LOGIN_APP, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_FEED_SHOW_STATISTICS, 'guard_name' => 'cloud']);

        // educa
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_CLASSBOOK_OPEN, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_HOME_OPEN, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SOCIAL_OPEN, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SOCIAL_GROUP_CREATE, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_TASK_OPEN, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_TASK_CREATE, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_EDU_OPEN, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_DEVICES_OPEN, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_DEVICES_MANAGE, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_CALENDAR_OPEN, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_CALENDAR_CREATE, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_CALENDAR_CAN_DISCARD_INVITES, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_CALENDAR_VIEW_OUTLOOK, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_CALENDAR_EDIT_ALL, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_MESSAGES_OPEN, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_MESSAGES_CREATE, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_MESSAGES_CHAT_CREATE, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_EDU_SHARE_FILES, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SOCIAL_BLOCK, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SOCIAL_REPORT, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_EXPLORER_OPEN, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_CHAT_REPORT, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_CHAT_BLOCK, 'guard_name' => 'cloud']);

        //Wiki
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_WIKI_OPEN, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_WIKI_EDIT, 'guard_name' => 'cloud']);

        // Documents
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_DOCUMENTS_OPEN, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_DOCUMENTS_EDIT, 'guard_name' => 'cloud']);

        // Contacts
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_CONTACTS_OPEN, 'guard_name' => 'cloud']);

        //LearnContent
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_LEARN_CONTENT_CREATE, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_LEARN_CONTENT_EDIT_ALL, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_LEARN_CONTENT_CATEGORY_CREATE, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_LEARN_CONTENT_CATEGORY_EDIT, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_LEARN_CONTENT_COMMENT, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_LEARN_CONTENT_LIKE, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_LEARN_CONTENT_BOOKMARK, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_LEARN_CONTENT_DEVELOPER, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_LEARN_CONTENT_XAPI_DEVELOPER, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_LEARN_CONTENT_SELECT_FROM_LIBRARY, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_LEARN_CONTENT_TAGS_CREATE, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_LEARN_CONTENT_CROSS_LINK, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_LEARN_CONTENT_PERMISSIONS, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_LEARN_CONTENT_COMPETENCES, 'guard_name' => 'cloud']);

        // Gruppen-Rechte
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_GROUP_EDIT, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_GROUP]); // change image, name and color
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_GROUP_MEMBER_EDIT, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_GROUP]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_GROUP_ROLE_EDIT, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_GROUP]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_GROUP_ARCHIVE, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_GROUP]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_GROUP_DELETE, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_GROUP]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_GROUP_SECTION_CREATE, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_GROUP]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_GROUP_MEETING_CREATE, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_GROUP]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_GROUP_MEMBER_INVITE, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_GROUP]);

        // Section Rechte
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SECTION_VIEW, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SECTION_EDIT, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SECTION_ANNOUNCEMENT_OPEN, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SECTION_ANNOUNCEMENT_CREATE, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SECTION_ANNOUNCEMENT_LIKE, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SECTION_ANNOUNCEMENT_COMMENT, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SECTION_TASK_CREATE, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SECTION_TASK_OPEN, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SECTION_TASK_RECEIVE, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION ]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SECTION_CALENDAR_OPEN, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SECTION_CALENDAR_CREATE, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SECTION_INTERACTIVE_COURSE_OPEN, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SECTION_INTERACTIVE_COURSE_CREATE, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SECTION_INTERACTIVE_COURSE_ANALYTICS, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SECTION_FILES_OPEN, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION]); // download and open app
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SECTION_FILES_EDIT, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION]); // delete rename, etc.
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SECTION_FILES_UPLOAD, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION]); // upload, but can not delete
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_SECTION_ACCESSCODE_VIEW, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION]); // upload, but can not delete
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_MESSAGES_OPEN, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION ]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_MESSAGES_CREATE, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION ]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_WIKI_OPEN, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION ]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_WIKI_EDIT, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION ]);

        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_MEETING_VIEW, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION ]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_MEETING_EDIT, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION ]);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_MEETING_MODERATOR, 'guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION ]);

        // Systemsteuerung
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::SYSTEM_SETTINGS_CLOUD_OPEN, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::SYSTEM_SETTINGS_CLOUD_STATS, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::SYSTEM_SETTINGS_CLOUD_RIGHTS, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::SYSTEM_SETTINGS_CLOUD_USER, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::SYSTEM_SETTINGS_CLOUD_CLOUD, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::SYSTEM_SETTINGS_CLOUD_TENANTS, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::SYSTEM_SETTINGS_CLOUD_GROUPS, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::SYSTEM_SETTINGS_CLOUD_ANALYTICS, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::SYSTEM_SETTINGS_MAINTENANCE, 'guard_name' => 'cloud']);

        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::IS_MULTI_TENANT_USER, 'guard_name' => 'cloud']);

        // Analytics öffnen
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::MISC_ANALYTICS_OPEN, 'guard_name' => 'cloud']);

        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::MISC_SETTINGS_USERNAME_EDIT, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::MISC_SETTINGS_IMAGE_EDIT, 'guard_name' => 'cloud']);


        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_STORE_COIN_SHOW, 'guard_name' => 'cloud']);
        \StuPla\CloudSDK\Permission\Models\Permission::create(['name' => PermissionConstants::EDUCA_STORE_COIN_COLLECT, 'guard_name' => 'cloud']);
    }

    private function botNutzerPermission()
    {
        $rolen = \StuPla\CloudSDK\Permission\Models\Role::where('name', '=','Bot')->get();
        foreach ($rolen as $role) {
            $permission = \StuPla\CloudSDK\Permission\Models\Permission::where('guard_name', '=', 'cloud')->where('scope_name','=', Scope::getDefaultName())->get();
            foreach ($permission as $p) {
                $role->givePermissionTo($p);
            }
        }
    }

    private function superAdminNutzerPermission()
    {
        $rolen = \StuPla\CloudSDK\Permission\Models\Role::where('name', '=','Super-Administrator')->get();
        foreach ($rolen as $role) {
            $permission = \StuPla\CloudSDK\Permission\Models\Permission::where('guard_name', '=', 'cloud')->where('scope_name','=', Scope::getDefaultName())->get();
            foreach ($permission as $p) {
                if(PermissionConstants::EDUCA_LEARN_CONTENT_XAPI_DEVELOPER == $p->name)
                    continue;
                $role->givePermissionTo($p);
            }
        }
    }

    private function adminNutzerPermission()
    {
        $rolen = \StuPla\CloudSDK\Permission\Models\Role::where('name', '=','Tenant-Administrator')->get();
        foreach ($rolen as $role) {
            $permission = \StuPla\CloudSDK\Permission\Models\Permission::where('guard_name', '=', 'cloud')->where('scope_name','=', Scope::getDefaultName())->get();
            foreach ($permission as $p) {
                if(PermissionConstants::EDUCA_LEARN_CONTENT_XAPI_DEVELOPER == $p->name)
                    continue;
                if(PermissionConstants::IS_MULTI_TENANT_USER == $p->name)
                    continue;
                $role->givePermissionTo($p);
            }
        }
    }

    private function verwaltungsNutzerPermission()
    {
        $rolen = \StuPla\CloudSDK\Permission\Models\Role::where('name', '=','Verwaltung')->get();
        foreach ($rolen as $role)
        {
            $role->givePermissionTo(PermissionConstants::ADMINISTRATION_STUPLA_EDIT_AFTER_TIME);
            $role->givePermissionTo(PermissionConstants::ADMINISTRATION_TEACHING_PLAN_EDIT);
            $role->givePermissionTo(PermissionConstants::ADMINISTRATION_CONTACTS_EDIT);
            $role->givePermissionTo(PermissionConstants::ADMINISTRATION_CONTACTS_ACCESS_EDIT);
            $role->givePermissionTo(PermissionConstants::ADMINISTRATION_CONTACTS_PRACTICAL_CAPACITY_EDIT);
            $role->givePermissionTo(PermissionConstants::EDUCA_DEVICES_MANAGE);
        }

    }

    private function dozentenPermission()
    {
        $rolen = \StuPla\CloudSDK\Permission\Models\Role::where('name', '=','Dozent')->get();
        foreach ($rolen as $role)
        {
            $role->givePermissionTo(PermissionConstants::ADMINISTRATION_DOZENT_STUPLA_SCHOOL);
        }
    }

    private function unternehmenPermission()
    {

    }

    private function teilnehmerNutzerPermission()
    {
        $rolen = \StuPla\CloudSDK\Permission\Models\Role::where('name', '=','Teilnehmer')->get();
        foreach ($rolen as $role)
        {
            $role->givePermissionTo(PermissionConstants::EDUCA_HOME_OPEN);
            $role->givePermissionTo(PermissionConstants::EDUCA_EXPLORER_OPEN);
            $role->givePermissionTo(PermissionConstants::EDUCA_SOCIAL_OPEN);
            $role->givePermissionTo(PermissionConstants::EDUCA_TASK_OPEN);
            $role->givePermissionTo(PermissionConstants::EDUCA_EDU_OPEN);
            $role->givePermissionTo(PermissionConstants::EDUCA_CALENDAR_OPEN);
            $role->givePermissionTo(PermissionConstants::EDUCA_MESSAGES_CHAT_CREATE);
            $role->givePermissionTo(PermissionConstants::EDUCA_MESSAGES_CREATE);
            $role->givePermissionTo(PermissionConstants::EDUCA_CALENDAR_VIEW_OUTLOOK);
            $role->givePermissionTo(PermissionConstants::EDUCA_LOGIN);
            $role->givePermissionTo(PermissionConstants::EDUCA_LOGIN_APP);
            $role->givePermissionTo('settings.image.edit');
        }

    }

    public static function groupDefaultPermission()
    {
        // Default Group Role
        $adminRole = \StuPla\CloudSDK\Permission\Models\Role::create(['guard_name' => 'cloud', 'name' => 'Besitzer','scope_name' => PermissionConstants::SCOPE_GROUP, 'scope_id' => 'template']);
        $memberRole = \StuPla\CloudSDK\Permission\Models\Role::create(['guard_name' => 'cloud', 'name' => 'Mitglied', 'scope_name' => PermissionConstants::SCOPE_GROUP,'scope_id' => 'template']);

        $permission = \StuPla\CloudSDK\Permission\Models\Permission::where('guard_name', '=', 'cloud')->where('scope_name','=', PermissionConstants::SCOPE_GROUP)->get();
        foreach ($permission as $p) {
            $adminRole->givePermissionTo($p);
        }

        $permission = \StuPla\CloudSDK\Permission\Models\Permission::where('guard_name', '=', 'cloud')->where('scope_name','=', PermissionConstants::SCOPE_SECTION)->get();
        foreach ($permission as $p) {
            $adminRole->givePermissionTo($p);
            if(str_contains($p->name,"open") || str_contains($p->name, "view"))
            {
                $memberRole->givePermissionTo($p);
            }
        }


    }
}
