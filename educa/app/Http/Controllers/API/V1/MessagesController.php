<?php

namespace App\Http\Controllers\API\V1;

use App\Group;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Shared\RocketChatProvider;
use App\Lehrer;
use App\Mail\ChatMail;
use App\RCUser;
use App\Section;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MessagesController extends ApiController
{

    public function getGroupsAndImIds(Request $request)
    {
        // parent::checkSecurityToken($request);

        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null) {
            return response()->json(["status" => -1, "error" => "No user has this token. Token invalid"]);
        }

        $groupsSorted = $cloud_user->gruppen()->sort(function ($a, $b) {
            $recentA = $a->getActivity()->latest()->first();
            $recentB = $b->getActivity()->latest()->first();

            if ($recentA == null && $recentB == null) {
                return 0;
            } elseif ($recentA == null) {
                return 1;
            } elseif ($recentB == null) {
                return -1;
            }

            $ad = new \DateTime($recentA->created_at);
            $bd = new \DateTime($recentB->created_at);

            if ($ad == $bd) {
                return 0;
            }

            return $ad < $bd ? 1 : -1;
        });

        $groups = [];
        foreach ($groupsSorted as $group) {
            $groupArray = [
                "id" => $group->id,
                "name" => $group->name,
                "reiter" => []
            ];

            $unreadCount = 0;
            foreach ($group->reiters(1) as $beitragReiter) {
                $unreadCount += $beitragReiter->getUnread($cloud_user->id)->count();
            }
            $groupArray["unread_count"] = $unreadCount;

            foreach ($group->reiters() as $reiter) {
                $groupArray["reiter"][] = [
                    "name" => $reiter->name,
                    "type" => $reiter->typeId,
                    "id" => $reiter->count
                ];
            }
            $groups[] = $groupArray;
        }

        try {
            //  $subscription = RocketChatProvider::getSubscriptions($cloud_user);
            $dmessages = $cloud_user->getRooms();
        } catch (\Exception $exception) {
            $error = 'Kommunikationsfehler mit dem Chat-Server.';
            return response()->json(["status" => 0, "error" => $error, "token" => RocketChatProvider::$token]);
        }
        return response()->json(["status" => 1, "groups" => $groups, "messages" => $dmessages, "token" => RocketChatProvider::$token]);
    }

    public function getImListWithCloudUsernames(Request $request)
    {
        $imList = $this->getImList($request);
        return $this->createJsonResponse("ok", false, 200, ["rooms" => $imList]);

    }

    private function getImList(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $imList = RocketChatProvider::getRooms($cloud_user);

        //find custom group name
        $groupAppParams = DB::table("section_group_apps")
            ->join('group_apps', 'section_group_apps.group_app_id', '=', 'group_apps.id')
            ->join('sections', 'section_group_apps.section_id', '=', 'sections.id')
            ->where('group_apps.type', '=', 'chat')
            ->select('parameters','section_id')
            ->get();

        $roomIdToNameMap = [];

        foreach( $groupAppParams->toArray() as $params)
        {
                $jsonParams = json_decode($params->parameters, TRUE);
                if( array_key_exists("roomId", $jsonParams) && array_key_exists("educaRoomName", $jsonParams) )
                {
                    $obj = ["roomId" => $jsonParams["roomId"], "roomName" => $jsonParams["educaRoomName"]];
                    if( $params->section_id > 0 )
                    {
                        $obj["educaGroup"] = Section::find($params->section_id)->group()->first();
                    }
                    array_push($roomIdToNameMap,$obj);
                }
        }

        foreach ($imList as $entry) {
            $entry->cloudUsers = [];

            // Direkter Chat
            if ($entry->t == "d") {
                $entry->type = "im";
                if(property_exists($entry,"u")) {
                    $entry->usernames = [];
                    $entry->uids = [];
                    $username = $entry->u->username;
                    $entry->usernames[] = $username;
                    $entry->uids[] = $entry->u->_id;

                    $rcUser = RCUser::where('username', '=', $username)->get()->first();
                    array_push($entry->cloudUsers,
                        [
                            "username" => $username,
                            "cloudName" => $rcUser != null ? $rcUser->cloudID->name : $username,
                            "cloudUserId" => $rcUser != null ? $rcUser->cloudID->id : -1,
                            "image" => $rcUser != null ? $rcUser->cloudID->image : null
                        ]);
                }


                if(property_exists($entry,"usernames")) {
                    foreach ($entry->usernames as $username) {
                        $rcUser = RCUser::where('username', '=', $username)->get()->first();
                        array_push($entry->cloudUsers,
                            [
                                "username" => $username,
                                "cloudName" => $rcUser != null ? $rcUser->cloudID?->name : $username,
                                "cloudUserId" => $rcUser != null ? $rcUser->cloudID?->id : -1,
                                "image" => $rcUser != null ? $rcUser->cloudID?->image : null
                            ]);
                    }
                }
            }

            // GruppenChat
            if($entry->t == "p")
            {
                $entry->type = "group";
                $roomName = $entry->name;
                foreach( $roomIdToNameMap as $e)
                {
                    if($e["roomId"] == $entry->_id)
                    {
                        $entry->educaGroup = $e["educaGroup"];
                        $roomName = $e["roomName"];
                    }
                }
                $entry->name = $roomName;
                $entry->usernames = [];
                $entry->uids = [];
            }

        }

        usort($imList, function ($a, $b)
        {
            $tsa = $a? $a->_updatedAt : null;
            $tsb = $b? $b->_updatedAt : null;

            if (strtotime($tsa) < strtotime($tsb))
                return 1;
            else if (strtotime($tsa) > strtotime($tsb))
                return -1;
            else
                return 0;
        });
        return $imList;
    }

    public function getGroupChatFromSection($sectionId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        if (!$this->isSectionInGroupOfCloudUser($cloud_user, $sectionId)) {
            return $this->createJsonResponse("No Permission", true, 400);
        }

        return $this->createJsonResponse("ok", false, 200);
    }

    private function prepareRoom($usernames, $room)
    {

        $cloudUserObjects = [];
        // Prepare room Object
        //
        foreach ($usernames as $username) {
            $rcUser = RCUser::where('username', '=', $username)->get()->first();
            array_push($cloudUserObjects,
                [
                    "username" => $username,
                    "cloudName" => $rcUser != null ? $rcUser->cloudID->name : $username,
                    "cloudUserId" => $rcUser != null ? $rcUser->cloudID->id : -1
                ]);
        }
        return [

            "_id" => $room->getId(),
            "cloudUsers" => $cloudUserObjects,
            "usernames" => $room->getMembers(),
            "lastMessage" => null];

    }

    public function createImChat(Request $request)
    {
        $usernames = [];
        $cloud_ids = $request->input("cloudIds", []);
        foreach ($cloud_ids as $cloud_id) {
            $rcUser = RCUser::where('cloudid', '=', $cloud_id)->get()->first();
            if ($rcUser != null) {
                $usernames[] = $rcUser->username;
            }
        }
        $currentCloudUser = $this->getUserForToken($request);
        if($currentCloudUser == null)
            return $this->createJsonResponse("no user", true, 400,[]);

        $room = RocketChatProvider::createMessage($currentCloudUser, join(",", $usernames));
        if($room == null)
            return $this->createJsonResponse("user not allowed to create chat messages", true, 400,[]);

        return $this->createJsonResponse("ok", false, 200, ["newRoom" => $this->prepareRoom($usernames, $room), "rooms" => $this->getImList($request) ]);
    }


    public function createGroupChat(Request $request)
    {
        $usernames = [];
        $name = $request->name?: "Neue_Gruppe_".random_int(1,1000000);
        $cloud_ids = $request->input("cloudIds", []);
        foreach ($cloud_ids as $cloud_id) {
            $rcUser = RCUser::where('cloudid', '=', $cloud_id)->get()->first();
            if ($rcUser != null) {
                $usernames[] = $rcUser->username;
            }
        }
        $currentCloudUser = $this->getUserForToken($request);
        $room = RocketChatProvider::createGroupWithName($currentCloudUser, $usernames, $name);

        return $this->createJsonResponse("ok", false, 200, ["newRoom" =>  $this->prepareRoom($usernames, $room), "rooms" => $this->getImList($request) ]);
    }


    public function reportMessage(Request $request)
    {
        // $request->object  >>> JSON Object
        /**
         *
         * msgObj  -> desired message object
         * msgChunk -> chunk of last messages
         * isTechnical -> is report technical or content-related
         * additionalInfo -> additional info text
         */
        $object = $request->input("object");
        $isTechnical = ($object["isTechnical"] == "true");
        $chatMail = new ChatMail($object);
        if (!$isTechnical) {
            $mails = [];
            foreach ($object["members"] as $member) {
                $rcUser = RCUser::where("uid", '=', $member["_id"])->first();
                if ($rcUser && $rcUser->cloudID != null) {
                    $cloudID = $rcUser->cloudID;
                    if ($cloudID->hasRole("Mitarbeiter")) {
                        if (filter_var($cloudID->email, FILTER_VALIDATE_EMAIL)) {
                            $mails[] = $cloudID->email;
                        } else {
                            $lehrer = Lehrer::find($cloudID->getAppLogin("klassenbuch"));
                            if ($lehrer) {
                                $mails[] = $cloudID->email;
                            }

                        }
                    }
                }
            }

            Mail::to($mails)->send($chatMail);
        } else {
            $mail = "service-desk@schule-plus.com";
            $cc = "benjamin@schule-plus.com"; //"anja.pape@bbw-suedhessen.de";
            Mail::to($mail)
                ->cc($cc)
                ->send($chatMail);
        }

        return $this->createJsonResponse("ok", false, 200);

    }
}
