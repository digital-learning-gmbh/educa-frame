<?php

namespace App\Http\Controllers\API\V1\Template\Templates;

use App\StudyProgressEntry;
use Carbon\Carbon;

class IBAStudienbescheinigungBafoegTemplate extends AbstractTemplate
{

    function templateFile()
    {
        return "studienbescheinigung_bafoeg.docx";
    }

    function executeTemplateGeneration($templateProcessor, $model)
    {
        $addInfo = $model->getAddInfo();
        $templateProcessor->setValue('date_now', date("d.m.Y"));
        $templateProcessor->setValue('firstname', $model->firstname);
        $templateProcessor->setValue('lastname', $model->lastname);
        $templateProcessor->setValue('anrede', $addInfo->anrede == "na" ? "" : ($addInfo->anrede == "herr" ? "Herr " : "Frau "));
        $templateProcessor->setValue('studierende', $addInfo->gender == "female" ? "ordentliche Studierende " : ($addInfo->gender == "male" ? "ordentlicher Studierender " : "ordentliche/r Studierende/Studierender "));
        $templateProcessor->setValue('mnr', $addInfo->personalnummer);
        $templateProcessor->setValue('street', $addInfo->street);
        $templateProcessor->setValue('city', $addInfo->city);
        $templateProcessor->setValue('plz', $addInfo->plz);
        $templateProcessor->setValue('birthdate', Carbon::parse($addInfo->birthdate)->format("d.m.Y"));

        $schule = $model->schulen->first();
        $schuljahr = $schule->getCurrentSchoolYear();
        $first = $model->getFirstStudyInformation();
        $currentStudy = $model->getCurrentStudyInformation($schuljahr->id);
        $templateProcessor->setValue('schule_name', $schule->name);
        $templateProcessor->setValue('semester', str_replace("SS","Sommersemester 20",str_replace("WS","Wintersemester 20", $schuljahr->name)));
        $templateProcessor->setValue('semester_start', Carbon::parse($schuljahr->start)->format("d.m.Y"));
        $templateProcessor->setValue('semester_ende', Carbon::parse($schuljahr->ende)->format("d.m.Y"));
        $templateProcessor->setValue('begin', Carbon::parse($first->schuljahr->start)->format("d.m.Y"));
        $templateProcessor->setValue('studiengang', htmlspecialchars($currentStudy->studium()->name));
        $templateProcessor->setValue('regelzeit',$currentStudy->studium()->normal_period);
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
            $templateProcessor->setValue('direction_of_study', htmlspecialchars(join(", ",$direction_short))); // $currentStudy->direction_of_study->name);
        else
            $templateProcessor->setValue('direction_of_study',"-");

        $templateProcessor->setValue('us', StudyProgressEntry::where("schuler_id","=",$model->id)->where("status","=","vacation")->count());
        $templateProcessor->setValue('fs', $currentStudy->fs );
        $templateProcessor->setValue('iba_semester', $currentStudy->hs );
    }

    function documentName()
    {
        return "studienbescheinigung_bafoeg";
    }
}
