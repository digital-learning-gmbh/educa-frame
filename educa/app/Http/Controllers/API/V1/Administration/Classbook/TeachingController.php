<?php

namespace App\Http\Controllers\API\V1\Administration\Classbook;

use App\FehlzeitTyp;
use App\Http\Controllers\API\UnterrichtController;
use App\Http\Controllers\API\V1\Administration\AdministationApiController;
use App\Http\Controllers\API\V1\Administration\Widgets\ActivitiesWidget;
use App\KlassenbuchEintrag;
use App\KlassenbuchTeilnahme;
use App\KlassenbuchTeilnahmeVorgemerkt;
use App\LehrplanEinheit;
use App\LehrplanGroups;
use App\Lesson;
use App\LessonPlan;
use App\Schuler;
use App\Schuljahr;
use App\SchuljahrEntwurf;
use Carbon\Traits\Creator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use StuPla\CloudSDK\formular\models\FormularRevision;

class TeachingController extends AdministationApiController
{
    public function studentList()
    {

    }

    public function klassenbuchEntryList()
    {

    }

    public function klassenbuchEntry(Request $request)
    {
        if($request->has("year_id"))
        {
            $schoolYear = Schuljahr::find($request->input("year_id"));
        } else {
            $id = $request->input("id");
            $type = $request->input("type");
            if($type == "lessonPlan") {
                $lessonPlan = LessonPlan::find($id);
            } else {
                $lessonPlan = Lesson::find($id);
            }
            $schoolYear = $lessonPlan->draft->schuljahr;
        }
        $uniqueId = $request->input("unique_id");
        if($schoolYear == null)
            return parent::createJsonResponse("Schoolyear not found", true, 404);

        $classbookInfo = self::getClassbookEntryForUniqueID($uniqueId, $schoolYear);
        return parent::createJsonResponse("Classbook information", false, 200, $classbookInfo);
    }

    public function memberList(Request $request)
    {
        $id = $request->input("id");
        $type = $request->input("type");
        $uniqueId = $request->input("unique_id");

        $klassenbuch_id = "-1";

        $klassenbuch = KlassenbuchEintrag::where('lesson_id', '=', $uniqueId)->first();
        if ($klassenbuch != null) {
            $klassenbuch_id = $klassenbuch->id;
        }
        //print_r($klassenbuch_id);
        // Is this safe? Could also be a Lesson
        if ($type == "lessonPlan") {
            $lessonPlan = LessonPlan::find($id);
        } else {
            $lessonPlan = Lesson::find($id);
        }
        if ($lessonPlan->draft == null) {
            $c = LessonPlan::find($lessonPlan->getParentId());
            $schoolYear = $c->draft->schuljahr;
        } else {
            $schoolYear = $lessonPlan->draft->schuljahr;
        }


        if ($request->has("start") && $request->input("start") != "undefined") {
            $datum = Carbon::createFromTimestamp($request->input("start"))->format("Y/m/d");
        } else {
            $datum = date("Y/m/d", strtotime($lessonPlan->startDate));
        }

        $members = self::getMembers($lessonPlan, $datum, $klassenbuch_id, $schoolYear, $request->input("start"), $request->input("end"));
        usort($members,  Array($this,"sortByLastname"));
        return parent::createJsonResponse("Classbook member information", false, 200, ["members" => $members]);
    }

