<?php

namespace App\Http\Controllers\API\V1;

use App\Appointment;
use App\Beitrag;
use App\CloudID;
use App\Dokument;
use App\FeedActivity;
use App\Group;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\V1\Feed\Card;
use App\Http\Controllers\API\V1\Feed\CardFactory;
use App\Http\Controllers\API\V1\Feed\Cards\AnnouncementCard;
use App\Http\Controllers\API\V1\Feed\Cards\AppointmentCard;
use App\Http\Controllers\API\V1\Feed\Cards\ColoredCard;
use App\Http\Controllers\API\V1\Feed\Cards\DocumentCard;
use App\Http\Controllers\API\V1\Feed\Cards\GroupCard;
use App\Http\Controllers\API\V1\Feed\Cards\SupportCard;
use App\Http\Controllers\API\V1\Feed\Cards\TaskCard;
use App\Http\Controllers\API\V1\Feed\Cards\TaskRatedCard;
use App\Http\Controllers\API\V1\Feed\Cards\TaskRestCard;
use App\Http\Controllers\API\V1\Feed\Cards\TaskSubmittedCard;
use App\Http\Controllers\API\V1\Groups\GroupController;
use App\Http\Controllers\API\V1\xAPI\XAPIBaseController;
use App\Http\Controllers\API\V1\xAPI\XAPIVerbs;
use App\Models\FeedCardRead;
use App\Models\GroupCache;
use App\Models\SingleAppointment;
use App\Models\SupportTicket;
use App\PermissionConstants;
use App\Providers\AppServiceProvider;
use App\Section;
use App\Submission;
use App\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\CommonMark\Util\Xml;
use StuPla\CloudSDK\Permission\Models\Role;
use StuPla\CloudSDK\Permission\Scope;

