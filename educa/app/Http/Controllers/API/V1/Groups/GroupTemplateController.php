<?php

namespace App\Http\Controllers\API\V1\Groups;

use App\Console\Commands\GroupTemplates\BBWTemplate;
use App\Group;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\V1\Groups\GroupTemplates\GroupDatabaseTemplate;
use App\Models\GroupTemplate;
use App\Providers\AppServiceProvider;
use Illuminate\Http\Request;

class GroupTemplateController extends ApiController
{
    public function list(Request $request)
    {
        $tenant = AppServiceProvider::getTenant();
        $group_templates = GroupTemplate::where("tenant_id","=",$tenant->id)->get();
        return parent::createJsonResponse("group template list",false, 200, ["group_templates" => $group_templates]);
    }

    public function createFromTemplate($template_id, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        $group_template = GroupTemplate::where("id","=",$template_id)->first();
        $group = GroupTemplateProcessor::processTemplate(new GroupDatabaseTemplate($group_template),$request->input("name"), $request->input("color"));
        $group->save();

        $group->addMember($cloud_user->id);
        $cloud_user->assignRole($group->getRoleForName("Besitzer"));


        $group->load('sections');
        $group->append('permissions');
        $group->load('externalIntegrations');
        foreach ($group->sections as $section)
        {
            $section->load('sectionGroupApps');
            $section->append('permissions');
        }

        return parent::createJsonResponse("group created",false, 200, ["group" => $group]);
    }

    public function deleteTemplate($template_id, Request $request)
    {
        $group_template = GroupTemplate::where("id","=",$template_id)->get();
        $group_template->delete();

        return $this->list($request);
    }

    public function convertGroupToTemplate(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        $tenant = AppServiceProvider::getTenant();
        $group = Group::find($request->input("group_id"));

        $groupTemplate = new GroupTemplate();
        $groupTemplate->tenant_id = $tenant->id;
        $groupTemplate->name = $request->input("name");
        if($groupTemplate->name == null)
        {
            $groupTemplate->name = $group->name. " Template";
        }

        //todo
        $groupTemplate->color = $group->color;

        $roles = [];
        foreach ($group->roles as $role)
        {
            $singleRole = [];
            foreach ($role->permissions as $permission)
            {
                $singleRole[] = $permission->name;
            }

            $roles[$role->name] = $singleRole;
        }
        $groupTemplate->roles = json_encode($roles);

        $topics = [];
        foreach ($group->sections as $section)
        {
            $topic = [];
            $topic["Apps"] = [];
            $topic["Permissions"] = [];

            foreach ($section->sectionGroupApps as $app)
            {
                $topic["Apps"][] = $app->groupApp->type;
            }

            foreach ($group->roles as $role)
            {
                $singleRole = [];
                $subRole = \StuPla\CloudSDK\Permission\Models\Role::where(['guard_name' => 'cloud', 'name' => $role->id, 'scope_name' => 'section', 'scope_id' => $section->id])->first();
                foreach ($subRole->permissions as $permission)
                {
                    $singleRole[] = $permission->name;
                }
                $topic["Permissions"][$role->name] = $singleRole;
            }

            $topics[$section->name] = $topic;
        }
        $groupTemplate->topics = json_encode($topics);

        $groupTemplate->save();

        $group_templates = GroupTemplate::where("tenant_id","=",$tenant->id)->get();
        return parent::createJsonResponse("group created",false, 200, ["groupTemplate" => $groupTemplate, "group_templates" => $group_templates]);
    }
}
