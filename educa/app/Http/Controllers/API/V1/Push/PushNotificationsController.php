<?php

namespace App\Http\Controllers\API\V1\Push;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Shared\RocketChatProvider;
use App\Mail\RocketChatMessage;
use App\Providers\AppServiceProvider;
use App\PushToken;
use App\RCUser;
use App\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use StuPla\CloudSDK\rocketchat\Client\ChannelClient;
use StuPla\CloudSDK\rocketchat\Client\GroupClient;
use StuPla\CloudSDK\rocketchat\Client\RoomClient;
use StuPla\CloudSDK\rocketchat\Models\User;

class PushNotificationsController extends ApiController
{

    public function rocketChatHook(Request $request)
    {
        $sender = RCUser::where('username', '=', $request->input("user_name"))->first();
        $orginDisplay = $request->input("user_name");
        $roomId = $request->input("channel_id");

        if($sender == null)
        {
            return "";
        }
        $user = new User([
            "id" => $sender->uid,
            "username" => $sender->username,
            "password" => $sender->password,
        ]);
        $user->setAuthToken($sender->access_token);

        $channelClient = new RoomClient();
        $channelClient->setAuth($user->getId(), $user->getAuthToken());
        $room = $channelClient->info($roomId);

        if($room->t != "d" && $room->t != "p") // we support only direct messages and privat chats
            return "";

        if($sender->cloudID != null)
        {
            $orginDisplay = $sender->cloudID->name;
        }

        $userArray = [];
        if($room->t == "p")
        {
            $groupClient = new GroupClient();
            $groupClient->setAuth($user->getId(), $user->getAuthToken());
            $members = $groupClient->members($roomId);
            foreach ($members as $member)
            {
                $userArray[] = $member->username;
            }
            $sectionApp = DB::table('section_group_apps')->whereRaw("JSON_CONTAINS(parameters, '{\"roomId\":\"$roomId\"}')")->first();
            if($sectionApp != null && $sectionApp->section_id != null)
            {
                $section = Section::find($sectionApp->section_id);
                $orginDisplay .= " @ ".$section->group->name." \ ".$section->name;
            }
        } else {
            $userArray = $room->usernames;
        }



        foreach ($userArray as $username)
        {
            if($username == $request->input("user_name"))
                continue;
            $responder = RCUser::where('username', '=', $username)->first();
            if($responder != null && $responder->cloudID != null)
            {
                self::pushToCloudUser($responder->cloudID, $orginDisplay, $request->input("text"), $roomId, "chat", null, $room);
            }
        }

        return ""; // empty = okay
    }

    public static function pushToCloudUser($cloudUser, $title, $content = "", $model_id = "", $model_type = "none", $image = null, $payload = [])
    {
        $tenant = AppServiceProvider::getTenant();
        foreach ($cloudUser->pushTokens as $pushToken)
        {
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60*20);

            $notificationBuilder = new PayloadNotificationBuilder($title);
            $notificationBuilder->setBody($content)->setSound('default');

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData(['model_id' => $model_id,
                "model_type" => $model_type, "extra_payload" => $payload, "cloud_id" => $cloudUser->id, "tenant" => $tenant ]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            $token = $pushToken->push_token;

            FCM::sendTo($token, $option, $notification,$data);
        }

        if(config("educa.push.email"))
        {
            $email = $cloudUser->email;
            if(config("educa.push.overrideEmail"))
            {
                $email = config("educa.push.overrideEmail");
            }
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($email)->send(new RocketChatMessage($cloudUser,$title,$content, AppServiceProvider::getTenant()));
            }
        }
    }
}