    private static function getMembers($lessonPlan, $datum, $klassenbuch_id, $schoolYear, $start, $end) {
        $studentsIds = [];
        $data = [];
        $subject = $lessonPlan->fach;
        $klassen = $lessonPlan->klassen;
        foreach ($klassen as $klasse)
        {
            $lehrplanEinheiten = array(); // iba different LehrplanEinheit::where('fach_id', $lessonPlan->fach_id)->whereIn('lehrplan_id',$klasse->getLehrplan->pluck("id")->toArray())->get();
            if(count($lehrplanEinheiten) == 0)
            {
                if ($klasse->type == "cluster_group") {
                    foreach ($klasse->klassen as $klasseMember) {
                        foreach ($klasseMember->schulerAtDatum($datum)->orderBy('lastname')->get() as $schuler) {
                            $studentsIds[] = $schuler->id;
                        }
                    }
                } else {
                    foreach ($klasse->schulerAtDatum($datum)->orderBy('lastname')->get() as $schuler) {
                        $studentsIds[] = $schuler->id;
                    }
                }
            }
        }

        $schulers = Schuler::find($studentsIds);

        if($lessonPlan->isManualStudents)
        {
            $schulers = $lessonPlan->students;
        }


        foreach ($schulers as $schuler) {
                $entry = KlassenbuchTeilnahme::where("eintrag_id", "=", $klassenbuch_id)->where("schuler_id", "=", $schuler->id)->first();

                $lastFehlzeit = FehlzeitTyp::find(DB::table('klassenbuch_teilnahmes')
                    ->join("klassenbuch_eintrags","klassenbuch_eintrags.id","=","klassenbuch_teilnahmes.eintrag_id")
                    ->where("schuler_id", "=", $schuler->id)
                    ->where('startDate', '>=', Carbon::createFromTimestamp($start)->setHour(1)->setMinute(0)->toDateTime())
                    ->where('startDate', '<=', Carbon::createFromTimestamp($start)->toDateTime())
                    ->orderBy("endDate","DESC")->pluck("klassenbuch_teilnahmes.fehlzeit_typ_id")->first());

                $singleObject = [];
                $singleObject["id"] = $schuler->id;
                $singleObject["firstname"] = $schuler->firstname;
                $singleObject["lastname"] = $schuler->lastname;
                $singleObject["options"] = [];
                // Laden der vorgemerkten Fehlzeiten

            if($start != null && $start != "undefined") {
                $vorgemerkt = KlassenbuchTeilnahmeVorgemerkt::where('schuler_id', '=', $schuler->id)
                    ->where('startDate', '<=', Carbon::createFromTimestamp($start)->toDateTime())
                    ->where('endDate', '>=', Carbon::createFromTimestamp($end)->toDateTime())->first();
            } else {
                $vorgemerkt = KlassenbuchTeilnahmeVorgemerkt::where('schuler_id', '=', $schuler->id)
                    ->where('startDate', '<=', $lessonPlan->getStartDate())
                    ->where('endDate', '>=', $lessonPlan->getStartDate())->first();
            }
                //
                $singleObject["notes"] = "";
                if($entry != null)
                {
                    $singleObject["notes"] = $entry->notes;
                }
                foreach($schoolYear->fehlzeiten as $fehlzeit) {
                    if ($fehlzeit->aktive == 1) {
                        $singleObject["options"][] = $fehlzeit;

                        if(!array_key_exists("selected",$singleObject))
                        {
                            $singleObject["editable"] = true;
                            if($entry != null && $entry->fehlzeit_typ_id == $fehlzeit->id)
                            {
                                // d.h. hier wurde was ausgewählt!
                                $singleObject["selected"] = $fehlzeit;
                                $singleObject["information"] = "";
                                if($vorgemerkt != null) {
                                    $singleObject["editable"] = false;
                                    $singleObject["information"] = "Gesperrt - Vorgemerkte Fehlzeit " . Carbon::parse($vorgemerkt->startDate)->format("d.m. H:i") . "-" . Carbon::parse($vorgemerkt->endDate)->format("d.m. H:i");
                                }
                            } else if($vorgemerkt != null && $vorgemerkt->fehlzeit_typ_id == $fehlzeit->id && $entry == null)
                            {
                                // hier wurde etwas vorgemerkt
                                $singleObject["selected"] = $fehlzeit;
                                if($fehlzeit->standart != 1 ) {
                                    $singleObject["information"] = "Gesperrt - Vorgemerkte Fehlzeit " . Carbon::parse($vorgemerkt->startDate)->format("d.m. H:i") . "-" . Carbon::parse($vorgemerkt->endDate)->format("d.m. H:i");
                                }
                                $singleObject["editable"] = false;
                            } else if($entry == null && $vorgemerkt == null && $lastFehlzeit != null && $lastFehlzeit->id == $fehlzeit->id && $lastFehlzeit->isOverTake == 1)
                            {
                                // hier wurde an dem Tag bereits etwas erfasst
                                $singleObject["selected"] = $fehlzeit;
                                if($fehlzeit->standart != 1 ) {
                                    $singleObject["information"] = "Aus vorherigem Unterricht übertragen";
                                }
                            } else if($entry == null && $vorgemerkt == null && ($lastFehlzeit == null || $lastFehlzeit->isOverTake == 0) && $fehlzeit->standart == 1)
                            {
                                $singleObject["selected"] = $fehlzeit;
                                $singleObject["editable"] = true;
                            }
                        }

                    }
                }
                $data[] = $singleObject;
            }
        return $data;
    }

