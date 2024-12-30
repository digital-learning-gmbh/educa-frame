<?php

namespace App\Http\Controllers\API\V1\Groups;

use App\CloudID;
use App\Group;
use App\Http\Controllers\API\V1\Groups\GroupTemplates\AbstractGroupTemplate;
use App\PermissionConstants;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Storage;
use StuPla\CloudSDK\Permission\Models\Permission;
use StuPla\CloudSDK\Permission\Models\Role;

class GroupTemplateProcessor
{

    public static function processTemplate(AbstractGroupTemplate $abstractGroupTemplate, $name = null, $color = null)
    {
        $group = new Group();

        // set name
        $group->name = $abstractGroupTemplate->name($name);

        // set color
        $group->color = $color != null ? $color : $abstractGroupTemplate->color();
        $group->save();

        // set image
        if($abstractGroupTemplate->startImage() != null) {
            $initPfad = "/images/group_templates/" . $abstractGroupTemplate->startImage();
            $name = str_random(32);
            Storage::disk("public")->copy($initPfad, '/images/groups/' . $name . ".png");
            $group->image = $name;
        }
        $group->tenant_id = AppServiceProvider::getTenant()->id;

        $group->save();

        // add roles
        $roles = $abstractGroupTemplate->roles();
        foreach ($roles as $name => $rights)
        {
            $instanceRole = \StuPla\CloudSDK\Permission\Models\Role::create(['guard_name' => 'cloud', 'name' => $name,'scope_name' => PermissionConstants::SCOPE_GROUP, 'scope_id' => $group->id]);
            $instanceRole->permissions()->sync( Permission::where(['guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_GROUP])->whereIn("name",$rights)->get(), false);
        }

        // add Bereiche
        $sections = $abstractGroupTemplate->topics();
        foreach ($sections as $section => $settings)
        {
            $sectionModel = $group->addSection($section,false);
            if(array_key_exists("Apps", $settings))
            {
                foreach ($settings["Apps"] as $app)
                {
                    if($app == "chat")
                    {
                        $g = new GroupController();
                        $g->addChatSectionGroupApp(CloudID::find(1), $group, $sectionModel);
                    } else
                        $sectionModel->addSectionGroupApp($app);
                }
            }
            if(array_key_exists("Permissions", $settings))
            {
                foreach ($settings["Permissions"] as $role => $permissions)
                {
                    $roleMaster = Role::findByName($role,'cloud', PermissionConstants::SCOPE_GROUP, $group->id);
                    $roleDB = Role::findByName($roleMaster->id,'cloud', PermissionConstants::SCOPE_SECTION, $sectionModel->id);
                    if($roleDB != null && count($permissions) > 0)
                    {
                        $roleDB->permissions()->sync(Permission::where(['guard_name' => 'cloud', 'scope_name' => PermissionConstants::SCOPE_SECTION])->whereIn("name",$permissions)->get(), false);
                    }
                }
            }
        }

        return $group;
    }
}
