<?php

namespace App\Http\Controllers\API\V1\Groups;

use App\Appointment;
use App\CloudID;
use App\FeedActivity;
use App\Group;
use App\GroupApp;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\V1\FeedController;
use App\Http\Controllers\Shared\RocketChatProvider;
use App\Klasse;
use App\Models\GroupCache;
use App\PermissionConstants;
use App\Providers\AppServiceProvider;
use App\Section;
use App\SectionGroupApp;
use App\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use StuPla\CloudSDK\Permission\Models\Permission;
use StuPla\CloudSDK\Permission\Models\Role;

class GroupController extends ApiController
{
    /**
     * Gibt die "Group" Objekte eines Users (cloud id) zurück
     * @param $id Gruppen ID
     * @param Request $request HTTP Request
     */
    /**
     * @OA\Get (
     *     tags={"v1","groups"},
     *     path="/api/v1/groups",
     *     description="",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Returns all groups of the current user")
     * )
     */
    public function getGroupsWithSection(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        if($cloud_user == null)
        {
            return parent::createJsonResponse("This token is not valid.", true, 400);
        }
        $grps = self::loadGroups($cloud_user);
        return parent::createJsonResponse("ok", false, 200, $grps );
    }

    public static function loadGroups($cloud_user)
    {
        $cacheEnabled = true;
        $grps = $cloud_user->gruppen_ordered(false);
        $finalGroups = [];
        foreach( $grps as $g )
        {
            if($cacheEnabled) {
            $cache = GroupCache::where("cloudid","=",$cloud_user->id)->where("groupid","=",$g->id)->first();
            if($cache != null) {
                $group = json_decode($cache->cache);
                $sections = [];
                foreach($group->sections as $section)
                {
                    if(in_array( PermissionConstants::EDUCA_SECTION_VIEW, $section->permissions))
                    {
                        $sections[] = $section;
                    }
                }
                $group->sections = $sections;
                $finalGroups[] = $group;
                continue;
            }
            }
            $g->append('permissions');
            $g->load('sections');
            $g->load('externalIntegrations');

            $identifier = $g->external_identifier;
            if($identifier && str_contains($identifier,"schoolclass")) {
                $g->schoolclass = Klasse::find(str_replace("schoolclass_", "", $identifier));
                if ($g->schoolclass != null) {
                    $g->school = $g->schoolclass->schuljahr->schule;
                    $g->schoolSettings = $g->school->getAllSettingsAttribute();
                }
            }

            foreach ($g->sections as $section)
            {
                $section->append('permissions');
                $section->load('sectionGroupApps');
            }

            GroupCache::where("cloudid","=",$cloud_user->id)->where("groupid","=",$g->id)->delete();
            $cache = new GroupCache();
            $cache->cloudid = $cloud_user->id;
            $cache->groupid = $g->id;
            $cache->cache = json_encode($g);
            $cache->save();
            $finalGroups[] = json_decode($cache->cache);
        }
        return $finalGroups;
    }

    public static function loadSections($cloud_user, $groups)
    {
        $sections = [];
        foreach($groups as $group)
        {
            foreach($group->sections as $section)
            {
                if(in_array( PermissionConstants::EDUCA_SECTION_VIEW, $section->permissions))
                {
                    $sections[] = $section;
                }
            }
        }

        return $sections;
    }

    /**
     * @OA\Get (
     *     tags={"v1","groups"},
     *     path="/api/v1/groups/{groupId}",
     *     description="",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="groupId",
     *       required=true,
     *       in="path",
     *       description="id of the group",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Returns all information of group")
     * )
     */
    public function getGroupInfo($groupId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        if(! $this->isCloudUserInGroup($cloud_user, $groupId))
            return $this->createJsonResponse("No Permission", true, 400);
        //
        $group = Group::find($groupId);
        if($group == null)
            return $this->createJsonResponse("Group not found.", true, 404);
        // Section der Gruppe laden
        $group->load('sections');
        $group->load('externalIntegrations');
        foreach ($group->sections as $section)
        {
            $section->load('sectionGroupApps');
        }
        return $this->createJsonResponse("ok", false, 200, $group );
    }