    function sortByLastname($a, $b) {
        return $a['lastname'] > $b['lastname'];
    }

    public function saveKlassenbuchEntry(Request $request)
    {
        $id = $request->input("id");
        $type = $request->input("type");
        $uniqueId = $request->input("unique_id");
        $formularRevisionId = $request->input("form_revision_id");

        // try to calculate the missing id
        if($id == null && count(explode("_",$uniqueId))>1)
        {
            $id = explode("_",$uniqueId)[1];
        }

        if($type == "lessonPlan") {
            $lessonPlan = LessonPlan::find($id);
        } else {
            $lessonPlan = Lesson::find($id);
        }
        if($lessonPlan == null)
            return parent::createJsonResponse("no classbook info, lesson or lessonplan not found", true, 401);

        //$duration = Carbon::parse($lessonPlan->startDate)->diffInMinutes($lessonPlan->endDate);

        // search for Klassenbucheintrag
        $klassenbuch = KlassenbuchEintrag::where('lesson_id', '=', $uniqueId)->first();
        if($klassenbuch == null)
        {
            $klassenbuch = new KlassenbuchEintrag;
            $klassenbuch->lesson_id = $uniqueId;
            $klassenbuch->save();
        }
        $klassenbuch->anrechnen = $request->input("consider_flag","1");
        if($formularRevisionId != "-1") {
            $klassenbuch->formular_revision_id = $formularRevisionId;
            $klassenbuch->formular_data = $request->input("form_data");
        }
        $klassenbuch->fach_id = $lessonPlan->fach_id;
        $klassenbuch->klasse()->sync($lessonPlan->klassen->pluck("id"));
        $klassenbuch->raum()->sync($lessonPlan->raum->pluck("id"));
        $klassenbuch->lehrer()->sync($lessonPlan->dozent->pluck("id"));


        $start = null;
        $end = null;
        try {
            $start = Carbon::createFromTimestamp($request->input("start"));
            $end = Carbon::createFromTimestamp($request->input("end"));
            $duration = $start->diffInMinutes($end);
        } catch (\Exception $exception) {
            $start = new Carbon($lessonPlan->getStartDate());
            $end = new Carbon($lessonPlan->getEndDate());
            $duration = $start->diffInMinutes($end);
        }

        $klassenbuch->startDate = $start;
        $klassenbuch->endDate = $end;

        $members = $request->input("presence");
        foreach($members as $member)
        {
            $entry = KlassenbuchTeilnahme::where("eintrag_id", "=", $klassenbuch->id)->where("schuler_id", "=", $member["id"])->first();
            if($entry == null)
                $entry = new KlassenbuchTeilnahme;

            if(!array_key_exists("selected",$member)) {
                Log::warning("no selected choosen!");
                continue;
            }
            $selectedValue = $member["selected"];

            $fehlzeitTyp = FehlzeitTyp::find($selectedValue["id"]);
            $entry->fehlzeit_typ_id = $fehlzeitTyp->id;
            $entry->eintrag_id = $klassenbuch->id;
            $entry->schuler_id = $member["id"];
            $entry->notes =  array_key_exists("notes", $member) ? $member["notes"] : "";
            $entry->length = $duration * $fehlzeitTyp->percent;
            $entry->save();

            // falls nicht standard, lege eine vorgemerkte abwesenheit für den Tag an
            $vorgemerkt = KlassenbuchTeilnahmeVorgemerkt::where('schuler_id', '=', $member["id"])
                ->where('startDate', '<', $start)
                ->where('endDate', '>', $end)->first();

            // aktualisieren falls der Typ anders ist
            if($vorgemerkt != null && $vorgemerkt->fehlzeit_typ_id != $fehlzeitTyp->id)
            {
                $vorgemerkt->endDate = $start->format('Y-m-d H:i:s.v');
                $vorgemerkt->save();
            }
        }

        $klassenbuch->save();

        return parent::createJsonResponse("Classbook info", false, 200, ["classbook_entry" => $klassenbuch]);
    }

