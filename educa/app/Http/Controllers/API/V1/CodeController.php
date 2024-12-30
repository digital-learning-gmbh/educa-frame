<?php

namespace App\Http\Controllers\API\V1;

use App\AccessCode;
use App\CloudID;
use App\Console\Commands\CloudIDChecker;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Auth\ReactLoginController;
use App\Providers\AppServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use StuPla\CloudSDK\Permission\Models\Role;

class CodeController extends ApiController
{
    /**
     * @OA\Post (
     *     tags={"v1","code"},
     *     path="/api/v1/code",
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
     *       name="action",
     *       required=true,
     *       in="query",
     *       description="the action: join, leave, etc.",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="code",
     *       required=true,
     *       in="query",
     *       description="the code: six letters / digits",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function executeCode(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        $action = $request->input("action");
        $code = $request->input("code");

        if($action == "join")
        {
            // search the group
            $accessCode = AccessCode::where('code', '=', $code)->first();
            if($accessCode != null && $accessCode->group != null)
            {
                $group = $accessCode->group;
                $group->addMember($cloud_user->id);
                $cloud_user->assignRole($group->getMemberRole());
                return $this->createJsonResponse("Zur Gruppe hinzugefügt", false, 200,["group" => $group, "msg" => "Sie wurden erfolgreich zur Gruppe ".$group->name." hinzugefügt"]);
            }

        } else if($action == "task")
        {
            return $this->createJsonResponse("Nichts zu tun", false, 200,[]);
        } else if($action == "event")
        {
            return $this->createJsonResponse("Nichts zu tun", false, 200,[]);
        }

        return $this->createJsonResponse("Die Action / der Code ist nicht gültig.", true, 400);
    }


    public function checkCode(Request $request)
    {
        $code = $request->input("code");
        $accessCode = AccessCode::where('code', '=', $code)->first();
        if($accessCode != null && $accessCode->group != null)
        {
            return $this->createJsonResponse("Die Gruppe gibt es", false, 200,["group" => $accessCode->group]);
        }

        return $this->createJsonResponse("Die Action / der Code ist nicht gültig.", true, 400);
    }

    public function createAccountWithCode(Request $request)
    {
        $code = $request->input("code");
        $accessCode = AccessCode::where('code', '=', $code)->first();
        if($accessCode != null && $accessCode->group != null)
        {
            if(!$request->input("email"))
            {
                return $this->createJsonResponse("Die Action / der Code ist nicht gültig.", true, 400);
            }
            if(!$request->input("password"))
            {
                return $this->createJsonResponse("Die Action / der Code ist nicht gültig.", true, 400);
            }
            if(!$request->input("name"))
            {
                return $this->createJsonResponse("Die Action / der Code ist nicht gültig.", true, 400);
            }

            // find user
            $cloudId = CloudID::withTrashed()->where('email','=', $request->input("email"))->first();
            if($cloudId != null)
            {

                $credentials = [
                    'email' => $request->input("email"),
                    'password' => $request->input("password")
                ];

                if (Auth::guard('cloud')->attempt($credentials)) {
                    $group = $accessCode->group;
                    $group->addMember($cloudId->id);
                    $cloudId->assignRole($group->getMemberRole());
                }
                $loginController = new ReactLoginController();
                return $loginController->login($request);
            }

            // create user
            $cloudId = new CloudID();
            $cloudId->email = $request->input("email");
            $cloudId->name = $request->input("name");
            $cloudId->password = bcrypt($request->input("password"));
            $cloudId->loginServer = "local";
            $cloudId->loginType = "eloquent";
            $cloudId->save();

            $tenant = AppServiceProvider::getTenant();
            if($tenant != null && $tenant->roleRegister != null && Role::where("id","=",$tenant->roleRegister)->where("scope_id","=",$tenant->id)->first() != null)
            {
                $cloudId->roles()->sync([Role::where("id","=",$tenant->roleRegister)->where("scope_id","=",$tenant->id)->first()?->id]);
            }

            $accessCode->used++;
            $accessCode->save();

            $group = $accessCode->group;
            $group->addMember($cloudId->id);
            $cloudId->assignRole($group->getMemberRole());

            CloudIDChecker::checkForSingleId($cloudId);

            $loginController = new ReactLoginController();
            return $loginController->login($request);
        }

        return $this->createJsonResponse("Die Action / der Code ist nicht gültig.", true, 400);
    }

    public function checkCodeInApp(Request $request) {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        $code = $request->input("code");
        $accessCode = AccessCode::where('code', '=', $code)->first();
        if($accessCode != null && $accessCode->group != null)
        {
            $group = $accessCode->group;
            $group->addMember($cloud_user->id);
            $cloud_user->assignRole($group->getMemberRole());

            CloudIDChecker::checkForSingleId($cloud_user);

            $accessCode->used++;
            $accessCode->save();
            return parent::createJsonResponse("done", false, 200, ["code" => $accessCode]);
        }

        return parent::createJsonResponse("failure", false, 200, ["message" => "Wir konnten keine Aktion mit diesen Code finden."]);
    }
}
