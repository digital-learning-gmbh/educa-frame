<?php

namespace App\Http\Controllers\API\V1\Template\Templates;

use App\Lehrer;
use App\Module;
use App\Note;
use App\NotenCache;
use App\Schuler;
use App\Schuljahr;
use App\StudyProgressEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IBAVordruckNoten extends AbstractTemplate
{
    protected $mnr;

    public function __construct($mnr = true)
    {
        $this->mnr = $mnr;
    }

    function templateFile()
    {
        return $this->mnr ? "noten_eingabe_mnr.docx" : "noten_eingabe_name.docx";
    }

    function executeTemplateGeneration($templateProcessor, $model)
    {
        $modulExecution = $model->examExecution;
        $schuljahr = $modulExecution->klasse->schuljahr;
        $j = 1;
        $templateProcessor->cloneBlock('whole_document', $model->getExamPartsAttribute()->count(), true, true);

        foreach ($model->getExamPartsAttribute() as $part) {
            $t = Lehrer::find(DB::table("lehrer_exam_execution_date")->where("exam_execution_date_id","=",$model->id)->where("part_exam_id","=",$part->id)->first()->lehrer_id);
            if($t != null)
            {
                $templateProcessor->setValue('dozent#'.$j, htmlspecialchars($t->displayName));
                $templateProcessor->setValue('street#'.$j, htmlspecialchars($t->getAddInfo()->street));
                $templateProcessor->setValue('plz#'.$j, htmlspecialchars($t->getAddInfo()->plz));
                $templateProcessor->setValue('city#'.$j, htmlspecialchars($t->getAddInfo()->city));
            }
            $templateProcessor->setValue('time#'.$j, htmlspecialchars(Carbon::parse($model->getStartDate())->format("H:i") . " - " . Carbon::parse($model->getEndDate())->format("H:i")));
            $templateProcessor->setValue('date#'.$j, htmlspecialchars(Carbon::parse($model->getStartDate())->format("d.m.Y")));
            $templateProcessor->setValue('semester#'.$j, str_replace("SS", "Sommersemester 20", str_replace("WS", "Wintersemester 20", $schuljahr->name)));
            $templateProcessor->setValue('planungsgruppe#'.$j, htmlspecialchars($modulExecution->klasse->name));
            $templateProcessor->setValue('fach_name#'.$j, htmlspecialchars($part->subjects->pluck("name")->join(", ")));
            $templateProcessor->setValue('aufsicht', htmlspecialchars($model->supervision));
            $templateProcessor->setValue('rooms#'.$j, htmlspecialchars($model->raumname()));
            $templateProcessor->setValue('modul#'.$j, htmlspecialchars($modulExecution->modulExam->modul->name));
            $templateProcessor->setValue('maxPoints#'.$j, htmlspecialchars($part->maxPoints));



            $arrayToPrint = [];
            $i = 1;
            foreach ($modulExecution->students as $schuler) {
                $arrayToPrint[] = ["n#".$j => $i, "mnr#".$j => $schuler->getAddInfo()->personalnummer, "student_name#".$j => htmlspecialchars($schuler->displayName), "studienort#".$j => htmlspecialchars($schuler->schulen()->first()->name)];
                $i++;
            }

            // sort
            if ($this->mnr) {
                usort($arrayToPrint, function ($item1, $item2) use($j) {
                    return $item1['mnr#'.$j] <=> $item2['mnr#'.$j];
                });
            } else {
                usort($arrayToPrint, function ($item1, $item2) use($j) {
                    return $item1['student_name#'.$j] <=> $item2['student_name#'.$j];
                });
            }

            $i = 1;
            $z = [];
            foreach ($arrayToPrint as $item) {
                $item['n#'.$j] = $i;
                $i++;
                $z[] = $item;
            }

            $templateProcessor->cloneRowAndSetValues('n#'.$j, $z);
            $j++;
        }
    }

    function documentName()
    {
        return "anwesenheit";
    }
}