    public function klassenbuchMissingEntries(Request $request)
    {
        $start = \Carbon\Carbon::now()->subMonths(2);
        $end = Carbon::yesterday();
        $type = strtolower($request->get("type"));
        $id = $request->get("id");
        // if we have a fixed entwurf, everthing is fine.
        if ($request->has("entwurf_id")) {
            $entwurf = $request->get("entwurf_id");
            $entwurf = SchuljahrEntwurf::findOrFail($entwurf);

            $unterricht = UnterrichtController::getUnterrichtInternal($start, $end, $type, $id, $entwurf, false);
        } else {
            $unterricht = UnterrichtController::getUnterrichtWithoutEntwurf($start, $end, $id, $type, false);
        }
        $missing = [];

        foreach ($unterricht as $singleUnterricht)
        {
            if(($singleUnterricht["type"] == "lessonPlan"
                    || $singleUnterricht["type"] == "lesson")
                && !KlassenbuchEintrag::where('lesson_id', '=', $singleUnterricht["unique_id"])->exists()
            )
            {
                if($singleUnterricht["type"] == "lesson" && $singleUnterricht["lessonType"] == "ausfall")
                {
                    continue;
                }
                $missing[] = $singleUnterricht;
            }
        }
        return parent::createJsonResponse("Classbook information about missing entries", false, 200, ["missing_entries" => $missing]);
    }

    public function getEntriesByDate(Request $request)
    {
        $start = $request->start_unix > 0? Carbon::createFromTimestamp($request->start_unix) : \Carbon\Carbon::now()->subMonths(1);
        $end = $request->end_unix > 0? Carbon::createFromTimestamp($request->end_unix) : \Carbon\Carbon::now()->addDays(2);

        //cap to half a year max
        $daysDiff = $start->diffInDays($end);
        if( sqrt(pow($daysDiff,2)) > 182 )
        {
            return parent::createJsonResponse("Start- und Enddatum liegen zu weit auseinander. (180 Tage max)", true, 400);
        }

        $type = strtolower($request->get("type"));
        $id = $request->get("id");
        // if we have a fixed entwurf, everthing is fine.
        if ($request->has("entwurf_id")) {
            $entwurf = $request->get("entwurf_id");
            $entwurf = SchuljahrEntwurf::findOrFail($entwurf);

            $unterricht = UnterrichtController::getUnterrichtInternal($start, $end, $type, $id, $entwurf, false);
        } else {
            $unterricht = UnterrichtController::getUnterrichtWithoutEntwurf($start, $end, $id, $type, false);
        }
        $collection = [];

        foreach ($unterricht as $singleUnterricht) {
            if ($singleUnterricht["type"] == "lessonPlan" || $singleUnterricht["type"] == "lesson") {

                // Klassenbuch-Entry
                if($singleUnterricht["klassenbuch"] instanceof KlassenbuchEintrag) {
                    $singleUnterricht["classbook_formular"] = self::getClassbookFormularForEntry($singleUnterricht["klassenbuch"]);
                }

                // Anwesenheiten

                if(false) {
                    if ($singleUnterricht["type"] == "lessonPlan") {
                        $lessonPlan = LessonPlan::find($singleUnterricht["id"]);
                    } else {
                        $lessonPlan = Lesson::find($singleUnterricht["id"]);
                    }

                    if ($lessonPlan->draft == null) {
                        $c = LessonPlan::find($lessonPlan->getParentId());
                        $schoolYear = $c->draft->schuljahr;
                    } else {
                        $schoolYear = $lessonPlan->draft->schuljahr;
                    }

                    $datum = Carbon::parse($singleUnterricht["start"])->format("Y/m/d");
                    $start = Carbon::parse($singleUnterricht["start"])->timestamp;
                    $end = Carbon::parse($singleUnterricht["end"])->timestamp;
                    $singleUnterricht["attendances"] = self::getMembers($lessonPlan, $datum, $singleUnterricht["klassenbuch"], $schoolYear, $start, $end);

                } else {
                    // Short version
                    if($singleUnterricht["klassenbuch"] instanceof KlassenbuchEintrag) {
                        $klassenbuchTeilnahme = KlassenbuchTeilnahme::where('eintrag_id','=',$singleUnterricht["klassenbuch"]->id)->with("typ")->with("student")->get();
                        $singleUnterricht["attendances"] = $klassenbuchTeilnahme;
                    } else {
                        $singleUnterricht["attendances"] = [];
                    }
                }
                //
                $collection[] = $singleUnterricht;
            }
        }

        usort($collection,  Array($this,"sortByStart"));
        return parent::createJsonResponse("Classbook entries by date", false, 200, ["classbook_entries" => $collection]);

    }


