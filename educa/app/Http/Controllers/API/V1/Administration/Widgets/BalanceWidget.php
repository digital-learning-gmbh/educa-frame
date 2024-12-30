<?php


namespace App\Http\Controllers\API\V1\Administration\Widgets;
use App\Board;
use App\Fach;
use App\Http\Controllers\API\UnterrichtController;
use App\Klasse;
use App\KlassenbuchEintrag;
use App\LessonPlan;
use App\Schule;
use App\SchuljahrEntwurf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BalanceWidget extends Widget
{
    /**
     * @OA\Get (
     *     tags={"administration", "v1", "widgets", "balance"},
     *     path="/api/v1/administration/widgets/balance/teacher",
     *     description="",
     *     @OA\Parameter(
     *     name="token",
     *     required=true,
     *     in="query",
     *     description="jwt token",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="start",
     *     required=true,
     *     in="query",
     *     description="timestamp of the start",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="end",
     *     required=true,
     *     in="query",
     *     description="timestamp of the ende",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="school",
     *     required=true,
     *     in="query",
     *     description="id of the school",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="draft",
     *     required=true,
     *     in="query",
     *     description="id of the school year draft",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Returns the tasks, that are assigend to the current loggedin user")
     * )
     */
    public function teacher(Request $request)
    {
        $school = Schule::findOrFail($request->input("school"));
        $entwurf = SchuljahrEntwurf::findOrFail($request->input("draft"));

        $data = [];
        $startCarbon = Carbon::createFromTimestamp($request->input("start"));
        $endCarbon = Carbon::createFromTimestamp($request->input("end"));
        $dozent = $school->lehrer()->orderBy('lastname')->get();


        $defaultUE = 60;

        if ($defaultUE == 0)
            $defaultUE = 60;

        $weeks = $startCarbon->diffInWeeks($endCarbon);
        foreach($dozent as $t)
        {
            $node = [];
            $node["dozent"] = $t->displayName;
            if($t->week_hours == null) {
                $node["soll"] = "<i>Nicht hinterlegt</i>";
                $node["soll_zeitraum"] = "n.a";
                $node["ist_zeitraum"] = "n.a";
                $node["prozent"] = "<div class='colorCell'>n.a.</div>";
            } else {
                $node["soll"] = $t->week_hours;
                $node["soll_zeitraum"] = $t->week_hours * $weeks;
                $node["ist_zeitraum"] = 0;
                $unterrichtsplan = UnterrichtController::getUnterrichtInternalWithoutFormat($startCarbon, $endCarbon, "teacher", $t->id, $entwurf);
                foreach ($unterrichtsplan as $lesson)
                {
                    $startLesson = new Carbon($lesson->getStartDate());
                    $endLesson = new Carbon($lesson->getEndDate());
                    if($lesson->deviant_ue != null && $lesson->deviant_ue != 0)
                    {
                        $time =  $lesson->deviant_ue*$defaultUE;
                        $node["ist_zeitraum"] += $time;
                    } else {
                        $node["ist_zeitraum"] += $startLesson->diffInMinutes($endLesson);
                    }
                }
                if($node["soll_zeitraum"] != 0) {
                    $node["prozent"] = round(($node["ist_zeitraum"] / ($node["soll_zeitraum"] * $defaultUE)) * 100, 2);
                    $node["prozent"] = "<div class='" . $this->getColorClass($node["prozent"]) . "'>" . $node["prozent"] . "%</div>";
                } else {
                    $node["prozent"] = 0;
                }
                $node["ist_zeitraum"] /= $defaultUE;
            }
            $data[] = $node;
        }
        return parent::createJsonResponseStatic('', false, 200,["data" => $data]);
    }

    private function getColorClass($value)
    {
        if($value < 20)
        {
            return "colorCell bg-danger  text-white";
        }
        if($value < 40 || $value > 90)
        {
            return "colorCell bg-warning";
        }
        return "colorCell";
    }

    /**
     * @OA\Get (
     *     tags={"administration", "v1", "widgets", "balance"},
     *     path="/api/v1/administration/widgets/balance/course",
     *     description="",
     *     @OA\Parameter(
     *     name="token",
     *     required=true,
     *     in="query",
     *     description="jwt token",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="start",
     *     required=true,
     *     in="query",
     *     description="timestamp of the start",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="end",
     *     required=true,
     *     in="query",
     *     description="timestamp of the ende",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="course",
     *     required=true,
     *     in="query",
     *     description="id of the school",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="draft",
     *     required=true,
     *     in="query",
     *     description="id of the school year draft",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Returns the tasks, that are assigend to the current loggedin user")
     * )
     */
    public function course(Request $request)
    {
        $klasse = Klasse::findOrFail($request->input("course"));
        $entwurf = SchuljahrEntwurf::findOrFail($request->input("draft"));

        $data = [];
        $start = Carbon::createFromTimestamp($request->input("start"));
        $end = Carbon::createFromTimestamp($request->input("end"));

        $unterrichtsplan = UnterrichtController::getUnterrichtInternalWithoutFormat($start->toDateTime(), $end->toDateTime(), "schoolclass", $klasse->id, $entwurf);
        $fachToUnterricht = [];
        foreach($unterrichtsplan as $unterricht) {
            if ($unterricht instanceof LessonPlan) {
                $fach_id = $unterricht->fach_id;
                if ($unterricht->fach_id == null) {
                    $fach_id = "-1";
                }
                if (!array_key_exists($fach_id, $fachToUnterricht)) {
                    $fachToUnterricht[$fach_id] = [];
                }
                $fachToUnterricht[$fach_id][] = $unterricht;
            }
        }

        foreach ($fachToUnterricht as $key=>$value)
        {
            $node = [];
            if($key == "-1")
            {
                $node["fach"] = "Kein Fach";
            } else {
                $node["fach"] = Fach::findOrFail($key)->name;
            }
            $node["soll_zeitraum"] = 0;
            $node["ist_zeitraum"] = 0;
            foreach ($value as $lesson)
            {
                $startLesson = new Carbon($lesson->getStartDate());
                $endLesson = new Carbon($lesson->getEndDate());
                $node["soll_zeitraum"] += $startLesson->diffInMinutes($endLesson);

                // check if there is a klassenbuch entry
                $klassenbuch = KlassenbuchEintrag::where('lesson_id', '=', UnterrichtController::getUniqueIDForLesson($lesson))->first();
                if($klassenbuch != null)
                {
                    $node["ist_zeitraum"] += $startLesson->diffInMinutes($endLesson);
                }
            }
            $node["prozent"] =  round(($node["ist_zeitraum"] / $node["soll_zeitraum"]) * 100,2);
            $node["prozent"] = "<div class='".$this->getColorClassCourse($node["prozent"])."'>".$node["prozent"]."%</div>";
            $data[] = $node;
        }
        return parent::createJsonResponseStatic('', false, 200,["data" => $data]);
    }

    private function getColorClassCourse($value)
    {
        if($value < 20)
        {
            return "colorCell bg-danger  text-white";
        }
        if($value < 40)
        {
            return "colorCell bg-warning";
        }
        if($value == 100)
        {
            return "colorCell bg-success";
        }
        return "colorCell";
    }
}
