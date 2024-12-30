<?php


namespace App\Http\Controllers\API\V1\Administration\ExamAdministration;


use App\Module;
use App\ModulExam;
use App\ModulPartExam;
use App\StudyProgressEntry;
use Illuminate\Support\Facades\DB;

class HelperCalculation
{
    public static function calculateOpenModuls($lehrplan_id, $fs)
    {
        $relevantLehrplanEinheiten = DB::select("SELECT einheit.module_id as m_id FROM stupla_lehrplan_einheits as einheit, stupla_modul_fach_curiculum as fachRelation WHERE " .
            "einheit.module_id = fachRelation.module_id AND einheit.lehrplan_id = fachRelation.lehrplan_id AND einheit.lehrplan_id = :lehrplan_id AND fachRelation.semester_occurrence = :fs",
            ['lehrplan_id' => $lehrplan_id,  "fs" => $fs]);

        //print_r(array_column($relevantLehrplanEinheiten, 'm_id'));
        // TODO alle Module abziehen, wo er schon eine Prüfung gemacht hat

        return Module::find(array_column($relevantLehrplanEinheiten, 'm_id'));
    }

    public static function calculateOpenExamParts(Module $modul, $lehrplan_id, $fs, ModulExam $modulExam)
    {
        $relevantModulExamParts = DB::select("SELECT part_exam.id as part_exam_id FROM".
            " stupla_modul_fach_curiculum as fachRelation, stupla_modul_part_exam_fach as examFach, stupla_modul_part_exams as part_exam ".
            "WHERE examFach.fach_id = fachRelation.fach_id AND fachRelation.lehrplan_id = :lehrplan_id ".
            "AND fachRelation.semester_occurrence = :fs AND fachRelation.module_id = :modul_id AND ".
            "examFach.modul_part_exam_id = part_exam.id AND part_exam.modul_exam_id = :exam_id",
            ['lehrplan_id' => $lehrplan_id,  "fs" => $fs, "modul_id" => $modul->id ,"exam_id" => $modulExam->id]);


        return ModulPartExam::find(array_column($relevantModulExamParts, 'part_exam_id'));
    }
}