    function sortByStart($a, $b) {
        return strtotime($a['start']) < strtotime($b['start']);
    }


    public function filterClassbook(Request $request)
    {
        /**
         * $request->filter_object:
         *
        course	455
        from	1635721200
        keyword	"rewr"
        school	1
        teacher	764
        until	1636844400
         *
         */

        $obj = $request->filter_object;


    }

    private static function getClassbookEntryForUniqueID($uniqueId, $schoolYear)
    {
        $klassenbuch = KlassenbuchEintrag::where('lesson_id', '=', $uniqueId)->first();
        return self::getClassbookFormularForEntry($klassenbuch, $schoolYear);
    }

    private static function getClassbookFormularForEntry($klassenbuch, $schoolYear = null) {
        if($klassenbuch == null && $schoolYear != null)
        {
            $formularRevision = $schoolYear->getEinstellungenFormular("unterricht_formular");
            if($formularRevision == null) {
                return ["classbook_entry" => null, "form_data" => null, "form" => null, "form_revision_id" => -1];
            }
            return ["classbook_entry" => null, "form_data" => null, "form" => $formularRevision, "form_revision_id" => $formularRevision->id];
        }
        $aktivitaet = \Spatie\Activitylog\Models\Activity::where('subject_id', '=', $klassenbuch->id)->where('subject_type', '=', 'App\KlassenbuchEintrag')->orderBy("created_at","DESC")->first();
        $formularRevision = FormularRevision::find($klassenbuch->formular_revision_id);
        if ($formularRevision != null) {
            return ["lastActivity" => [ "change" => $aktivitaet, "causer" => ActivitiesWidget::getCauserDisplay($aktivitaet->causer) ], "classbook_entry" =>  $klassenbuch, "form_data" => json_decode($klassenbuch->formular_data), "form" => $formularRevision, "form_revision_id" => $formularRevision->id];
        } else if($schoolYear != null) {
            // laden wir einfach den standard wieder
            $formularRevision = $schoolYear->getEinstellungenFormular("unterricht_formular");
            if($formularRevision != null)
            {
                return ["lastActivity" => [ "change" => $aktivitaet, "causer" => ActivitiesWidget::getCauserDisplay($aktivitaet->causer) ], "classbook_entry" => $klassenbuch, "form" => $formularRevision, "form_revision_id" => $formularRevision->id, "form_data" => json_decode($klassenbuch->formular_data)];
            }
        }
        return ["classbook_entry" => $klassenbuch, "lastActivity" => [ "change" => $aktivitaet, "causer" => ActivitiesWidget::getCauserDisplay($aktivitaet->causer) ]];
    }

}


