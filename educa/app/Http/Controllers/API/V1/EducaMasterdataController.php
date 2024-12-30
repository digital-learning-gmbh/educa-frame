<?php


namespace App\Http\Controllers\API\V1;


use App\CloudID;
use App\Console\Commands\CloudIDChecker;
use App\Group;
use App\GroupCluster;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\V1\Groups\GroupController;
use App\Lehrer;
use App\MailAccount;
use App\Models\SessionToken;
use App\Models\Tenant;
use App\Models\UserSectionSetting;
use App\PermissionConstants;
use App\Providers\AppServiceProvider;
use App\PushToken;
use App\Raum;
use App\RCUser;
use App\Schule;
use App\Schuler;
use App\Section;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use LdapRecord\Models\Scope;

class EducaMasterdataController extends ApiController
{

    public function getAllTenants(Request $request)
    {
        $systemInformation = config("educa");
        $tenants = Tenant::all();
        foreach ($tenants as $tenant)
        {
            $tenant->chat = str_replace("https://","",config('laravel-rocket-chat.instance'));
        }
        return $this->createJsonResponse("ok", false, 200, ["tenants" => $tenants,"systemInformation" => $systemInformation]);
    }

    public function getTenantConfig(Request $request)
    {
        $tenant = AppServiceProvider::getTenant();
        $tenant->availableLanguages = config('educa.languages');
        $systemInformation = config("educa");
        $tenant->openai_key = "beta-test";
        return $this->createJsonResponse("ok", false, 200, ["tenant" => $tenant,"systemInformation" => $systemInformation]);
    }

