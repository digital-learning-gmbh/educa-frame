<?php

namespace App\Http\Controllers\API\V1\Cloud;

use App\Group;
use App\Http\Controllers\API\ApiController;
use Illuminate\Http\Request;

class GroupsController extends ApiController
{
    public function getGroups(Request $request)
    {
        $groups = Group::all();
        $groups->each->append("membersCount");

        return parent::createJsonResponse("groups", false,200, ["groups" => $groups]);
    }

    public function unArchiveGroup($group_id, Request $request)
    {
        $group = Group::findOrFail($group_id);
        $group->setArchived(false);
        $group->save();

        return parent::createJsonResponse("group unarchived", false,200);
    }

    public function deleteGroup($group_id, Request $request)
    {
        $group = Group::findOrFail($group_id);
        $group->delete();

        return parent::createJsonResponse("group deleted", false,200);
    }
}
