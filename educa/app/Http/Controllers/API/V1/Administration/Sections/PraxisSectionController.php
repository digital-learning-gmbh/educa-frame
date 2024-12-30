<?php

namespace App\Http\Controllers\API\V1\Administration\Sections;

use App\Http\Controllers\API\ApiController;
use App\Klasse;
use App\LessonSection;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PraxisSectionController extends ApiController
{
    //

    public function dataset(Request $request)
    {
        $schoolClasses = Klasse::findOrFail(explode(",", $request->input("schoolClasses")));

        $data = [];
        $groups = [];
        $start = null;
        $end = null;
        $singleMode = $request->has("section") && $request->input("section") != "-1";
        foreach ($schoolClasses as $schoolClass) {

            $hasSection = false;

            foreach ($schoolClass->lehrabschnitte as $abschnitt) {
                // Skip if we are in single mode
                if ($singleMode && $request->input("section") != $abschnitt->id)
                    continue;

                $hasSection = true;
                // add section as background event
                $backgroundAbschnitt = [];
                $backgroundAbschnitt["id"] = "abschnitt_" . $abschnitt->id;
                $backgroundAbschnitt["type"] = "background";
                $backgroundAbschnitt["group"] = $schoolClass->id;
                $backgroundAbschnitt["className"] = $abschnitt->type == "praxis" ? "bg-primary text-white" : "bg-secondary text-white sectionbackground";
                $backgroundAbschnitt["content"] = $abschnitt->name;
                $backgroundAbschnitt["start"] = Carbon::parse($abschnitt->begin)->format("Y-m-d");
                $backgroundAbschnitt["end"] = Carbon::parse($abschnitt->end)->format("Y-m-d");
                $data[] = $backgroundAbschnitt;


                if ($abschnitt->type != "praxis") {
                    foreach ($schoolClass->schulerAktuell()->get() as $schuler) {
                        $backgroundAbschnitt = [];
                        $backgroundAbschnitt["id"] = "abschnitt_" . $abschnitt->id . "_" . $schuler->id;
                        $backgroundAbschnitt["type"] = "background";
                        $backgroundAbschnitt["group"] = $schoolClass->id . "_" . $schuler->id;
                        $backgroundAbschnitt["className"] = $abschnitt->type == "praxis" ? "bg-primary text-white sectionbackground" : "bg-secondary text-white sectionbackground";
                        $backgroundAbschnitt["start"] = Carbon::parse($abschnitt->begin)->format("Y-m-d");
                        $backgroundAbschnitt["end"] = Carbon::parse($abschnitt->end)->format("Y-m-d");

                        $data[] = $backgroundAbschnitt;
                    }
                }

                // Calculate frame
                if ($start == null || $start->isAfter(Carbon::parse($abschnitt->begin)))
                    $start = Carbon::parse($abschnitt->begin);

                if ($end == null || $end->isBefore(Carbon::parse($abschnitt->end)))
                    $end = Carbon::parse($abschnitt->end);

                if ($abschnitt->type == "praxis") {
                    foreach ($abschnitt->praxisEinsatze as $einsatz) {
                        $item = [];
                        $item["start"] = date("Y/m/d H:i:s", strtotime($einsatz->startDate));
                        $item["end"] = date("Y/m/d H:i:s", strtotime($einsatz->endDate));
                        $item["group"] = $schoolClass->id . "_" . $einsatz->schuler_id;
                        $item["className"] = $einsatz->id;

                        if ($einsatz->module() != null) {
                            $item["className"] .= " modul" . $einsatz->module()->id;
                        }

                        if ($einsatz->unternehmen_id != null) {
                            $item["content"] = $einsatz->unternehmen->name;
                        } else if ($einsatz->module() != null) {
                            $item["content"] = $einsatz->module()->name;
                        } else {
                            $item["content"] = "Praxiseinsatz";
                        }

                        // check if we have a Besuch
                        if ($einsatz->besuchstermine->count() > 0) {
                            $item["content"] = "<i class='fas fa-eye'></i> " . $item["content"];
                        }


                        $item["id"] = $einsatz->id;
                        $data[] = $item;
                    }
                }
            }

            if ($hasSection) {
                // we should at the groups :)
                $studentIds = [];
                foreach ($schoolClass->schulerAktuell()->get() as $schuler) {
                    $student = [];
                    $student["content"] = $schuler->lastname . ", " . $schuler->firstname;
                    $student["id"] = $schoolClass->id . "_" . $schuler->id;
                    $studentIds[] = $student["id"];
                    $student["value"] = $schuler->id;
                    $student["treeLevel"] = 2;
                    $student["className"] = "group_" . $schuler->id;
                    $groups[] = $student;
                }

                $schoolClassGroup = [];
                $schoolClassGroup["content"] = $schoolClass->name;
                $schoolClassGroup["id"] = $schoolClass->id;
                $schoolClassGroup["treeLevel"] = 1;
                $schoolClassGroup["nestedGroups"] = $studentIds;
                $groups[] = $schoolClassGroup;
            }
        }

        if ($start == null)
            $start = Carbon::now();

        if ($end == null)
            $end = Carbon::now();

        return parent::createJsonResponse("dataset of the part", false, 200, ["start" => $start, "end" => $end, "dataset" => $data, "groups" => $groups]);

    }

}
