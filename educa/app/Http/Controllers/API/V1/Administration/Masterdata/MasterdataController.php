<?php

namespace App\Http\Controllers\API\V1\Administration\Masterdata;

use App\AdditionalInfo;
use App\CloudID;
use App\Http\Controllers\API\V1\Administration\AdministationApiController;
use App\Http\Controllers\Shared\RocketChatProvider;
use App\Kontakt;
use App\Lehrer;
use App\Models\SessionToken;
use App\Schule;
use App\Schuler;
use App\Schuljahr;
use App\Studium;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class MasterdataController extends AdministationApiController
{

    /**
     * @OA\Get(
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/masterdata/schools",
     *     description="",
     *     @OA\Response(response="200", description="Array of all schools in the system with additional information (masterdata)")
     * )
     */
    public function schools(Request $request)
    {
        $cloudUser = parent::getUserForToken($request);
        if($cloudUser != null)
            $adminUser = $cloudUser->administrationUser();

        $schools = $adminUser->schulen()->with(["addinfo","schuljahre","kohorten"])->orderBy("name")->get();
        $schools->each->append('allSettings');
        return parent::createJsonResponse("schools",false, 200, ["schools" => $schools]);
    }

    /**
     * @OA\Get(
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/masterdata/schools/{school_id}/schoolyears",
     *     description="",
     *     @OA\Parameter(
     *     name="school_id",
     *     required=true,
     *     in="path",
     *     description="id of the school",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Array of all schools in the system with additional information (masterdata)")
     * )
     */
    public function schoolyears($school_id, Request $request)
    {
        $school = Schule::findOrFail($school_id);
        return parent::createJsonResponse("schoolyears",false, 200, $school->schuljahre()->orderBy("year","ASC")->get());
    }

    /**
     * @OA\Get(
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/masterdata/schools/{school_id}/schoolyears/{year_id}/courses",
     *     description="",
     *     @OA\Parameter(
     *     name="school_id",
     *     required=true,
     *     in="path",
     *     description="id of the school",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="year_id",
     *     required=true,
     *     in="path",
     *     description="id of the school year",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Array of all schools in the system with additional information (masterdata)")
     * )
     */
    public function courses($school_id, $year_id, Request $request)
    {
        $school = Schule::findOrFail($school_id);
        $year = Schuljahr::findOrFail($year_id);
        $courses = $year->klassen;
        foreach ($courses as $cours)
        {
            $cours->schuler = $cours->getSchulerAttribute();
            $cours->members_count = $cours->getAllSchulerAttribute()->count();
        }
        $courses->each->load("getLehrplan");
        $courses->each->load("getLehrplan.studiumRelation");
        return parent::createJsonResponse("courses",false, 200, ["courses" => $courses]);
    }

    /**
     * @OA\Get(
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/masterdata/schools/{school_id}/teachers",
     *     description="",
     *     @OA\Parameter(
     *     name="school_id",
     *     required=true,
     *     in="path",
     *     description="id of the school",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Array of all teachers in a school in the system with additional information (masterdata)")
     * )
     */
    public function teacher($school_id, Request $request)
    {
      //  $school = Schule::findOrFail($school_id);
        $teachers = Lehrer::with("schulen")->with("faecher:id,abk,name,color")->orderBy('lastname')->orderBy('firstname')->get()->each->append("iba_settings");

        foreach($teachers as $t)
        {
            $addinfo = $t->getAddInfo();
            $t["title"] = $addinfo->title;
        }
        $teachers->each->append('studiengang');
        return parent::createJsonResponse("teacher of the school",false, 200, ["teachers" => $teachers]);
    }

    /**
     * @OA\Get(
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/masterdata/schools/{school_id}/rooms",
     *     description="",
     *     @OA\Parameter(
     *     name="school_id",
     *     required=true,
     *     in="path",
     *     description="id of the school",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Array of all rooms of a school in the system with additional information (masterdata)")
     * )
     */
    public function rooms($school_id, Request $request)
    {
        $school = Schule::findOrFail($school_id);
        return parent::createJsonResponse("teacher of the school",false, 200, ["rooms" => $school->raume()->orderBy('name')->get()]);
    }

    public function usersJWT($id, Request $request)
    {
        $user = parent::getUserForToken($request);
        if($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        if($request->model == "student")
        {
            $user2 = Schuler::find($id);
            if($user2)
                $user2 = $user2->getCloudID();
        }
        else if($request->model == "teacher")
        {
            $user2 = Lehrer::find($id);
            if($user2)
                $user2 = $user2->getCloudID();
        }

        else if($request->model == "employee")
        {
            $user2 = User::find($id);
            if($user2)
                $user2 = $user2->getCloudID();
        }
        else if (!$request->model)
        {
            $user2 = CloudID::find($id);
        }


        if($user2 == null)
            return parent::createJsonResponse("This user is not valid.", true, 400);

        $token = str_random(128);


        $tokenString_old = trim($request->bearerToken() ? $request->bearerToken() : $request->input("token"));
        $token_old = SessionToken::where("token","LIKE",$tokenString_old)->first();

        $tokenString = trim($token);
        $sessionToken = new SessionToken();
        $sessionToken->token = $tokenString;
        $sessionToken->cloudid = $user2->id;
        $sessionToken->last_seen = Carbon::now();
        $sessionToken->browser = $request->input("browser",$token_old->browser);
        $sessionToken->app = $request->input("app",$token_old->app);
        $sessionToken->device = $request->input("device",$token_old->device);
        $sessionToken->os = $request->input("os",$token_old->os);
        $sessionToken->isAdmin = true;
        $sessionToken->save();

        $rcUser =  RocketChatProvider::login($user2);
        $response =  $this->createJsonResponse("ok", false, 200, ["jwt" => $token, "cloudId" => $user2->id] );
        if( $rcUser )
        {
            $response->withCookie(cookie( "educa_rc_token", $rcUser->getAuthToken(), 999999999999,"/",null,false,false));
            $response->withCookie(cookie( "educa_rc_uid",  $rcUser->getId(), 999999999999,"/",null,false,false));
        }
        return $response;
    }

    /**
     * @OA\Get(
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/masterdata/schools/{school_id}/studies",
     *     description="",
     *     @OA\Parameter(
     *     name="school_id",
     *     required=true,
     *     in="path",
     *     description="id of the school",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Array of all studies of a school in the system with additional information (masterdata)")
     * )
     */
    public function studium($school_id, Request $request)
    {
     ///   $school = Schule::findOrFail($school_id);
        $studium = Studium::all();
        $studium->load("lehrplan");
        foreach ($studium as $s)
        {
            $s->lehrplan->each->append("direction_of_study");
            $s->lehrplan->each->append("category_choose");
        }
        return parent::createJsonResponse("studies of the school",false, 200, ["studies" => $studium]);
    }

    public function getSupportTable(Request $request)
    {
        $type = $request->type;

        if(!$type)
            return $this->createJsonResponse( "No type given", true, 400);

        $entries = DB::table("support_table")->where(["type" => $type])->get();

        return $this->createJsonResponse( "ok", false, 200, ["entries" => $entries, "type" => $type]);
    }

    public function switchBackUser(Request $request)
    {
        $user = parent::getUserForToken($request);
        if($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $tokenString = trim($request->bearerToken() ? $request->bearerToken() : $request->input("token"));
        $token = SessionToken::where("token","LIKE",$tokenString)->first();
        if($token != null)
        {
            $token->delete();
        }

        if(Session::get('cloud_user_old') != null)
        {
            $user2 = CloudID::find(Session::get("cloud_user_old")->id);
            if($user2 == null)
                return parent::createJsonResponse("This user is not valid.", true, 400);
            $token = Auth::guard('api')->login($user2);

            $rcUser =  RocketChatProvider::login($user2);
            $response =  $this->createJsonResponse("ok", false, 200, ["jwt" => $token] );
            if( $rcUser )
            {
                $response->withCookie(cookie( "educa_rc_token", $rcUser->getAuthToken(), 999999999999,"/",null,false,false));
                $response->withCookie(cookie( "educa_rc_uid",  $rcUser->getId(), 999999999999,"/",null,false,false));
            }
            return $response;
        }
        return $this->createJsonResponse("ok", false, 200);
    }

}