    /**
     * @OA\Post (
     *     tags={"v1","groups","group_section"},
     *     path="/api/v1/groups/{groupId}/section",
     *     description="",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="name",
     *       required=true,
     *       in="query",
     *       description="name of the section",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="groupId",
     *       required=true,
     *       in="path",
     *       description="id of the group",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Returns all information of group")
     * )
     */
    public function createSection($groupId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        if(! $this->isCloudUserInGroup($cloud_user, $groupId))
            return $this->createJsonResponse("No Permission", true, 400);

        //
        $group = Group::find($groupId);
        if($group == null)
            return $this->createJsonResponse("Group not found.", true, 404);
        if( $group->isArchived() )
            return $this->createJsonResponse("Group is archived", true, 404);

        if(!$group->isAllowed($cloud_user,PermissionConstants::EDUCA_GROUP_SECTION_CREATE))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }

        $group->addSection($request->input("name"));

        $group->append("permissions");
        $group->load('sections');
        $group->load('externalIntegrations');
        foreach ($group->sections as $section)
        {
            $section->load('sectionGroupApps');
            $section->append("permissions");
        }
        return $this->createJsonResponse("ok", false, 200, $group );

    }


    /**
     * @OA\Post (
     *     tags={"v1","groups","group_section"},
     *     path="/api/v1/groups/{groupId}/sections/{sectionId}/update",
     *     description="",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="name",
     *       required=true,
     *       in="query",
     *       description="name of the section",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="groupId",
     *       required=true,
     *       in="path",
     *       description="id of the group",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="sectionId",
     *       required=true,
     *       in="path",
     *       description="id of the section",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Updates a section's name")
     * )
     */
    public function updateSection($sectionId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }


        if(! $this->isSectionInGroupOfCloudUser($cloud_user, $sectionId) )
            return $this->createJsonResponse("No Permission", true, 400);

        $section  = Section::find($sectionId);
        if(!$section)
            return $this->createJsonResponse("Section not found", true, 400);
        if(!$section->isAllowed($cloud_user, PermissionConstants::EDUCA_SECTION_EDIT))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }
        if( $section->group()->first()->isArchived() )
            return $this->createJsonResponse("Group is archived", true, 404);

        //Change name
        if($request->input("name"))
            $section->name = $request->input("name");
        if($request->input("description"))
            $section->description = $request->input("description");
        $section->save();
        $section->append('permissions');

        GroupCache::clearGroup($section->group_id);
        return $this->createJsonResponse("ok", false, 200, ["section" => $section] );

    }

    /**
     * @OA\Post (
     *     tags={"v1","groups","group_section"},
     *     path="/api/v1/groups/{groupId}/sections/{sectionId}/remove",
     *     description="",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="groupId",
     *       required=true,
     *       in="path",
     *       description="id of the group",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="sectionId",
     *       required=true,
     *       in="path",
     *       description="id of the section",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Removes a section")
     * )
     */
    public function removeSection($sectionId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        if(! $this->isSectionInGroupOfCloudUser($cloud_user, $sectionId) )
            return $this->createJsonResponse("No Permission", true, 400);


        $section  = Section::find($sectionId);
        if(!$section)
            return $this->createJsonResponse("Section not found", true, 400);
        if(!$section->isAllowed($cloud_user, PermissionConstants::EDUCA_SECTION_EDIT))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }
        $group  = $section->group;
        if( $group->isArchived() )
            return $this->createJsonResponse("Group is archived", true, 404);


        $section->delete();

        $group->append('permissions');
        GroupCache::clearGroup($section->group_id);
        return $this->createJsonResponse("Section removed.", false, 200, ["group" => $group] );

    }

    public function reorderSections($groupId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
            return $this->createJsonResponse("This token is not valid.", true, 400);

        $group = Group::find($groupId);
        if (!$group)
            return $this->createJsonResponse("No Permission", true, 400);

        $newOrderMapping = $request->new_order_mapping;
        $sectionCount = count($group->sections);

        if(!is_array($newOrderMapping))
            return $this->createJsonResponse("order is not an array", true, 400);

        if(count($newOrderMapping) !== $sectionCount)
            return $this->createJsonResponse("wrong amount of topics.", true, 400);

        if(!array_reduce($newOrderMapping, function($a, $b) { return $a && is_int($b); }, true))
            return $this->createJsonResponse("non numeric value provided.", true, 400);

        $validSectionIds = $group->sections()->pluck("id")->toArray();

        foreach($newOrderMapping as $sectionId => $oderId)
        {
            $section = Section::find($sectionId);

            if(!$section)
                return $this->createJsonResponse("Error. Topic not found", true, 400);

            if(!in_array($section->id, $validSectionIds) )
                return $this->createJsonResponse("you cannot change a topic that does not belong to this chapter.", true, 400);

            $section->order = $oderId;
            $section->save();
        }

        $group->load("sections");
        $group->append('permissions');

        $group->load('externalIntegrations');
        foreach ($group->sections as $section)
        {
            $section->load('sectionGroupApps');
            $section->append("permissions");
        }

        GroupCache::clearGroup($section->group_id);
        return $this->createJsonResponse("ok", false, 200, ["group" => $group]);
    }

    public function getSectionInfo($sectionId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        if(! $this->isSectionInGroupOfCloudUser($cloud_user, $sectionId))
            return $this->createJsonResponse("No Permission", true, 400);

        //
        $section = Section::find($sectionId);
        if(!$section)
            return $this->createJsonResponse("Section not found.", true, 404);

        $group = $section->group()->first();
        if($group == null)
            return $this->createJsonResponse("Group not found.", true, 404);

        $section->load('sectionGroupApps');
        $section->append('permissions');
        return $this->createJsonResponse("ok", false, 200, $section );
    }

    /**
     * @OA\Get (
     *     tags={"v1","groups","group_apps"},
     *     path="/api/v1/groups/{groupId}/section/{sectionId}/apps/available",
     *     description="",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="groupId",
     *       required=true,
     *       in="path",
     *       description="id of the group",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="sectionId",
     *       required=true,
     *       in="path",
     *       description="id of the section",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Returns all apps of a section")
     * )
     */
    public function getApps($sectionId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        if(! $this->isSectionInGroupOfCloudUser($cloud_user, $sectionId))
            return $this->createJsonResponse("No Permission", true, 400);

       // $group = Group::find($groupId);
       // if($group == null)
       //     return $this->createJsonResponse("Group not found.", true, 404);

        $apps = GroupApp::all();

        return $this->createJsonResponse("ok", false, 200, $apps );
    }

    /**
     * Returns all apps that exist
     * @param $groupId
     * @param Request $request
     * @return Response
     */
    /**
     * @OA\Get (
     *     tags={"v1","groups","group_apps"},
     *     path="/api/v1/groups/apps/all",
     *     description="",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Returns all apps that exist")
     * )
     */
    public function getAllApps( Request $request)
    {
        return $this->createJsonResponse("ok", false, 200,  GroupApp::all() );
    }

    /**
     * @OA\Get (
     *     tags={"v1","groups","feed"},
     *     path="/api/v1/groups/{groupId}/feed",
     *     description="",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="groupId",
     *       required=true,
     *       in="path",
     *       description="id of the group",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Feed of the group, data object is the same as on the main feed")
     * )
     */
    public function feed($groupId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        $group = Group::find($groupId);
        if($group == null)
            return $this->createJsonResponse("Group not found", true, 404);

        if( $group->isArchived() )
            return $this->createJsonResponse("Group is archived", true, 404);

        if(! $this->isCloudUserInGroup($cloud_user, $groupId))
            return $this->createJsonResponse("No Permission", true, 400);

        $lastTimestamp = $request->input("lastTime");
        $feedActivity = FeedActivity::where(function ($query) use ($group, $lastTimestamp) {
            $query->where('belong_type', '=', 'group');
            $query->where('belong_id', $group->id);
            if($lastTimestamp != null && $lastTimestamp != "-1" && $lastTimestamp != "")
            {
                $query->where('created_at', '<', Carbon::parse($lastTimestamp));
            } else {
                $query->where('created_at', '<', Carbon::now());
            }

        })->whereIn('type', FeedController::feedTypes())->orderBy('created_at','DESC')->take(10)->get();

        $feedData = FeedController::generateCard($feedActivity,$cloud_user);

        $lastTimestamp = null;
        foreach ($feedActivity as $item)
        {
            $lastTimestamp = $item->created_at;
        }
        return parent::createJsonResponse("",false, 200, ["feedData" => $feedData, "lastTimestamp" => $lastTimestamp]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","groups"},
     *     path="/api/v1/groups/create",
     *     description="",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="name",
     *       required=true,
     *       in="query",
     *       description="name of the group",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="color",
     *       required=true,
     *       in="query",
     *       description="color of the group",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="members",
     *       required=false,
     *       in="query",
     *       description="members of the group (expects comma-separated)",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Creates a new group")
     * )
     */
    public function createGroup(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        if(!$cloud_user->hasAnyPermission(PermissionConstants::EDUCA_SOCIAL_GROUP_CREATE))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }

        $group = new Group;
        $group->name = $request->input("name", "Neue Gruppe");
        $group->color = $request->input("color", "#3490DC");
        $group->save();
        $group->createRolesTemplate();

        $tenant = AppServiceProvider::getTenant();
        if($tenant != null)
            $group->tenant_id = $tenant->id;
        // Vielleicht mal konfigurierbar machen
        $group->addSection("Allgemein");

        // Selber ist admin
        $group->addMember($cloud_user->id);
        $group->tenant_id = AppServiceProvider::getTenant()->id;
        $cloud_user->assignRole($group->getRoleForName("Besitzer"));

        // Mitglieder hinzufügen
        $member_ids = $request->input("member_ids");
        if(is_array($member_ids)) {
            foreach ($member_ids as $member) {
                if (CloudID::find($member) != null) {
                    $group->addMember($member);
                    $member->assignRole($group->getRoleForName("Mitglied"));
                }
            }
        }
        $group->save();
        $group->load('sections');
        $group->append('permissions');
        $group->load('externalIntegrations');
        foreach ($group->sections as $section)
        {
            $section->load('sectionGroupApps');
            $section->append('permissions');
        }
        return $this->createJsonResponse("ok", false, 200, ["group" => $group] );

    }

    /**
     * @OA\Post (
     *     tags={"v1","groups"},
     *     path="/api/v1/groups/{groupId}/delete",
     *     description="",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="groupId",
     *       required=true,
     *       in="path",
     *       description="id of the group",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Deletes a group")
     * )
     */
    public function deleteGroup($groupId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        if(! $this->isCloudUserInGroup($cloud_user, $groupId))
            return $this->createJsonResponse("No Permission", true, 400);

        $group = Group::find($groupId);

        if(!$group->isAllowed($cloud_user,PermissionConstants::EDUCA_GROUP_DELETE))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }

        $group->delete();

        return $this->createJsonResponse("ok", false, 200);

    }

    /**
     * @OA\Post (
     *     tags={"v1","groups"},
     *     path="/api/v1/groups/{groupId}/archive",
     *     description="",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="groupId",
     *       required=true,
     *       in="path",
     *       description="id of the group",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Archives a group")
     * )
     */
    public function archiveGroup($groupId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        $group = Group::find($groupId);
        if($group == null)
            return $this->createJsonResponse("Group not found.", true, 404);
        if( $group->isArchived() )
            return $this->createJsonResponse("Group is archived", true, 404);

        if(!$group->isAllowed($cloud_user,PermissionConstants::EDUCA_GROUP_ARCHIVE)) {
            return $this->createJsonResponse("No Permission", true, 400);
        }

        $group->setArchived(true);

        return $this->createJsonResponse("ok", false, 200);
    }

    /**
     * @OA\Get (
     *     tags={"v1","groups","feed"},
     *     path="/api/v1/groups/{groupId}/settings",
     *     description="",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="groupId",
     *       required=true,
     *       in="path",
     *       description="id of the group",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Get all infos for this group")
     * )
     */
    public function getSettings($groupId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        if(! $this->isCloudUserInGroup($cloud_user, $groupId))
            return $this->createJsonResponse("No Permission", true, 400);

        $group = Group::findOrFail($groupId);

        //members der Gruppe laden
        $group->members = $group->membersWithRoles();

        // Sections der Gruppe laden
        $group->load('sections');
        $group->append('permissions');
        $group->append('roles');
        $group->append('rolesWithPermission');
        $group->load('externalIntegrations');
        foreach ($group->sections as $section)
        {
            $section->load('sectionGroupApps');
            $section->append('permissions');
        }
        return $this->createJsonResponse("ok", false, 200, ["group" => $group]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","groups","feed"},
     *     path="/api/v1/groups/{groupId}/settings",
     *     description="",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="name",
     *       required=true,
     *       in="query",
     *       description="group name",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="color",
     *       required=true,
     *       in="query",
     *       description="group color",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="image",
     *       required=false,
     *       in="query",
     *       description="group image",
     *         @OA\Schema(
     *           type="file"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="groupId",
     *       required=true,
     *       in="path",
     *       description="id of the group",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Stores general group settings.")
     * )
     */
    public function putSettings($groupId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        $group = Group::find($groupId);
        if($group == null)
            return $this->createJsonResponse("Group not found.", true, 404);
        if( $group->isArchived() )
            return $this->createJsonResponse("Group is archived", true, 404);

        if(!$group->isAllowed($cloud_user,PermissionConstants::EDUCA_GROUP_EDIT))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }

        $newname = $request->input("name");
        if($newname) $group->setName($newname);

        $newcolor = $request->input("color");
        if($newcolor) $group->setColor($newcolor);

        $newtype = $request->input("type");
        if($newtype) $group->setType($newtype);

        $newdescription = $request->input("description");
        $group->setDescription($newdescription);

        if($request->hasfile('image'))
        {
            Storage::disk('public')->delete('/images/groups/'.$group->image.".png");

            $file = $request->file('image');
            $image = (new ImageManager);
            $image = $image->make($file->getRealPath());
            $image = $image->fit("250");

            $name = str_random(32);
            Storage::disk('public')->put('/images/groups/'.$name.".png", $image->stream('png', 90));
            $group->image = $name;
            $group->save();
        }
        GroupCache::clearGroup($group->id);
        return $this->getSettings($groupId, $request);
    }

    /**
     * @OA\Post (
     *     tags={"v1","groups","feed"},
     *     path="/api/v1/groups/{groupId}/members/add",
     *     description="",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="members",
     *       required=false,
     *       in="query",
     *       description="new members (ids, comma-separated)",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="groupId",
     *       required=true,
     *       in="path",
     *       description="id of the group",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Add members to a group")
     * )
     */
    public function addMembers($groupId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        $group = Group::find($groupId);
        if($group == null)
            return $this->createJsonResponse("Group not found.", true, 404);
        if( $group->isArchived() )
            return $this->createJsonResponse("Group is archived", true, 404);

        if($group == "closed" && !$group->isAllowed($cloud_user,PermissionConstants::EDUCA_GROUP_MEMBER_EDIT))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }


        // Mitglieder hinzufügen
        $member_ids = $request->member_ids;
        if(!is_array($member_ids))
            return $this->createJsonResponse("No members specified", true, 400);
        foreach ($member_ids as $member)
        {
            if(CloudID::find($member) != null) {
                $group->addMember($member);
                CloudID::find($member)->assignRole($group->getMemberRole());
            }
        }
        GroupCache::clearGroup($group->id);
        return $this->getSettings($groupId, $request);
    }

    /**
     * @OA\Post (
     *     tags={"v1","groups","feed"},
     *     path="/api/v1/groups/{groupId}/members/update",
     *     description="",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="member",
     *       required=false,
     *       in="query",
     *       description="member id",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="role",
     *       required=false,
     *       in="query",
     *       description="new role",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="groupId",
     *       required=true,
     *       in="path",
     *       description="id of the group",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Update a member's role")
     * )
     */
    public function updateMember($groupId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        $group = Group::find($groupId);
        if($group == null)
            return $this->createJsonResponse("Group not found.", true, 404);
        if( $group->isArchived() )
            return $this->createJsonResponse("Group is archived", true, 404);

        if(!$group->isAllowed($cloud_user,PermissionConstants::EDUCA_GROUP_MEMBER_EDIT))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }

        $member = CloudID::find($request->input("member_id"));
        if($member == null)
            return $this->createJsonResponse("Member not found.", true, 404);

        foreach ($group->roles() as $rolle)
        {
            $member->removeRole($rolle);
        }
        $roles = $request->input("role");
        foreach ($roles as $roleId) {
            $role = Role::findById($roleId, 'cloud', 'group', $group->id);
            $member->assignRole($role);
        }
        GroupCache::clearGroup($group->id);
        return $this->getSettings($groupId, $request);
    }

    /**
     * @OA\Post (
     *     tags={"v1","groups","feed"},
     *     path="/api/v1/groups/{groupId}/members/remove",
     *     description="",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="member",
     *       required=false,
     *       in="query",
     *       description="member id",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="groupId",
     *       required=true,
     *       in="path",
     *       description="id of the group",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Remove a member from the group")
     * )
     */
    public function removeMember($groupId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        $group = Group::find($groupId);
        if($group == null)
            return $this->createJsonResponse("Group not found.", true, 404);
        if( $group->isArchived() )
            return $this->createJsonResponse("Group is archived", true, 404);

        if(!$group->isAllowed($cloud_user, PermissionConstants::EDUCA_GROUP_MEMBER_EDIT))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }

        $cloudid = $request->input("member_id");
        $group->removeMember($cloudid);
        $group->append('permissions');

        GroupCache::clearGroup($group->id);
        return $this->getSettings($groupId, $request);
    }

    public function addRole($groupId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        $group = Group::find($groupId);
        if($group == null)
            return $this->createJsonResponse("Group not found.", true, 404);
        if( $group->isArchived() )
            return $this->createJsonResponse("Group is archived", true, 404);

        if(!$group->isAllowed($cloud_user, PermissionConstants::EDUCA_GROUP_EDIT))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }

        $roleName="Neue Rolle ".Carbon::now()->getTimestamp(); //= $request->input("roleName");
        $role = Role::create(['guard_name' => 'cloud', 'name' => $roleName,'scope_name' => 'group', 'scope_id' => $group->id]);

        // get roles from the section and add an empyt role
        foreach ($group->sections as $section)
        {
            $subRole = Role::create(['guard_name' => 'cloud', 'name' => $role->id, 'scope_name' => 'section', 'scope_id' => $section->id]);
            $role->assignRole($subRole);
        }


        GroupCache::clearGroup($group->id);
        return $this->getSettings($groupId, $request);
    }

    public function updateRole($groupId, $roleId, Request $request) {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        $group = Group::find($groupId);
        if($group == null)
            return $this->createJsonResponse("Group not found.", true, 404);
        if( $group->isArchived() )
            return $this->createJsonResponse("Group is archived", true, 404);

        if(!$group->isAllowed($cloud_user, PermissionConstants::EDUCA_GROUP_EDIT))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }

        $role = Role::find($roleId);
        if($roleId == null)
            return $this->createJsonResponse("Role not found.", true, 404);

        $role->name = $request->object["name"];
        $role->save();
        // hier brauchen wir eine struktur vom frontend

        //
        // group => [
        //       'test.asfsd'
        // ],
        // sections = [
        //      1 => [
        //             'appoinment.create',
        //             'xzt'
        //      ]
        // ]

        // groupen-Reechte
        // Remove all Permissions

        $role->permissions()->detach();
        foreach ($request->object["group"] as $permission) {
            $permissionDB = Permission::where('name',$permission)->where('scope_name','group')->where('guard_name','cloud')->first();
            $role->givePermissionTo($permissionDB);
        }

        foreach ($request->object["sections"] as $entry)
        {
            $section = Section::findOrFail($entry["section_id"]);
            $subRole = Role::where(['guard_name' => 'cloud', 'name' => $role->id, 'scope_name' => 'section', 'scope_id' => $section->id])->first();
            if ($subRole != null) {
                $subRole->permissions()->detach();
                foreach ( $entry["permissions"] as $permission) {
                    $subRole->givePermissionTo(Permission::where('name', $permission)->where('scope_name', 'section')->where('guard_name', 'cloud')->first());
                }
            }
        }

        GroupCache::clearGroup($group->id);
        return $this->getSettings($groupId, $request);
    }

    public function deleteRole($groupId, $roleId, Request $request) {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        $group = Group::find($groupId);
        if($group == null)
            return $this->createJsonResponse("Group not found.", true, 404);
        if( $group->isArchived() )
            return $this->createJsonResponse("Group is archived", true, 404);

        if(!$group->isAllowed($cloud_user, PermissionConstants::EDUCA_GROUP_EDIT))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }

        $role = Role::find($roleId);
        if(!$role)
            return $this->createJsonResponse("Role not found.", true, 404);
        $role->delete();

        GroupCache::clearGroup($group->id);
        return $this->getSettings($groupId,$request);
    }

    /**
     * @OA\Post (
     *     tags={"v1","groups","feed"},
     *     path="/api/v1/groups/{groupId}/section/{sectionId}/apps/add",
     *     description="",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="type",
     *       required=true,
     *       in="query",
     *       description="type of the group app ",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="groupId",
     *       required=true,
     *       in="path",
     *       description="id of the group",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="sectionId",
     *       required=true,
     *       in="path",
     *       description="id of the section",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Returns all apps of a section")
     * )
     */
    public function addApp($sectionId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        if(! $this->isSectionInGroupOfCloudUser($cloud_user, $sectionId))
            return $this->createJsonResponse("No Permission", true, 400);

        $section = Section::find($sectionId);
        if($section == null)
            return $this->createJsonResponse("Section not found.", true, 404);

        if(!$section->isAllowed($cloud_user, PermissionConstants::EDUCA_SECTION_EDIT))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }

        //
        $group =  $section->group()->first();
        if($group == null)
            return $this->createJsonResponse("Group not found.", true, 404);
        if( $group->isArchived() )
            return $this->createJsonResponse("Group is archived", true, 404);


        $type = $request->input("type");


        ///// CHAT STUFF
        if($type === "chat") {

            if( !$this->addChatSectionGroupApp($cloud_user, $group, $section) )
                return $this->createJsonResponse("Could not create App.", true, 400);
        }
        ///// END CHAT STUFF
        else
        {
           if( !$section->addSectionGroupApp($type) )
               return $this->createJsonResponse("Could not add App", true, 400);
        }
        $section = Section::where(["id" => $sectionId])->with("sectionGroupApps")->first();
        $section->append('permissions');
        GroupCache::clearGroup($group->id);
        return $this->createJsonResponse("App added.", false, 200, ["section" => $section]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","groups","feed"},
     *     path="/api/v1/groups/{groupId}/section/{sectionId}/apps/rename",
     *     description="",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="section_app_id",
     *       required=true,
     *       in="query",
     *       description="id of the group app ",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="sectionId",
     *       required=true,
     *       in="path",
     *       description="id of the section",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="section_app_name",
     *       required=true,
     *       in="path",
     *       description="new app name",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Renames a SectionGroupApp")
     * )
     */
    public function renameApp($sectionId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        if(! $this->isSectionInGroupOfCloudUser($cloud_user, $sectionId))
            return $this->createJsonResponse("No Permission", true, 400);

        $group_app_id = $request->input("section_app_id");
        if(!$group_app_id)
            return $this->createJsonResponse("No id given", true, 400);

        $section = Section::find($sectionId);
        if($section == null)
            return $this->createJsonResponse("Section not found.", true, 404);

        if(!$section->isAllowed($cloud_user, PermissionConstants::EDUCA_SECTION_EDIT))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }

        $group = $section->group()->first();
        if($group == null)
            return $this->createJsonResponse("Group not found.", true, 404);
        if( $group->isArchived() )
            return $this->createJsonResponse("Group is archived", true, 404);


        $sectionGroupApp = SectionGroupApp::find($group_app_id);
        if(! $sectionGroupApp)
            return $this->createJsonResponse("Section Group App not found", true, 404);

        $sectionGroupApp->name = $request->input("section_app_name");
        $sectionGroupApp->save();

        $section = Section::where(["id" => $sectionId])->with("sectionGroupApps")->first();
        $section->append('permissions');
        GroupCache::clearGroup($group->id);
        return $this->createJsonResponse("App renamed.", false, 200, ["section" => $section]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","groups","apps"},
     *     path="/api/v1/groups/{groupId}/section/{sectionId}/apps/remove",
     *     description="",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="groupId",
     *       required=true,
     *       in="path",
     *       description="id of the group",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="sectionId",
     *       required=true,
     *       in="path",
     *       description="id of the section",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="section_app_id",
     *       required=true,
     *       in="path",
     *       description="id of the section app",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Removes a SectionGroupApp")
     * )
     */
    public function removeApp($sectionId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        if(! $this->isSectionInGroupOfCloudUser($cloud_user, $sectionId))
            return $this->createJsonResponse("No Permission", true, 400);

        $section_app_id = $request->input("section_app_id");
        if(!$section_app_id)
            return $this->createJsonResponse("No id given", true, 400);

        $section = Section::find($sectionId);
        if($section == null)
            return $this->createJsonResponse("Section not found.", true, 404);
        if(!$section->isAllowed($cloud_user, PermissionConstants::EDUCA_SECTION_EDIT))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }

        $group = $section->group()->first();
        if($group == null)
            return $this->createJsonResponse("Group not found.", true, 404);
        if( $group->isArchived() )
            return $this->createJsonResponse("Group is archived", true, 404);


        $sectionGroupApp = SectionGroupApp::find($section_app_id);
        if(! $sectionGroupApp)
          return $this->createJsonResponse("Section Group App not found", true, 404);

        if(!$section->removeSectionGroupApp( $section_app_id))
            return $this->createJsonResponse("Could not remove section group app.", true, 404);

        $sectionGroupApp->delete();

        $section = Section::where(["id" => $sectionId])->with("sectionGroupApps")->first();
        $section->append('permissions');
        GroupCache::clearGroup($group->id);
        return $this->createJsonResponse("App removed.", false, 200, ["section" => $section]);
    }


    public function addChatSectionGroupApp($cloud_user, $group, $section)
    {
        $room = null;
        $room = RocketChatProvider::createGroup($cloud_user, $group->members(true));
        if (!$room) //if creation fails
            return false;
        if( !$section->addSectionGroupApp("chat", json_encode( [ "roomId" => $room->getId(), "educaRoomName" => $group->name] )) )
        {
            $this->removeGroupChat($cloud_user, $room->getId());
            return false;
        }
        return true;
    }

    public function membersSection($sectionId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        if(! $this->isSectionInGroupOfCloudUser($cloud_user, $sectionId))
            return $this->createJsonResponse("No Permission", true, 400);

        $section = Section::find($sectionId);
        $members = $section->members();

        return $this->createJsonResponse("Members of the section.", false, 200, ["members" => $members]);
    }

    public function getSectionEvents($sectionId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        if(! $this->isSectionInGroupOfCloudUser($cloud_user, $sectionId))
            return $this->createJsonResponse("No Permission", true, 400);

        $events = Appointment::join("appointment_section", "appointment_section.appointment_id", "appointments.id")
            ->where("appointment_section.section_id", $sectionId)
            ->where("startDate", ">=", Carbon::today()->toDateString())
            ->orderBy("startDate", "ASC")
            ->limit(5)
            ->get();

        return $this->createJsonResponse("Events of the section.", false, 200, ["events" => $events]);
    }

    public function getSectionTasks($sectionId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        if(! $this->isSectionInGroupOfCloudUser($cloud_user, $sectionId))
            return $this->createJsonResponse("No Permission", true, 400);

        $tasks = Task::join("task_section", "task_section.task_id", "tasks.id")
            ->where("task_section.section_id", $sectionId)
            ->where("start", ">=", Carbon::today()->toDateString())
            ->orderBy("start", "ASC")
            ->limit(5)
            ->get();

        return $this->createJsonResponse("Tasks for this section.", false, 200, ["tasks" => $tasks]);
    }

    public function updateSectionImage($sectionId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }


        if(! $this->isSectionInGroupOfCloudUser($cloud_user, $sectionId) )
            return $this->createJsonResponse("No Permission", true, 400);

        $section  = Section::find($sectionId);
        if(!$section)
            return $this->createJsonResponse("Section not found", true, 400);
        if(!$section->isAllowed($cloud_user, PermissionConstants::EDUCA_SECTION_EDIT))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }

        if($request->hasfile('image'))
        {
            Storage::disk('public')->delete('/images/sections/'.$section->image.".png");

            $file = $request->file('image');
            $image = (new ImageManager);
            $image = $image->make($file->getRealPath());
            $image = $image->fit("250");

            $name = str_random(32);
            Storage::disk('public')->put('/images/sections/'.$name.".png", $image->stream('png', 90));
            $section->image = $name;
            $section->save();
        }

        GroupCache::clearGroup($section->group_id);
        return $this->createJsonResponse("ok", false, 200, ["section" => $section] );

    }
}
