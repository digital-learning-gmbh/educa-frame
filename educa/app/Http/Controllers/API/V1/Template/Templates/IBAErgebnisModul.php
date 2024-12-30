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

class IBAErgebnisModul extends AbstractTemplate
{
    protected $mnr;

    public function __construct($mnr = true)
    {
        $this->mnr = $mnr;
    }

    function templateFile()
    {
        return $this->mnr ? "noten_ergebnisse_mnr.docx" : "noten_ergebnisse_name.docx";
    }

    function executeTemplateGeneration($templateProcessor, $model)
    {
        $schuljahr = $model->klasse->schuljahr;
        $templateProcessor->setValue('semester', str_replace("SS", "Sommersemester 20", str_replace("WS", "Wintersemester 20", $schuljahr->name)));
        $templateProcessor->setValue('planungsgruppe', htmlspecialchars($model->klasse->name));
        $templateProcessor->setValue('modul', htmlspecialchars($model->modulExam->modul->name));

        $arrayToPrint = [];
        $i = 1;
        foreach ($model->students as $schuler)
        {
            $note = Note::where("exam_execution_id","=",$model->id)->where("model_type","=","modul")->where("schuler_id","=",$schuler->id)->orderBy("version","DESC")->first();
            if($note != null && $note->attest)
            {
                $note->modul_note = "Attest";
                $note->modul_punkte = "-";
            }
            if($note != null) {
                $arrayToPrint[] = ["n" => $i, "mnr" => $schuler->getAddInfo()->personalnummer, "student_name" => htmlspecialchars($schuler->displayName), "note" => $note->note < 0 ? "-" : $note->note, "points" => $note->points < 0 ? "-" : $note->points, "version" => $note->version, "bemerkung" => $note->bemerkung];
            } else {
                $arrayToPrint[] = ["n" => $i, "mnr" => $schuler->getAddInfo()->personalnummer, "student_name" => htmlspecialchars($schuler->displayName),  "note" => "", "points" => "", "version" => "","bemerkung" => ""];
            }$i++;
        }

        // sort
        if($this->mnr)
        {
            usort($arrayToPrint, function ($item1, $item2) {
                return $item1['mnr'] <=> $item2['mnr'];
            });
        } else {
            usort($arrayToPrint, function ($item1, $item2) {
                return $item1['student_name'] <=> $item2['student_name'];
            });
        }

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
