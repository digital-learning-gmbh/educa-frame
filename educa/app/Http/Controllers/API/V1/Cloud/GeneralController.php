<?php

namespace App\Http\Controllers\API\V1\Cloud;

use App\Appointment;
use App\Beitrag;
use App\CloudID;
use App\Dokument;
use App\Group;
use App\Http\Controllers\API\ApiController;
use App\Models\LearnContent;
use App\Task;
use App\xApiStatement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GeneralController extends ApiController
{
    private static $CACHE_TIME = 3600; // 1 hr
    private static $USE_CACHE = false; // switch for dev


    public function chartInfo(Request $request)
    {
        $availableWidgets = [
            [ "id" => "new_users", "default_height" => 9, "default_width" => 6, "x" => 0, "y" => 0 ],
            [ "id" => "active_users", "default_height" => 9, "default_width" => 6,  "x" => 7, "y" => 0],
            [ "id" => "object_counts", "default_height" => 9, "default_width" => 6,  "x" => 0, "y" => 10],
            [ "id" => "learn_content_counts", "default_height" => 9, "default_width" => 6,  "x" => 7, "y" => 10],
            [ "id" => "spaces", "default_height" => 9, "default_width" => 6, "x" => 0, "y" => 20 ],
            [ "id" => "feed_counts", "default_height" => 9, "default_width" => 6, "x" => 0, "y" => 20 ],
            [ "id" => "activity", "default_height" => 9, "default_width" => 6, "x" => 7, "y" => 20 ],
        ];

        return parent::createJsonResponse("widgets configuration", false, 200, ["widgets" => $availableWidgets]);
    }

    public function newUsers(Request $request)
    {
        $start = Carbon::createFromTimestamp($request->input("start"));
        $end = Carbon::createFromTimestamp($request->input("end"));

        $cloudIds = self::$USE_CACHE ? Cache::get("cloudIds_new") : null;

        if (!$cloudIds) {
            $cloudIds = DB::table("cloud_i_d_s")->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as value'))
                ->where("created_at","<",$end)->where("created_at",">=",$start)
                ->groupBy('date')
                ->get();
            foreach ($cloudIds as $cloudId)
            {
                $cloudId->x = Carbon::parse($cloudId->date);
                $cloudId->y = $cloudId->value;
            }
            Cache::put('cloudIds_new', $cloudIds, self::$CACHE_TIME); // 1 hr cache
        }
        return parent::createJsonResponse("new user timeline", false, 200, [
            "cloudIds" => $cloudIds
        ]);
    }

    public function activeUsers(Request $request)
    {
        $start = Carbon::createFromTimestamp($request->input("start"));
        $end = Carbon::createFromTimestamp($request->input("end"));

        $cloudIds = [];
        while ($start->isBefore($end))
        {
            $cloudIds_count = DB::table("cloud_i_d_s")
                ->where("created_at","<",$start->endOfDay())
                ->count();

            $cloudId = new \stdClass();
            $cloudId->x = Carbon::parse($start);
            $cloudId->y = $cloudIds_count;
            $cloudIds[] = $cloudId;

            $start->addDay();
        }
        return parent::createJsonResponse("active user timeline", false, 200, [
            "cloudIds" => $cloudIds
        ]);
    }

    public function activity(Request $request)
    {
        $start = Carbon::createFromTimestamp($request->input("start"));
        $end = Carbon::createFromTimestamp($request->input("end"));

        $cloudIds = [];
        while ($start->isBefore($end))
        {
            $cloudIds_count = xApiStatement::
                    whereBetween('created_at', [$start->startOfDay()->format('Y-m-d')." 00:00:00", $start->endOfDay()->format('Y-m-d')." 23:59:59"])
                ->count();

            $cloudId = new \stdClass();
            $cloudId->x = Carbon::parse($start);
            $cloudId->y = $cloudIds_count;
            $cloudIds[] = $cloudId;

            $start->addDay();
        }
        return parent::createJsonResponse("active user timeline", false, 200, [
            "cloudIds" => $cloudIds
        ]);
    }

    public function objects(Request $request)
    {
        $start = Carbon::createFromTimestamp($request->input("start"));
        $end = Carbon::createFromTimestamp($request->input("end"));

        $count_announcements = [];
        $count_events = [];
        $count_files = [];
        $count_tasks = [];
        $count_groups = [];
        while ($start->isBefore($end))
        {
            $count_announcement = Group::where("created_at","<",$start->endOfDay())
                ->count();

            $cloudId = new \stdClass();
            $cloudId->x = Carbon::parse($start);
            $cloudId->y = $count_announcement;
            $count_groups[] = $cloudId;

            $count_announcement = Beitrag::where("created_at","<",$start->endOfDay())
                ->count();

            $cloudId = new \stdClass();
            $cloudId->x = Carbon::parse($start);
            $cloudId->y = $count_announcement;
            $count_announcements[] = $cloudId;

            $count_announcement = Appointment::where("created_at","<",$start->endOfDay())
                ->count();

            $cloudId = new \stdClass();
            $cloudId->x = Carbon::parse($start);
            $cloudId->y = $count_announcement;
            $count_events[] = $cloudId;

            $count_announcement = Task::where("created_at","<",$start->endOfDay())
                ->count();

            $cloudId = new \stdClass();
            $cloudId->x = Carbon::parse($start);
            $cloudId->y = $count_announcement;
            $count_tasks[] = $cloudId;

            $count_announcement = Dokument::where("created_at","<",$start->endOfDay())
                ->count();

            $cloudId = new \stdClass();
            $cloudId->x = Carbon::parse($start);
            $cloudId->y = $count_announcement;
            $count_files[] = $cloudId;

            $start->addDay();
        }
        $datasets = [
            [
                "fill" => true,
                "label" => "Gruppen",
                "data" => $count_groups,
                "borderColor" => 'rgb(217, 83, 79)',
                "backgroundColor" => 'rgba(217, 83, 79, 0.5)',
                "tension" => 0.1
            ],
            [
                "fill" => true,
                "label" => "Ankündigungen",
                "data" => $count_announcements,
                "borderColor" => 'rgb(92, 184, 92)',
                "backgroundColor" => 'rgba(92, 184, 92, 0.5)',
                "tension" => 0.1
            ],
            [
                "fill" => true,
                "label" => "Termine",
                "data" => $count_events,
                "borderColor" => 'rgb(240, 173, 78)',
                "backgroundColor" => 'rgba(240, 173, 78, 0.5)',
                "tension" => 0.1
            ],
            [
                "fill" => true,
                "label" => "Dokumente",
                "data" => $count_files,
                "borderColor" => 'rgb(2, 117, 216)',
                "backgroundColor" => 'rgba(2, 117, 216, 0.5)',
                "tension" => 0.1
            ],
            [
                "fill" => true,
                "label" => "Aufgaben",
                "data" => $count_tasks,
                "borderColor" => 'rgb(41, 43, 44)',
                "backgroundColor" => 'rgba(41, 43, 44, 0.5)',
                "tension" => 0.1
            ]
        ];

        return parent::createJsonResponse("active user timeline", false, 200, [
            "cloudIds" => $datasets
        ]);
    }

    public function feeds(Request $request)
    {
        $start = Carbon::createFromTimestamp($request->input("start"));
        $end = Carbon::createFromTimestamp($request->input("end"));

        $cloudIds = self::$USE_CACHE ? Cache::get("cloudIds_new") : null;

        if (!$cloudIds) {
            $cloudIds = DB::table("feed_activities")->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as value'))
                ->where("created_at","<",$end)->where("created_at",">=",$start)
                ->groupBy('date')
                ->get();
            foreach ($cloudIds as $cloudId)
            {
                $cloudId->x = Carbon::parse($cloudId->date);
                $cloudId->y = $cloudId->value;
            }
            Cache::put('cloudIds_new', $cloudIds, self::$CACHE_TIME); // 1 hr cache
        }
        return parent::createJsonResponse("new user timeline", false, 200, [
            "cloudIds" => $cloudIds
        ]);
    }

    public function learnContentObjects(Request $request)
    {
        $start = Carbon::createFromTimestamp($request->input("start"));
        $end = Carbon::createFromTimestamp($request->input("end"));

        $count_announcements = [];
        $count_events = [];
        $count_files = [];
        $count_tasks = [];
        $count_groups = [];
        $count_youtube = [];
        while ($start->isBefore($end))
        {
            $count_announcement = LearnContent::where("foreignType","=","text")
                ->count();

            $cloudId = new \stdClass();
            $cloudId->x = Carbon::parse($start);
            $cloudId->y = $count_announcement;
            $count_groups[] = $cloudId;

            $count_videos = LearnContent::where("foreignType","=","document")->where(function ($query) {
                foreach (LearnContent::$MOVIE_extensions as $extension)
                {
                    $query->orWhereJsonContains('metadata->suffix', $extension);
                }
            })->count();

            $cloudId = new \stdClass();
            $cloudId->x = Carbon::parse($start);
            $cloudId->y = $count_videos;
            $count_tasks[] = $cloudId;

            $count_images = LearnContent::where("foreignType","=","document")->where(function ($query) {
                foreach (LearnContent::$IMAGE_extensions as $extension)
                {
                    $query->orWhereJsonContains('metadata->suffix', $extension);
                }
            })->count();

            $cloudId = new \stdClass();
            $cloudId->x = Carbon::parse($start);
            $cloudId->y = $count_images;
            $count_files[] = $cloudId;

            $count_announcement = LearnContent::where("foreignType","=","document")
                ->count() -$count_images -$count_videos;

            $cloudId = new \stdClass();
            $cloudId->x = Carbon::parse($start);
            $cloudId->y = $count_announcement;
            $count_announcements[] = $cloudId;

            $count_announcement = LearnContent::where("foreignType","=","h5p")
                ->count();

            $cloudId = new \stdClass();
            $cloudId->x = Carbon::parse($start);
            $cloudId->y = $count_announcement;
            $count_events[] = $cloudId;

            $count_announcement = LearnContent::where("foreignType","=","youtube")->orWhere("foreignType","=","webContent")
                ->count();

            $cloudId = new \stdClass();
            $cloudId->x = Carbon::parse($start);
            $cloudId->y = $count_announcement;
            $count_youtube[] = $cloudId;

            $start->addDay();
        }
        $datasets = [
            [
                "fill" => true,
                "label" => "Texte",
                "data" => $count_groups,
                "borderColor" => 'rgb(217, 83, 79)',
                "backgroundColor" => 'rgba(217, 83, 79, 0.5)',
                "tension" => 0.1
            ],
            [
                "fill" => true,
                "label" => "Dokumente",
                "data" => $count_announcements,
                "borderColor" => 'rgb(92, 184, 92)',
                "backgroundColor" => 'rgba(92, 184, 92, 0.5)',
                "tension" => 0.1
            ],
            [
                "fill" => true,
                "label" => "H5P Inhalte",
                "data" => $count_events,
                "borderColor" => 'rgb(240, 173, 78)',
                "backgroundColor" => 'rgba(240, 173, 78, 0.5)',
                "tension" => 0.1
            ],
            [
                "fill" => true,
                "label" => "Bilder",
                "data" => $count_files,
                "borderColor" => 'rgb(2, 117, 216)',
                "backgroundColor" => 'rgba(2, 117, 216, 0.5)',
                "tension" => 0.1
            ],
            [
                "fill" => true,
                "label" => "Videos",
                "data" => $count_tasks,
                "borderColor" => 'rgb(41, 43, 44)',
                "backgroundColor" => 'rgba(41, 43, 44, 0.5)',
                "tension" => 0.1
            ],
            [
                "fill" => true,
                "label" => "Web-Inhalte",
                "data" => $count_youtube,
                "borderColor" => 'rgb(255,102,0)',
                "backgroundColor" => 'rgba(255,102,0, 0.5)',
                "tension" => 0.1
            ]
        ];

        return parent::createJsonResponse("active user timeline", false, 200, [
            "cloudIds" => $datasets
        ]);
    }

    public function spaces(Request $request)
    {
        $factor = 1.35;
        $datasets = [
            "labels" => ['Freier Speicher in GB', 'Belegte Speicher in GB'],
            "datasets" => [
                [
                    "label" => "GB",
                    "data" => [disk_free_space("./") /  pow(1024, 3)*$factor,(disk_total_space("./") - disk_free_space("./"))/ pow(1024, 3)*$factor],
                    "backgroundColor" => [
                        "rgba(217, 83, 79, 0.5)",
                        "rgba(92, 184, 92, 0.5)",
                    ],
                    "borderColor" => [
                        "rgb(217, 83, 79)",
                        "rgb(92, 184, 92)",
                    ]
                ]
            ]
        ];
        return parent::createJsonResponse("active user timeline", false, 200, [
            "spaces" => $datasets
        ]);
    }
}