    /**
     * @OA\Get (
     *     tags={"v1","cloud_user"},
     *     path="/api/v1/cloud_user/all",
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
     *     @OA\Response(response="200", description="Returns all cloud users to build a cache ")
     * )
     */
    public function getAllCloudUsers(Request $request): \Illuminate\Http\Response
    {
        $user = parent::getUserForToken($request);
        if($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        if ($user->hasPermissionTo(PermissionConstants::IS_MULTI_TENANT_USER)) {
            $payload = CloudID::select("id", "name", "email", "image")->with("rcUserRelation")->get();
        } else {
            $tenant_ids = $user->tenants()->pluck("tenants.id");
            $cloudIds2 = [];
            $cloudIds1 = DB::table("tenant_cloudid")->whereIn("tenant_id", $tenant_ids)->pluck("cloudid");
            foreach (CloudID::all() as $cloudId)
            {
                if($cloudId->hasPermissionTo(PermissionConstants::IS_MULTI_TENANT_USER))
                {
                    $cloudIds2[] = $cloudId->id;
                }
            }
            $payload = CloudID::whereIn("cloud_i_d_s.id", $cloudIds1)->orWhereIn("cloud_i_d_s.id",$cloudIds2)->select("id", "name", "email", "image")->with("rcUserRelation")->get();
        }
        foreach ($payload as $cloudId) {
            if($cloudId)
                $cloudId->makeHidden(["created_at","updated_at"]);
            if($cloudId->rcUserRelation)
                $cloudId->rcUserRelation->makeHidden(["created_at","updated_at","cloudid"]);
            $cloudId->rcUser = $cloudId->rcUserRelation;
            unset($cloudId->rcUserRelation);
        }

        return $this->createJsonResponse("ok", false, 200, $payload);
    }

    public function getCloudUsers(Request $request)
    {
        $user = parent::getUserForToken($request);
        if($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        if($request->input("users") == null || count($request->input("users")) <= 0)
        {
            return $this->createJsonResponse("ok", false, 200, ["cloudUsers" => []]);
        }

        if ($user->hasPermissionTo(PermissionConstants::IS_MULTI_TENANT_USER)) {
            $payload = CloudID::select("id", "name", "email", "image")->whereIn("id",$request->input("users"))->with("rcUserRelation")->limit(20)->get();
        } else {
            $tenant_ids = $user->tenants()->pluck("tenants.id");
            $cloudIds2 = [];
            $cloudIds1 = DB::table("tenant_cloudid")->whereIn("tenant_id", $tenant_ids)->pluck("cloudid");
            foreach (CloudID::whereIn("id",$request->input("users"))->limit(20)->get() as $cloudId)
            {
                if($cloudId->hasPermissionTo(PermissionConstants::IS_MULTI_TENANT_USER))
                {
                    $cloudIds2[] = $cloudId->id;
                }
            }
            $payload = CloudID::whereIn("cloud_i_d_s.id", $cloudIds1)->orWhereIn("cloud_i_d_s.id",$cloudIds2)->select("id", "name", "email", "image")->with("rcUserRelation")->get();
        }
        foreach ($payload as $cloudId) {
            if($cloudId)
                $cloudId->makeHidden(["created_at","updated_at"]);
            if($cloudId->rcUserRelation)
                $cloudId->rcUserRelation->makeHidden(["created_at","updated_at","cloudid"]);
            $cloudId->rcUser = $cloudId->rcUserRelation;
            unset($cloudId->rcUserRelation);
        }

        return $this->createJsonResponse("ok", false, 200, ["cloudUsers" => $payload]);

    }

    public function searchCloudUsers(Request $request)
    {
        $user = parent::getUserForToken($request);
        if($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        if($request->input("q") == null || strlen($request->input("q")) <= 2)
        {
            return $this->createJsonResponse("ok", false, 200, ["cloudUser" => []]);
        }

        if ($user->hasPermissionTo(PermissionConstants::IS_MULTI_TENANT_USER)) {
            $payload = CloudID::select("id", "name", "email", "image")->where("name","LIKE","%".$request->input("q")."%")->with("rcUserRelation")->limit(20)->get();
        } else {
            $tenant_ids = $user->tenants()->pluck("tenants.id");
            $cloudIds2 = [];
            $cloudIds1 = DB::table("tenant_cloudid")->whereIn("tenant_id", $tenant_ids)->pluck("cloudid");
            foreach (CloudID::where("name","LIKE","%".$request->input("q")."%")->limit(20)->get() as $cloudId)
            {
                if($cloudId->hasPermissionTo(PermissionConstants::IS_MULTI_TENANT_USER))
                {
                    $cloudIds2[] = $cloudId->id;
                }
            }
            $payload = CloudID::whereIn("cloud_i_d_s.id", $cloudIds1)->orWhereIn("cloud_i_d_s.id",$cloudIds2)->select("id", "name", "email", "image")->with("rcUserRelation")->get();
        }
        foreach ($payload as $cloudId) {
            if($cloudId)
                $cloudId->makeHidden(["created_at","updated_at"]);
            if($cloudId->rcUserRelation)
                $cloudId->rcUserRelation->makeHidden(["created_at","updated_at","cloudid"]);
            $cloudId->rcUser = $cloudId->rcUserRelation;
            unset($cloudId->rcUserRelation);
        }

        return $this->createJsonResponse("ok", false, 200, ["cloudUser" => $payload]);
    }

    /**
     * @OA\Get (
     *     tags={"v1","me"},
     *     path="/api/v1/me",
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
     *     @OA\Response(response="200", description="Information about the current user according to the token including information about the avaiable apps and group memebership")
     * )
     */
    public function getCurrentUser(Request $request): \Illuminate\Http\Response
    {
        $user = parent::getUserForToken($request);
        if ($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);
        CloudIDChecker::checkForSingleId($user);
        $tenant = AppServiceProvider::getTenant();
        $groupController = new GroupController();
        $user["groups"] = $groupController->loadGroups($user);
        $user["group_cluster"] = $user->gruppenCluster()->with("groups")->get();
        $user["other_groups_collapsed"] = $user->getAppEinstellung("other_groups_collapsed", "dashboard","false") == "true";

        $user["apps"] = $user->getApps();
        if ($user->hasPermissionTo(PermissionConstants::IS_MULTI_TENANT_USER)) {
            $user["permissions_global"] = collect($user->getAllPermissions()->pluck('name'))->merge($user->getAllPermissions(\StuPla\CloudSDK\Permission\Scope::getDefaultName(), $tenant->id)->pluck('name'));
        } else {
            $user["permissions_global"] = $user->getAllPermissions(\StuPla\CloudSDK\Permission\Scope::getDefaultName(), $tenant->id)->pluck('name');
        }
        $user["teacher"] = Lehrer::find($user->getAppLogin("klassenbuch"));
        $user["student"] = Schuler::find($user->getAppLogin("student"));
        if ($user->hasAppRights("webmail")) {
            $user["webmail_url"] = MailAccount::find($user->getAppLogin("webmail"))->url;
        }

        $user["advisor_for"] = [];

        $user["counts"] = $this->getCountsForUser($user);

        return $this->createJsonResponse( "ok", false, 200, [ "user" => $user  ]);
    }

    public function getCountsForUser($cloud_user)
    {
        $groups = $cloud_user->gruppen()->pluck("id");
        $sectionIds = Section::whereIn('group_id', $groups)->pluck('id');
        $tasks = TaskController::loadTaskForType($cloud_user, $sectionIds, true, null, null,false, true);

        $countsTask = 0;
        foreach ($tasks as $task)
        {
            $task->is_submission_seen = false;
            if($task->cloud_id != $cloud_user->id)
                foreach($task->submissions as $submission)
                    if($submission->cloudid == $cloud_user->id)
                        $task->is_submission_seen = $submission->has_seen;

            if(!$task->is_submission_seen && $task->cloud_id != $cloud_user->id)
            {
                $countsTask++;
            }
        }

        $counts = [];
        $counts["tasks"] = $countsTask;

        return $counts;
    }

    public function getRooms(Request $request)
    {
        $rooms = []; //Raum::all(); // TODO

        $schools = Schule::all();
        foreach ($schools as $s) {
            array_push($rooms, ["name" => $s->name, "rooms" => $s->raume]);
        }

        return $this->createJsonResponse("ok", false, 200, ["rooms" => $rooms]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","me", "pushnotification"},
     *     path="/api/v1/me/pushToken/register",
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
     *       name="push_token",
     *       required=true,
     *       in="query",
     *       description="token of the fcm",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Add FCM Push TOken")
     * )
     */
    public function registerPushToken(Request $request)
    {
        $user = parent::getUserForToken($request);
        if ($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        if (!$request->has("push_token"))
            return $this->createJsonResponse("No push token", true, 499);

        $pushToken = PushToken::where('push_token', "=", $request->input("push_token"))->first();

        if ($pushToken == null)
            $pushToken = new PushToken;

        $pushToken->cloud_id = $user->id;
        $pushToken->push_token = $request->input("push_token");
        $pushToken->save();

        return $this->createJsonResponse("ok", false, 200, ["pushToken" => $pushToken]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","me", "pushnotification"},
     *     path="/api/v1/me/pushToken/deregister",
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
     *       name="push_token",
     *       required=true,
     *       in="query",
     *       description="token of the fcm",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Add FCM Push TOken")
     * )
     */
    public function deregisterPushToken(Request $request)
    {
        $user = parent::getUserForToken($request);
        if ($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        if (!$request->has("push_token"))
            return $this->createJsonResponse("No push token", true, 499);

        PushToken::where([
            "cloud_id" => $user->id,
            "push_token" => $request->input("push_token")
        ])->delete();

        return $this->createJsonResponse("ok", false, 200);
    }

    public function updateSessionToken(Request $request)
    {
        $user = parent::getUserForToken($request);
        if ($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $sessionToken = parent::sessionToken();
        $sessionToken->browser = $request->input("browser");
        $sessionToken->app = $request->input("app");
        $sessionToken->device = $request->input("device");
        $sessionToken->os = $request->input("os");
        $sessionToken->save();

        return $this->createJsonResponse("ok", false, 200);

    }

    public function getSessionToken(Request $request)
    {
        $user = parent::getUserForToken($request);
        if ($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);
        $sessions = SessionToken::where("cloudid","=",$user->id)->get();

        return $this->createJsonResponse("ok", false, 200,["sessions" => $sessions]);
    }

    public function closeSessionToken($session_id, Request $request)
    {
        $user = parent::getUserForToken($request);
        if ($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $targetTenant = AppServiceProvider::getTenant();
        if(!$targetTenant && !$targetTenant->isAllowed($user,PermissionConstants::SYSTEM_SETTINGS_CLOUD_USER))
            SessionToken::where("cloudid","=",$user->id)->where("id","=",$session_id)->delete();
        else
            SessionToken::where("id","=",$session_id)->delete();

        $sessions = SessionToken::where("cloudid","=",$user->id)->get();

        return $this->createJsonResponse("ok", false, 200,["sessions" => $sessions]);
    }

    public function getGroupSettings(Request $request){

        $cloudUser = parent::getUserForToken($request);
        if ($cloudUser == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

            $sql = Group::from('groups')->select('groups.*');
            $sql->where("archived", "=", false);
            $sql->join('cloudid_group',function ($join) use ($cloudUser)
                {
                    $join->on('cloudid_group.group_id', '=','groups.id');
                    $join->where('cloudid_group.cloudid','=', $cloudUser->id);
                });
            $groups = $sql->get();
            $arr = [];

            $usersSectionSettings = UserSectionSetting::where('cloud_id','=', $cloudUser->id)->get();

            foreach ($groups as $group)
            {
                $obj = ["id" => $group->id, "name"=> $group->name, "sections" => []];

                foreach ($group->sections as $section)
                {
                    $record = $usersSectionSettings->filter( function($ele) use($section) { return $ele->section_id == $section->id; })->first();
                    $notificationDisabled = $record? !!$record->notificationDisabled : false;
                    if($section->isAllowed($cloudUser, "section.view"))
                        $obj["sections"][] = ["id" => $section->id, "name"=> $section->name, "notificationDisabled" => $notificationDisabled];
                }

                $arr[] = $obj;
            }

            return $this->createJsonResponse("ok", false, 200,["groups" => $arr]);

    }

    public function updateSectionGroupSetting(Request $request){

        $cloudUser = parent::getUserForToken($request);
        if ($cloudUser == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $usersSectionSettings = UserSectionSetting::where('cloud_id','=', $cloudUser->id)->get();

        $groupId = $request->input("groupId");
        $sectionId = $request->input("sectionId");
        $flag = $request->input("flag");

        $sql = Group::from('groups')->where("groups.id","=",$groupId)->select('groups.*');
        $sql->where("archived", "=", false);
        $sql->join('cloudid_group',function ($join) use ($cloudUser)
            {
                $join->on('cloudid_group.group_id', '=','groups.id');
                $join->where('cloudid_group.cloudid','=', $cloudUser->id);
            });

        $group = $sql->first();
        if (!$group)
            return parent::createJsonResponse("no permission for this group.", true, 400);

        $changedGroup = ["id" => $group->id, "name"=> $group->name, "sections" => []];

        foreach($group->sections as $section)
        {
            $record = $usersSectionSettings->filter( function($ele) use($section) { return $ele->section_id == $section->id; })->first();
            $notificationDisabled = $record? !!$record->notificationDisabled : false;

            $setting = null;
            if($sectionId == null || $section->id == $sectionId)
            {
                if( !$section->isAllowed($cloudUser, "section.view"))
                    return parent::createJsonResponse("no permission for this section.", true, 400);
                $setting = UserSectionSetting::where(["cloud_id" => $cloudUser->id, "section_id" => $section->id])->first();
                if(!$setting)
                {
                    $setting = new UserSectionSetting();
                    $setting->cloud_id = $cloudUser->id;
                    $setting->section_id = $section->id;
                }

               $notificationDisabled = !!$flag;
               $setting->notificationDisabled = !!$flag;
               $setting->save();
            }
          $changedGroup["sections"][] = ["id" => $section->id, "name"=> $section->name, "notificationDisabled" => $notificationDisabled];

        }

        return parent::createJsonResponse("ok", false, 200, ["group" => $changedGroup]);

    }

    public function updateGroupClusterSettings(Request $request){

        $cloudUser = parent::getUserForToken($request);
        if ($cloudUser == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $collapsed = false;
        // "weitere gruppen" flag is saved in settings
        if($request->input("groupClusterId") == "other_groups")
        {
            $cloudUser->setAppEinstellung("other_groups_collapsed", "dashboard",!!$request->input("flag")?"true" : "false");
            $collapsed = $cloudUser->getAppEinstellung("other_groups_collapsed", "dashboard","false") == "true";
        }
        else{
            $groupCluster = GroupCluster::where('cloudid','=',$cloudUser->id)->where("id","=", $request->input("groupClusterId"))->first();
            if(!$groupCluster)
                return parent::createJsonResponse("cluster not found.", true, 400);

            $groupCluster->collapsed = !!$request->input("flag");
            $groupCluster->save();
            $collapsed = $groupCluster->collapsed;
        }

        $clusters = $cloudUser->gruppenCluster()->with("groups")->get();

        return parent::createJsonResponse("ok", false, 200, ["groupClusters" => $clusters, "collapsed" => $collapsed]);
    }

    public function flipGroupClusterFavorite(Request $request){

        $cloudUser = parent::getUserForToken($request);
        if ($cloudUser == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $favoritesGroupCluster = GroupCluster::where('cloudid','=',$cloudUser->id)->where("name","=","Favoriten")->first();

        if(!$favoritesGroupCluster)
            return parent::createJsonResponse("favorites cluster not found.", true, 400);

        $group = $cloudUser->groups()->where(["group_id" => $request->input("groupId")])->first();

        if(!$group)
            return parent::createJsonResponse("no permission for this group.", true, 400);

        if($favoritesGroupCluster->groups()->where(["group_id" => $request->input("groupId")])->exists())
            $favoritesGroupCluster->groups()->detach(["group_id" => $request->input("groupId")]);
        else
            $favoritesGroupCluster->groups()->attach(["group_id" => $request->input("groupId")]);

        $clusters = $cloudUser->gruppenCluster()->with("groups")->get();

        return parent::createJsonResponse("ok", false, 200, ["groupClusters" => $clusters]);

    }
}
