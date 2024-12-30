<?php

namespace App\Http\Controllers\API\V1\Groups;

use App\Group;
use App\Http\Controllers\API\ApiController;
use App\Models\ExternalIntegration;
use Illuminate\Http\Request;

class ExternalIntegrationController extends ApiController
{
    public function getGroupIntegration($groupId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        if($cloud_user == null)
        {
            return parent::createJsonResponse("This token is not valid.", true, 400);
        }

        if(! $this->isCloudUserInGroup($cloud_user, $groupId))
            return $this->createJsonResponse("No Permission", true, 400);

        $group = Group::find($groupId);
        if($group == null)
            return $this->createJsonResponse("Group not found.", true, 404);

        return $this->createJsonResponse("ok", false, 200, ["externalIntegrations" => $group->externalIntegrations ]);
    }

    public function addGroupIntegration($groupId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        if($cloud_user == null)
        {
            return parent::createJsonResponse("This token is not valid.", true, 400);
        }

        if(! $this->isCloudUserInGroup($cloud_user, $groupId))
            return $this->createJsonResponse("No Permission", true, 400);

        $group = Group::find($groupId);
        if($group == null)
            return $this->createJsonResponse("Group not found.", true, 404);

        $integrationObj = $request->input("external_integration");

        $externalIntegeration = new ExternalIntegration();
        $externalIntegeration->group_id = $groupId;
        $externalIntegeration->template_id = array_key_exists("type",$integrationObj) ? $integrationObj["template_id"] : null;
        $externalIntegeration->type = array_key_exists("type",$integrationObj) ? $integrationObj["type"] : "link";
        $externalIntegeration->icon = array_key_exists("icon",$integrationObj) ? $integrationObj["icon"] : "/images/link.png";
        $externalIntegeration->displayName = array_key_exists("displayName",$integrationObj) ? $integrationObj["displayName"] : "Neue Integration";
        $externalIntegeration->description = array_key_exists("description",$integrationObj) ? $integrationObj["description"] : "";
        $externalIntegeration->url = array_key_exists("url",$integrationObj) ? $integrationObj["url"] : "";
        $externalIntegeration->save();

        return $this->getGroupIntegration($groupId,$request);
    }

    public function deleteGroupIntegration($groupId, $external_integration_id, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        if($cloud_user == null)
        {
            return parent::createJsonResponse("This token is not valid.", true, 400);
        }

        if(! $this->isCloudUserInGroup($cloud_user, $groupId))
            return $this->createJsonResponse("No Permission", true, 400);

        $group = Group::find($groupId);
        if($group == null)
            return $this->createJsonResponse("Group not found.", true, 404);

        $externalIntegration = ExternalIntegration::find($external_integration_id);
        if($externalIntegration == null)
            return $this->createJsonResponse("Integration not found.", true, 404);

        $externalIntegration->delete();

        return $this->getGroupIntegration($groupId,$request);
    }
}
