<?php

namespace App\Http\Controllers\API\V1\Template\Templates;

use App\StudyProgressEntry;
use Carbon\Carbon;

class IBAExMaStandardTemplate extends AbstractTemplate
{

    function templateFile()
    {
        return "studienbescheinigung_exma.docx";
    }

    function executeTemplateGeneration($templateProcessor, $model)
    {
        $addInfo = $model->getAddInfo();
        $templateProcessor->setValue('date_now', date("d.m.Y"));
        $templateProcessor->setValue('firstname', htmlspecialchars($model->firstname));
        $templateProcessor->setValue('lastname', htmlspecialchars($model->lastname));
        $templateProcessor->setValue('anrede', $addInfo->anrede == "na" ? "" : ($addInfo->anrede == "herr" ? "Herr " : "Frau "));
        $templateProcessor->setValue('mnr', $addInfo->personalnummer);
        $templateProcessor->setValue('birthdate', Carbon::parse($addInfo->birthdate)->format("d.m.Y"));
        $templateProcessor->setValue('gender', $addInfo->gender == "female" ? "sie hat ihren" : ($addInfo->gender == "male" ? "er hat seinen" : "sie/er hat ihren/seinen"));
        $templateProcessor->setValue('studierende', $addInfo->gender == "female" ? "Studierende " : ($addInfo->gender == "male" ? "Studierender " : "Studierende/Studierender "));

        $schule = $model->schulen->first();
        $schuljahr = $schule->getCurrentSchoolYear();
        $first = $model->getFirstStudyInformation();
        $last = $model->getLastStudyInformation();
        $currentStudy = $model->getCurrentStudyInformation($schuljahr->id);
        if($currentStudy == null)
            $currentStudy = $last;
        $templateProcessor->setValue('schule_name', $schule->name);
        $templateProcessor->setValue('semester', str_replace("SS","Sommersemester 20",str_replace("WS","Wintersemester 20", $schuljahr->name)));
        $templateProcessor->setValue('semester_start', Carbon::parse($schuljahr->start)->format("d.m.Y"));
        $templateProcessor->setValue('semester_ende', Carbon::parse($schuljahr->ende)->format("d.m.Y"));
        $templateProcessor->setValue('immadatum', Carbon::parse($first->schuljahr->start)->format("d.m.Y"));
        $study_end = null;
        if($last != null) {
            $study_end = Carbon::parse($last->schuljahr->ende);
            $attribute = json_decode($last->study_attributes);
            if(is_object($attribute) && property_exists($attribute,"date_of_exmatrikulation"))
                $study_end = Carbon::parse($attribute->date_of_exmatrikulation);
            if(is_object($attribute) && property_exists($attribute,"cancel_date"))
                $study_end = Carbon::parse($attribute->cancel_date);
            if(is_object($attribute) && property_exists($attribute,"date_of_cancel"))
                $study_end = Carbon::parse($attribute->date_of_cancel);
            if(is_object($attribute) && property_exists($attribute,"extendUntil"))
                $study_end = Carbon::parse($attribute->extendUntil);
            if(is_object($attribute) && property_exists($attribute,"finish_date"))
                $study_end = Carbon::parse($attribute->finish_date);
        }
        $templateProcessor->setValue('exmadatum', $study_end == null || $study_end->isAfter(Carbon::parse($schuljahr->ende)) ? Carbon::parse($schuljahr->ende)->format("d.m.Y") : $study_end->format("d.m.Y"));
        $templateProcessor->setValue('studiengang', htmlspecialchars($currentStudy->studium() ? $currentStudy->studium()->name : "Kein Studiengang"));
        $templateProcessor->setValue('regelzeit',$currentStudy->studium()  ? $currentStudy->studium()->normal_period : "-");
        $direction_short = [];
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
        if(count($direction_short) > 0)
            $templateProcessor->setValue('direction_of_study', join(", ",$direction_short)); // $currentStudy->direction_of_study->name);
        else
            $templateProcessor->setValue('direction_of_study',"-");

        $templateProcessor->setValue('us', StudyProgressEntry::where("schuler_id","=",$model->id)->where("status","=","vacation")->count());
        $templateProcessor->setValue('fs', $currentStudy->fs );
        $templateProcessor->setValue('iba_semester', $currentStudy->hs );
    }

    function documentName()
    {
        return "studienbescheinigung";
    }
}
