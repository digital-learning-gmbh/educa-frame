<?php
namespace App\Http\Controllers\API\V1;

use App\BBBServer;
use App\ModelMeeting;
use App\Http\Controllers\API\ApiController;
use App\Providers\AppServiceProvider;
use BigBlueButton\BigBlueButton;
use BigBlueButton\Parameters\CreateMeetingParameters;
use BigBlueButton\Parameters\GetMeetingInfoParameters;
use BigBlueButton\Parameters\GetRecordingsParameters;
use BigBlueButton\Parameters\JoinMeetingParameters;
use BigBlueButton\Parameters\EndMeetingParameters;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MeetingController extends ApiController
{

    private $bbb_load_max = 300000000; //Maximale Sessions pro server, TODO in Config auslagern

    /**
     * @OA\Get (
     *     tags={"v1","meetings"},
     *     path="/api/v1/meeting/{modelType}/{id}/join",
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
     *       name="modelType",
     *       required=true,
     *       in="path",
     *       description="model type",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="id",
     *       required=true,
     *       in="path",
     *       description="model id",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="type",
     *       required=false,
     *       in="query",
     *       description="meeting type",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="name",
     *       required=false,
     *       in="query",
     *       description="name of the meeting",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Returns a join url for the current user to the meeting associated with supplied model")
     * )
     */
    public function join($modelType, $id, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return parent::createJsonResponse("This token is not valid.", true, 400);
        }

        $type = "bigbluebutton";
        if($request->has("type"))
        {
            $type = $request->input("type");
        }

        $meeting = ModelMeeting::where("model_type", "=", $modelType)->where("model_id", "=", $id)->where("type", "=", $type)->first();
//        if($meeting != null)
//        {
//            if(!$this->isCreated($meeting->first()))
//            {
//                $server = BBBServer::find($meeting->first()->bbb_server);
//                $server->load -= 1;
//                $server->save();
//            }
//        }
        //Meeting erstellen?
        if($meeting == null) {
            $meeting = new ModelMeeting();
            $meeting->type = $type;
            $meeting->model_type = $modelType;
            $meeting->model_id = $id;
            $meeting->name = $request->input("name", $meeting->getModel()->name());

            if ($meeting->getModel() == null || !$meeting->getModel()->checkMeetingRights($cloud_user)) {
                return parent::createJsonResponse("Keine Rechte, um ein Meeting zu starten und es ist aktuell kein Meeting aktiv.", true, 403);
            }

            $meeting = $this->createMeeting($meeting);
            if (!$meeting) {
                return parent::createJsonResponse("Technisches Problem beim Starten des Meetings (1).", true, 500);
            }
            $meeting->save();
        }
        $meeting->name = $meeting->getModel()->name();
        $meeting->save();

        if(!$this->isCreated($meeting)) {
            $meeting = $this->createAtBigBlueButton($meeting);
            if(!$meeting)
            {
                return parent::createJsonResponse("Technisches Problem beim Starten des Meetings (2).", true, 500);
            }
        }

        $server = BBBServer::find($meeting->bbb_server);
        $this->setEnvBBBServer($server);
        $bbb = new BigBlueButton();
        $passwort = $meeting->password_member;
        if($meeting->getModel()->checkMeetingRights($cloud_user))
        {
            $passwort = $meeting->password_moderator;
        }

        $name = $cloud_user->displayName;
        $joinMeetingParams = new JoinMeetingParameters($meeting->meeting_id, $name, $passwort);
        $joinMeetingParams->setAvatarURL($request->getSchemeAndHttpHost()."/api/image/cloud?cloud_id=".$cloud_user->id."&size=35&name=".$cloud_user->image);
        $joinMeetingParams->setRedirect(true);
        $url = $bbb->getJoinMeetingURL($joinMeetingParams);

        return parent::createJsonResponse("ok", false, 200, ["url" => $url]);
    }


    public function info($modelType, $id, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return parent::createJsonResponse("This token is not valid.", true, 400);
        }

        $type = "bigbluebutton";
        if($request->has("type"))
        {
            $type = $request->input("type");
        }

        $meeting = ModelMeeting::where("model_type", "=", $modelType)->where("model_id", "=", $id)->where("type", "=", $type)->first();
        if($meeting)
        {
            $server = BBBServer::find($meeting->bbb_server);
            $this->setEnvBBBServer($server);
            $bbb = new BigBlueButton();

            $recordingParams = new GetRecordingsParameters();
            $recordingParams->setMeetingId($meeting->meeting_id);
            $recordings = $bbb->getRecordings($recordingParams);
            $records = [];
            foreach ($recordings->getRecords() as $recording)
            {
                $o = [];
                $o["name"] = $recording->getName();
                $o["startTime"] = Carbon::createFromTimestamp(intval($recording->getStartTime() / 1000))->format("d.m.Y H:i");
                $o["endTime"] = Carbon::createFromTimestamp(intval($recording->getEndTime() / 1000))->format("d.m.Y H:i");
                $o["url"] = $recording->getPlaybackUrl();
                $o["url_button"] = "<a href='".$recording->getPlaybackUrl()."' class='btn btn-primary' target='_blank'><i class='fas fa-play'></i> Abspielen</a>";
                $records[] = $o;
            }
            return parent::createJsonResponse("ok", false, 200, ["meeting" => $meeting , "recordings" => $records ]);
        }

        return parent::createJsonResponse("ok", false, 200, ["meeting" => null , "recordings" => []]);
    }

    public function live($modelType, $id, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return parent::createJsonResponse("This token is not valid.", true, 400);
        }

        $type = "bigbluebutton";
        if($request->has("type"))
        {
            $type = $request->input("type");
        }

        $meeting = ModelMeeting::where("model_type", "=", $modelType)->where("model_id", "=", $id)->where("type", "=", $type)->first();
        $personCount = $this->personCount($meeting);
        return parent::createJsonResponse("ok", false, 200, ["personCount" => $personCount]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","meetings"},
     *     path="/api/v1/meeting/{modelType}/{id}/delete",
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
     *       name="modelType",
     *       required=true,
     *       in="path",
     *       description="model type",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="id",
     *       required=true,
     *       in="path",
     *       description="model id",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="type",
     *       required=false,
     *       in="query",
     *       description="meeting type",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Ends and deletes a meeting")
     * )
     */
    public function delete($modelType, $id, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return parent::createJsonResponse("This token is not valid.", true, 400);
        }