class FeedController extends ApiController
{
    /**
     * @OA\Get (
     *     tags={"v1","feed"},
     *     path="/api/v1/feed",
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
     *       name="lastTime",
     *       required=false,
     *       in="query",
     *       description="lastTimestamp for paging",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function feed(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $lastTimestamp = $request->input("lastTime");
        $filter = $request->input("filter","all");
        $groupIds = DB::table('cloudid_group')->where('cloudid', '=', $cloud_user->id)->pluck('group_id');
        $feedActivity = FeedActivity::where(function ($query) use($groupIds,$cloud_user) {
            $query->where(function ($query2) use ($groupIds) {
                $query2->where('belong_type', '=', 'group');
                $query2->whereIn('belong_id', $groupIds);
            })->orWhere(function ($query2) use ($cloud_user) {
                $query2->where('belong_type', '=', 'user');
                $query2->where('belong_id', $cloud_user->id);
            });
        });
        $feedActivity = $feedActivity->whereIn('type', static::feedTypes()->filter(function ($element) use($filter) {
            if($filter == "all")
                return true;
            if($filter == "task" && str_contains($element,"task"))
            {
                return true;
            }
            if($filter == "announcement" && str_contains($element,"announcement"))
            {
                return true;
            }
            if($filter == "event" && str_contains($element,"appointment"))
            {
                return true;
            }
            return false;
        })->all());
        if($lastTimestamp != null && $lastTimestamp != "-1" && $lastTimestamp != "")
        {
            $feedActivity = $feedActivity->where('created_at', '<', Carbon::parse($lastTimestamp));
        } else {
            $feedActivity = $feedActivity->where('created_at', '<', Carbon::now());
        }
        $feedActivity = $feedActivity->groupBy('merge_id','type');
        $feedActivity = $feedActivity->orderByRaw('max(created_at) DESC')->take(40)->get();
        $feedData = self::generateCard($feedActivity, $cloud_user);
        $lastTimestamp = null;
        foreach ($feedActivity as $item)
        {
            $lastTimestamp = $item->created_at;
        }
        return parent::createJsonResponse("",false, 200, ["feedData" => $feedData, "lastTimestamp" => $lastTimestamp]);
    }

    public static function generateCard($feedActivity,$cloud_user = null) {

        $cardFactory = new CardFactory();

        foreach ($feedActivity as $feedElement) {
            $payload = json_decode($feedElement->payload);

            $user = null;
            if ($feedElement->creator_model == "cloud") {
                $user = CloudID::find($feedElement->creator);
            }

            $card = null;
            if ($feedElement->type == "group.created") {
                if ($payload == null || $payload->group == null) {
                    continue;
                }
                $group = Group::find($payload->group->id);
                if ($group == null) {
                    continue;
                }
                $card = new GroupCard($group,$user);
            } else if ($feedElement->type == "announcement.created") {
                if ($payload == null || $payload->announcement == null) {
                    continue;
                }
                $user = CloudID::find($payload->announcement->cloudid);
                $announcement = Beitrag::find($payload->announcement->id);
                if ($announcement == null || $user == null || ($cloud_user != null && DB::table("blocked_users")->where("cloudid","=",$announcement->cloudid)->where("by_cloudid","=",$cloud_user->id)->exists()))
                    continue;
                if(!$announcement->section()->isAllowed($cloud_user,PermissionConstants::EDUCA_SECTION_VIEW))
                    continue;
                // $announcement->load("comments");
                $announcement->load("likes");
                $announcement->load("media");
                $announcement->load("mentions");

                $announcement->commentsFiltered($cloud_user);
                $card = new AnnouncementCard($user, $announcement, $payload->group, $announcement->section());
            } else if ($feedElement->type == Appointment::$FEED_INVITE) {
                if ($payload == null) {
                    continue;
                }
                $appointment = Appointment::find($payload->id);
                if ($appointment == null) {
                    continue;
                }
                $inSection = false;
                foreach ($appointment->sections as $section)
                {
                    if($section->isAllowed($cloud_user,PermissionConstants::EDUCA_SECTION_VIEW))
                    {
                        $inSection = true;
                    }
                }
                if(!$inSection)
                    continue;

                $card = new AppointmentCard($appointment, "create");
            } else if ($feedElement->type == Appointment::$FEED_UPDATED) {
                if ($payload == null) {
                    continue;
                }
                $appointment = Appointment::find($payload->id);
                if ($appointment == null) {
                    continue;
                }

                $inSection = false;
                foreach ($appointment->sections as $section)
                {
                    if($section->isAllowed($cloud_user,PermissionConstants::EDUCA_SECTION_VIEW))
                    {
                        $inSection = true;
                    }
                }
                if(!$inSection)
                    continue;

                $card = new AppointmentCard($appointment, "update");
            }  else if ($feedElement->type == Appointment::$FEED_DELETED) {
                if ($payload == null) {
                    continue;
                }
                if(property_exists($payload,"hasRepetition") && $payload->hasRepetition) {
                    $card = new ColoredCard("Serientermin wurde abgesagt", "Die Serie '".$payload->title."' beginnend am ".Carbon::parse($payload->startDate)->format("d.m.Y H:i")."-".Carbon::parse($payload->endDate)->format("H:i")." wurde abgesagt.");
                    $card->setColor("red");
                } else {
                    $card = new ColoredCard("Termine wurde abgesagt", "Der Termin '".$payload->title."' am ".Carbon::parse($payload->startDate)->format("d.m.Y H:i")."-".Carbon::parse($payload->endDate)->format("H:i")." wurde abgesagt.");
                    $card->setColor("red");
                }

            } else if ($feedElement->type == Appointment::$FEED_SINGLE_REMOVED) {
                if ($payload == null) {
                    continue;
                }
                $card = new ColoredCard("Termin einer Serie wurde abgesagt", "Der Termin '".$payload->title."' am ".Carbon::parse($payload->startDate)->format("d.m.Y H:i")."-".Carbon::parse($payload->endDate)->format("H:i")." der Serie wurde abgesagt.");
                $card->setColor("red");
            } else if ($feedElement->type == Appointment::$FEED_SINGLE_MOVE) {
                if ($payload == null) {
                    continue;
                }
                $appointment = Appointment::find($payload->appointment_id);
                if($appointment == null)
                    continue;
                $card = new ColoredCard("Termin einer Serie wurde geändert", "Der Termin der Serie '".$appointment->title."' am ".Carbon::parse($appointment->startDate)->format("d.m.Y H:i")."-".Carbon::parse($appointment->endDate)->format("H:i")." wurde geändert in ".Carbon::parse($payload->startDate)->format("d.m.Y H:i")."-".Carbon::parse($payload->endDate)->format("H:i")." abgesagt.");
                $card->setColor("white");
            } else if ($feedElement->type == Task::$FEED_CREATE) {
                if ($payload == null) {
                    continue;
                }
                $task = Task::find($payload->id);
                if($task == null)
                {
                    continue;
                }
                $card = new TaskCard($task);
            } else if ($feedElement->type == Task::$FEED_DELETED) {
                if ($payload == null) {
                    continue;
                }
                $card = new ColoredCard("Aufgabe wurde gelöscht", "Die Aufgabe '".$payload->title."' wurde gelöscht.");
                $card->setColor("blue");
            } else if ($feedElement->type == Task::$FEED_SUBMITTED) {
                if ($payload == null) {
                    continue;
                }
                $submission = Submission::find($payload->id);
                if($submission == null || $submission->task == null)
                {
                    continue;
                }
                $card = new TaskSubmittedCard($submission->task, $submission);
            } else if ($feedElement->type == Task::$FEED_RESET) {
                if ($payload == null) {
                    continue;
                }
                $submission = Submission::find($payload->id);
                if($submission == null || $submission->task == null)
                {
                    continue;
                }
                $card = new TaskRestCard($submission->task, $submission);
            } else if ($feedElement->type == Task::$FEED_RATED) {
                if ($payload == null) {
                    continue;
                }
                $submission = Submission::find($payload->id);
                if($submission == null || $submission->task == null)
                {
                    continue;
                }
                $card = new TaskRatedCard($submission->task, $submission);
            } else if ($feedElement->type == Dokument::$FEED_INFO) {
                if ($payload == null) {
                    continue;
                }
                $dokument = Dokument::find($payload->id);
                if($dokument == null || $dokument->model() == null)
                {
                    continue;
                }
                $model = $dokument->model();
                if(!$model instanceof Section || !$model->isAllowed($cloud_user,PermissionConstants::EDUCA_SECTION_FILES_OPEN))
                    continue;
                $card = new DocumentCard($dokument);
            }  else if ($feedElement->type == "coloredCard") {
                if ($payload == null) {
                    continue;
                }
                $card = new ColoredCard($payload->headline,$payload->content);
            } else if ($feedElement->type == SupportTicket::$FEED_UPDATED) {
                if ($payload == null || !property_exists($payload,"supportTicket") || !property_exists($payload, "additionalInformation")) {
                    continue;
                }
                $supportTicket = SupportTicket::find($payload->supportTicket->id);
                $additionalInformation = $payload->additionalInformation;
                if($supportTicket == null)
                    continue;
                $card = new SupportCard($supportTicket,$additionalInformation);
            }

            if ($card != null) {
                $card->id = $feedElement->id;
                $card->read = FeedCardRead::where("feed_activity_id","=",$feedElement->id)->where("cloud_i_d_id","=",$cloud_user->id)->exists();
                if(!$card->read)
                {
                    $read = new FeedCardRead();
                    $read->feed_activity_id = $feedElement->id;
                    $read->cloud_i_d_id = $cloud_user->id;
                    $read->save();

                    $object = json_decode(json_encode($card),true);
                    $object["objectType"] = "feedCard";
                    XAPIBaseController::createStatement($cloud_user,null,$object,XAPIVerbs::$ACCESS);
                }
                $cardFactory->addCard($card);
            }
        }

        return $cardFactory->build();
    }

    public static function feedTypes()
    {
        return collect([
            "announcement.created",
            "group.created",
            Appointment::$FEED_INVITE,
            Appointment::$FEED_DELETED,
            Appointment::$FEED_UPDATED,
            Appointment::$FEED_SINGLE_MOVE,
            Appointment::$FEED_SINGLE_REMOVED,
            Appointment::$FEED_SINGLE_DELETE,
            Task::$FEED_CREATE,
            Task::$FEED_DELETED,
            Task::$FEED_SUBMITTED,
            Task::$FEED_RATED,
            Task::$FEED_RESET,
            Dokument::$FEED_INFO,
            SupportTicket::$FEED_UPDATED
        ]);
    }

    public function statistics(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $feed_activity_id = $request->input("feed_activity_id");

        $feed_activitys = FeedCardRead::where("feed_activity_id","=",$feed_activity_id)->get();
        $min_date = FeedCardRead::where("feed_activity_id","=",$feed_activity_id)->orderBy("created_at")->first();

        $grouped = [];
        $cloud_users = [];
        $globale_rollen = Role::where("scope_id","=",Scope::getDefaultId())->where("scope_name","=",Scope::getDefaultName())->get();

        $datasets = [];
        $count_rollen = [];

        foreach ($globale_rollen as $role)
        {
            $count_rollen[$role->id] = 0;
            $grouped[$role->id] = [];
        }

        foreach ($feed_activitys as $feed_activity)
        {
            $rolen = $feed_activity->creator_role_display();
            foreach ($rolen as $role)
            {
                $date = Carbon::parse($feed_activity->created_at)->format("Y-m-d");
                if (!array_key_exists($date, $grouped[$role->id])) {
                    $grouped[$role->id][$date] = 0;
                }
                $grouped[$role->id][$date] += 1;
                $count_rollen[$role->id] += 1;
            }
            // $cloud_users[] = [ "count" => $feed_activity->creator_display(), "role" => $rolen->pluck("name")->join(", ") ];
        }

        foreach ($globale_rollen as $role)
        {
            if($min_date != null)
            {
                $startDate = Carbon::parse($min_date->created_at);
                while ($startDate->isBefore(Carbon::now()))
                {
                    $day = $startDate->format("Y-m-d");
                    if(!array_key_exists($day,$grouped[$role->id]))
                    {
                        $grouped[$role->id][$day] = 0;
                    }
                    $startDate = $startDate->addDay();
                }
            }
        }

        $faker = \Faker\Factory::create();
        foreach ($globale_rollen as $role)
        {
            $color = $faker->rgbColor();
            $finalSet = [];
            if(array_key_exists($role->id, $grouped)) {
                foreach ($grouped[$role->id] as $key => $value) {
                    $finalSet[] = ["x" => Carbon::parse($key), "y" => $value];
                }
            } else {
                continue;
            }

            $datasets[] = [
                "fill" => true,
                "label" => $role->name,
                "data" =>  $finalSet,
                "borderColor" => 'rgb('.$color.')',
                "backgroundColor" => 'rgba('.$color.', 0.5)',
                "tension" => 0.1
            ];
        }

        $mapped_rollen_count = [];
        foreach ($count_rollen as $key => $value)
        {
            $mapped_rollen_count[] = ["count" => $value, "role" => Role::findById($key)->name ];
        }


        return parent::createJsonResponse("statistics",false, 200, ["history" => $datasets, "count_rollen" => $mapped_rollen_count]);
    }


    public function sections(Request $request)
    {
        $user = parent::getUserForToken($request);
        if ($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $tenant = AppServiceProvider::getTenant();
        $groupController = new GroupController();
        if($request->input("group_id") && $request->get("group_id") != "null") {
            $groups = [];
            $cache = GroupCache::where("cloudid","=",$user->id)->where("groupid","=",$request->input("group_id"))->first();
            if($cache != null) {
                $groups[] = json_decode($cache->cache);
            }
        } else {
            $groups = $groupController->loadGroups($user);
        }
        $sections = $groupController->loadSections($user, $groups);

        return parent::createJsonResponse("groups",false, 200, ["groups" => $groups, "sections" => $sections, "dashboardLevel" => $tenant->dashboardLevel ]);
    }

    public function lastSeenSections(Request $request)
    {
        $user = parent::getUserForToken($request);
        if ($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $tenant = AppServiceProvider::getTenant();

        $sections = $user->lastSeen()->with("section.group")->take(6)->get();

        return parent::createJsonResponse("groups",false, 200, ["sections" => $sections ]);
    }

}
