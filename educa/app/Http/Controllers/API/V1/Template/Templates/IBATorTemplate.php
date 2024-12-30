<?php

namespace App\Http\Controllers\API\V1\Template\Templates;

use App\Module;
use App\Note;
use App\NotenCache;
use App\Schuljahr;
use App\StudyProgressEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IBATorTemplate extends AbstractTemplate
{

    function templateFile()
    {
        return "tor.docx";
    }

    function executeTemplateGeneration($templateProcessor, $model)
    {
        $addInfo = $model->getAddInfo();
        $templateProcessor->setValue('date_now', date("d.m.Y"));
        $templateProcessor->setValue('firstname', $model->firstname);
        $templateProcessor->setValue('lastname', $model->lastname);
        $templateProcessor->setValue('anrede', $addInfo->anrede == "na" ? "" : ($addInfo->anrede == "herr" ? "Herr " : "Frau "));
        $templateProcessor->setValue('mnr', $addInfo->personalnummer);
        $templateProcessor->setValue('street', htmlspecialchars($addInfo->street));
        $templateProcessor->setValue('city', htmlspecialchars($addInfo->city));
        $templateProcessor->setValue('plz', htmlspecialchars($addInfo->plz));
        $templateProcessor->setValue('birthdate', Carbon::parse($addInfo->birthdate)->format("d.m.Y"));

        $schule = $model->schulen->first();
        $schuljahr = $schule->getCurrentSchoolYear();
        $first = $model->getFirstStudyInformation();
        $currentStudy = $model->getCurrentStudyInformation($schuljahr->id);
        if ($currentStudy == null) {
            $currentStudy = $model->getLastStudyInformation();
        }
        $templateProcessor->setValue('schule_name', $schule->name);
        $templateProcessor->setValue('semester', str_replace("SS", "Sommersemester 20", str_replace("WS", "Wintersemester 20", $schuljahr->name)));
        $templateProcessor->setValue('semester_start', Carbon::parse($schuljahr->start)->format("d.m.Y"));
        $templateProcessor->setValue('semester_ende', Carbon::parse($schuljahr->ende)->format("d.m.Y"));
        $templateProcessor->setValue('begin', Carbon::parse($first->schuljahr->start)->format("d.m.Y"));
        $templateProcessor->setValue('studiengang', htmlspecialchars($currentStudy->studium()->name));
        $direction_short = [];
        $directions = $currentStudy->direction_of_study;
        foreach ($directions as $direction) {
            foreach ($direction["selected"] as $einheit) {
                $direction_short[] = $einheit->name;
//                $additional_attributes = json_decode($einheit->additional_attributes);
//                if ($additional_attributes != null && property_exists($additional_attributes, "shortcut"))
//                    $direction_short[] = $additional_attributes->shortcut;
//                else
            }
        }
        if(count($direction_short) > 0)
            $templateProcessor->setValue('direction_of_study', "Fachvertiefung: ".htmlspecialchars(join(", ",$direction_short)));
        else
            $templateProcessor->setValue('direction_of_study',"");

        $templateProcessor->setValue('us', StudyProgressEntry::where("schuler_id","=",$model->id)->where("status","=","vacation")->count());
        $templateProcessor->setValue('fs', $currentStudy->fs);
        $templateProcessor->setValue('iba_semester', $currentStudy->hs);
        $templateProcessor->setValue('wahlmodul', "");

        // load noten



        $noten =  DB::table("notes")->where('schuler_id', '=', $model->id)->whereIn('model_type', ["modul"])
            ->where("note", ">=", 1)->where("note", "<", 6)
            ->where("non_curricular", "=", 0)
            ->where("consider_current_curriculum","=",1)
            ->where("attest", "=", 0)->where("status", "=", "public")
        ->groupBy(["model_type", "model_id"])
            ->get([DB::raw('MAX(version) as version'),"model_type","model_id"]);


        $arrayToPrint = [];
        foreach ($noten as $subag) {
            $note =   Note::with("schuljahr")->where("schuler_id","=",$model->id)
                ->where("model_type","=",$subag->model_type)->where("model_id","=",$subag->model_id)->where("version","=",$subag->version)
                ->where("non_curricular", "=", 0)
                ->where("consider_current_curriculum","=",1)
                ->where("attest", "=", 0)->where("status", "=", "public")

                ->first();
            $note->append("belongsObject");
            $fs = "-";
            if ($note->belongsObject instanceof Module) {
                $resultFs = DB::table("modul_fach_curiculum")->where("lehrplan_id", "=", $currentStudy->lehrplan()->id)->where("module_id", "=", $note->belongsObject->id)->orderBy("semester_occurrence", "DESC")->first();
                if ($resultFs != null) {
                    $fs = $resultFs->semester_occurrence;
                } else {
                    continue;
                }
            }
            $noteDisplay = number_format($note->note,1);
            if($note->points < 0)
            {
                $noteDisplay = $note->note == 1 ? "bestanden" : "nicht bestanden";
                if($note->note != 1)
                    continue;
            }
            $arrayToPrint[] = ["fs_modul" => $fs, "modulId" => $note->belongsObject->examination_number, "modulName" => htmlspecialchars($note->belongsObject->name), "ects" => $note->note == 5 ? "0" : $note->belongsObject->ects, "note" => $noteDisplay];
        }

        $templateProcessor->cloneRowAndSetValues('modulId', $arrayToPrint);

        // load cache cow

        $schuljahr_ids = Schuljahr::where("start","<=",Carbon::now())->where("ende",">=",Carbon::now())->pluck("id");
        $lastNotenCache = NotenCache::where("schuler_id","=",$model->id)->whereIn("schuljahr_id",$schuljahr_ids)->first();
        if($lastNotenCache == null)
            $lastNotenCache = NotenCache::where("schuler_id","=",$model->id)->orderBy("ects_sum","DESC")->first();

        if ($lastNotenCache != null) {
            $templateProcessor->setValue('note_schnitt', $lastNotenCache->note_schnitt);
            $templateProcessor->setValue('ects_all', $lastNotenCache->ects_sum);
            $templateProcessor->setValue('ects_cal', $lastNotenCache->ects_cal);
            $templateProcessor->setValue('gesamt_ects', $lastNotenCache->ects_max);
            $templateProcessor->setValue('date_of_calculation', Carbon::parse($lastNotenCache->updated_at)->format("d.m.Y"));

        }
    }

    function documentName()
    {
        return "tor";
    }
}