//        $type = "bigbluebutton";
//        if($request->has("type"))
//        {
//            $type = $request->input("type");
//        }
//
//        $meeting = ModelMeeting::where("model_type", "=", $modelType)->where("model_id", "=", $id)->where("type", "=", $type)->get();
//        //Meeting löschen, falls es existiert
//        if($meeting->isEmpty())
//        {
//            return parent::createJsonResponse("Meeting does not exist.", true, 400);
//        }
//
//        $meeting = $meeting->first();
//
//        $server = BBBServer::find($meeting->bbb_server);
//        $this->setEnvBBBServer($server);
//        $bbb = new BigBlueButton();
//
//        $passwort = $meeting->password_moderator;
//
//        $endMeetingParams = new EndMeetingParameters($meeting->meeting_id, $passwort);
//        $response = $bbb->endMeeting($endMeetingParams);
//        if ($response->getReturnCode() == 'FAILED') {
//            return parent::createJsonResponse("Could not end meeting.".$response->getMessage(), true, 500);
//        }
//        $server->load -= 1;
//        $server->save();
        // erstmal nicht löschbar machen
        // $meeting->delete();
        return parent::createJsonResponse("Meeting deleted.", false, 200);
    }

    public function setEnvBBBServer($server)
    {
        putenv("BBB_SECRET=".$server->secret);
        putenv("BBB_SERVER_BASE_URL=".$server->base_url);
    }

    public function createMeeting($meeting)
    {
        $server = BBBServer::where("active", 1)->orderBy("load", "ASC")->get();
        //Fallback: Wenn alle Server "überfüllt", nimm den, der am wenigsten ausgelastet ist
        if ($server->isEmpty()) {
            BBBServer::where("active", 1)->orderBy("load", "ASC")->get();
            if ($server->isEmpty()) //gibt keinen Server?
            {
                return false;
            }
        }
        $server = $server->first();
        $this->setEnvBBBServer($server);

        $uuid = uniqid();
        $teilnehmerPasswort = str_random(16);
        $moderatorPasswort = str_random(16);

        $meeting->meeting_id = $uuid;
        $meeting->password_moderator = $moderatorPasswort;
        $meeting->password_member = $teilnehmerPasswort;
        $meeting->bbb_server = $server->id;

        $meeting->save();

        $server->load += 1;
        $server->save();

        return $meeting;
    }

    public function createAtBigBlueButton($meeting)
    {
        $server = BBBServer::find($meeting->bbb_server);

        if($server == null)
            return false;

        $this->setEnvBBBServer($server);
        $uuid = $meeting->meeting_id;
        $teilnehmerPasswort = $meeting->password_member;
        $moderatorPasswort = $meeting->password_moderator;

        $bbb = new BigBlueButton();

        $createMeetingParams = new CreateMeetingParameters($uuid, $meeting->name);
        $createMeetingParams->setAttendeePassword($teilnehmerPasswort);
        $createMeetingParams->setModeratorPassword($moderatorPasswort);
        $createMeetingParams->setWelcomeMessage($meeting->getModel()->welcomeText());
        $tenant = AppServiceProvider::getTenant();
        $createMeetingParams->setLogoutUrl("https://" . $tenant->domain . "/");
        // $createMeetingParams->setEndCallbackUrl("");
        $createMeetingParams->setRecord(true);
        $createMeetingParams->setAllowStartStopRecording(true);
        $createMeetingParams->setAutoStartRecording(false);
        $createMeetingParams->addPresentation("https://".$tenant->domain."/default.pdf");

        $response = $bbb->createMeeting($createMeetingParams);
        if ($response->getReturnCode() == 'FAILED') {
            return false;
        }

        return $meeting;
    }

    public function isCreated($meeting)
    {
        $server = BBBServer::find($meeting->bbb_server);
        $this->setEnvBBBServer($server);

        $bbb = new BigBlueButton();
        $getMeetingInfoParams = new GetMeetingInfoParameters($meeting->meeting_id, $meeting->password_moderator);
        $response = $bbb->getMeetingInfo($getMeetingInfoParams);
        if ($response->getReturnCode() == 'FAILED') {
            // meeting not found or already closed
            return false;
        }
        return true;
    }


    public function personCount($meeting)
    {
        $server = BBBServer::find($meeting->bbb_server);
        $this->setEnvBBBServer($server);

        $bbb = new BigBlueButton();
        $getMeetingInfoParams = new GetMeetingInfoParameters($meeting->meeting_id, $meeting->password_moderator);
        $response = $bbb->getMeetingInfo($getMeetingInfoParams);
        if ($response->getReturnCode() == 'FAILED') {
            // meeting not found or already closed
            return 0;
        }
        return $response->getMeeting()->getParticipantCount();
    }

}
