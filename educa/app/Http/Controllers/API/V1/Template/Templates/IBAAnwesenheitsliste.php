<?php

namespace App\Http\Controllers\API\V1\Template\Templates;

use App\Module;
use App\Note;
use App\NotenCache;
use App\Schuler;
use App\Schuljahr;
use App\StudyProgressEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IBAAnwesenheitsliste extends AbstractTemplate
{

    function templateFile()
    {
        return "anwesenheitsliste.docx";
    }

    function executeTemplateGeneration($templateProcessor, $model)
    {
        if($model["lessonPlan"] == null || $model["date"] == null)
            return;

        $schuljahr = $model["lessonPlan"]->draft->schuljahr;
        $templateProcessor->setValue('date_of_lesson', $model["date"]->format("d.m.Y"));
        $templateProcessor->setValue('longname', htmlspecialchars($model["lessonPlan"]->fachname()));
        $templateProcessor->setValue('shortname',htmlspecialchars($model["lessonPlan"]->fachAbk()));
        $templateProcessor->setValue('term', str_replace("SS", "Sommersemester 20", str_replace("WS", "Wintersemester 20", $schuljahr->name)));
        $templateProcessor->setValue('dozenten', htmlspecialchars($model["lessonPlan"]->dozentname()));
        $templateProcessor->setValue('planning_groups', htmlspecialchars($model["lessonPlan"]->klassen()->pluck("name")->join(", ")));
        $templateProcessor->setValue('raume', htmlspecialchars($model["lessonPlan"]->raumname()));

        $klassen = $model["lessonPlan"]->klassen;
        foreach ($klassen as $klasse)
        {
            $lehrplanEinheiten = array(); // iba different LehrplanEinheit::where('fach_id', $lessonPlan->fach_id)->whereIn('lehrplan_id',$klasse->getLehrplan->pluck("id")->toArray())->get();
            if(count($lehrplanEinheiten) == 0)
            {
                if ($klasse->type == "cluster_group") {
                    foreach ($klasse->klassen as $klasseMember) {
                        foreach ($klasseMember->schulerAtDatum($model["date"])->orderBy('lastname')->get() as $schuler) {
                            $studentsIds[] = $schuler->id;
                        }
                    }
                } else {
                    foreach ($klasse->schulerAtDatum($model["date"])->orderBy('lastname')->get() as $schuler) {
                        $studentsIds[] = $schuler->id;
                    }
                }
            }
        }

        $schulers = Schuler::find($studentsIds);

        $i = 1;
        $arrayToPrint = [];
        foreach ($schulers as $schuler)
        {
            $fachrichtung = "";
            $schule = $schuler->schulen->first();
            $schuljahr_schuler = $schule->getCurrentSchoolYear();
            $currentStudy = $schuler->getCurrentStudyInformation($schuljahr_schuler->id);
            $direction_short = [];
            if($currentStudy != null) {
                $directions = $currentStudy->direction_of_study;
                foreach ($directions as $direction) {
                    foreach ($direction["selected"] as $einheit) {
                        $additional_attributes = json_decode($einheit->additional_attributes);
                        if ($additional_attributes != null && property_exists($additional_attributes, "shortcut"))
                            $direction_short[] = $additional_attributes->shortcut;
                        else
                            $direction_short[] = $einheit->name;
                    }
                }
                if (count($direction_short) > 0)
                    $fachrichtung = join(", ", $direction_short);
            }
            $arrayToPrint[] = [ "n" => $i, "fachrichtungen" => htmlspecialchars($fachrichtung), "displayName" => htmlspecialchars($schuler->displayName), "studienort" => $schule != null ? $schule->name : "" ];
            $i++;
        }


        usort($arrayToPrint, function ($item1, $item2) {
            return $item1['displayName'] <=> $item2['displayName'];
        });

        $i = 1;
        $z = [];
        foreach ($arrayToPrint as $item)
        {
            $item["n"] = $i;
            $i++;
            $z[] = $item;
        }

        $templateProcessor->cloneRowAndSetValues('n', $z);
    }

    function documentName()
    {
        return "anwesenheit";
    }
}
