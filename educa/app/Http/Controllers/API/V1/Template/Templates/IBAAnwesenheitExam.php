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

class IBAAnwesenheitExam extends AbstractTemplate
{
    protected $mnr;

    public function __construct($mnr = true)
    {
        $this->mnr = $mnr;
    }

    function templateFile()
    {
        return $this->mnr ? "exam_anwesenheit_mnr.docx" : "exam_anwesenheit_name.docx";
    }

    function executeTemplateGeneration($templateProcessor, $model)
    {
        $modulExecution = $model->examExecution;
        $schuljahr = $modulExecution->klasse->schuljahr;
        $templateProcessor->setValue('time', htmlspecialchars(Carbon::parse($model->getStartDate())->format("H:i")." - ".Carbon::parse($model->getEndDate())->format("H:i")));
        $templateProcessor->setValue('date',htmlspecialchars(Carbon::parse($model->getStartDate())->format("d.m.Y")));
        $templateProcessor->setValue('semester', str_replace("SS", "Sommersemester 20", str_replace("WS", "Wintersemester 20", $schuljahr->name)));
        $templateProcessor->setValue('planungsgruppe', htmlspecialchars($modulExecution->klasse->name));
        $templateProcessor->setValue('fach_name', htmlspecialchars($model->getExamPartsAttribute()->pluck("subjects.name")->join(", ")));
        $templateProcessor->setValue('aufsicht', htmlspecialchars($model->supervision));
        $templateProcessor->setValue('rooms', htmlspecialchars($model->raumname()));

        $arrayToPrint = [];
        $i = 1;
        foreach ($modulExecution->students as $schuler)
        {
            $arrayToPrint[] = ["n" => $i, "mnr" => $schuler->getAddInfo()->personalnummer, "student_name" => htmlspecialchars($schuler->displayName) ];
            $i++;
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
