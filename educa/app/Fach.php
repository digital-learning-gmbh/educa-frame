<?php

namespace App;

use App\ClingoModels\ClingoMapper;
use App\Http\Controllers\Shared\ClingoConstant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Fach extends Model implements ClingoMapper
{
    CONST IDENTIFIER = "fach";

    protected $appends = ["shortName", "extended_display"];

    public function loadFromClingo($string)
    {
        // TODO: Implement loadFromClingo() method.
    }

    public function getClingo( $withRelationship )
    {
        $string = ClingoConstant::basicClingoTranslate($this::IDENTIFIER,[$this->getClingoID()]);
        if( $withRelationship )
        {
            $string .= ClingoConstant::basicClingoTranslate("hoursStudentUnit",[$this->duration, $this->getClingoID()]);
            $string .= ClingoConstant::basicClingoTranslate("hoursUnit",[$this->duration, $this->getClingoID()]);
        }
        return $string;
    }

    public function lehrer()
    {
        return $this->belongsToMany("App\Lehrer","lehrer_fach");
    }

    public function lehrplanEinheit()
    {
        return $this->hasMany("App\LehrplanEinheit");
    }

    public function getClingoID()
    {
        return Fach::IDENTIFIER ."_". $this->id;
    }

    public static function findByClingoId($clingo)
    {
        return Fach::findOrFail(str_replace(Fach::IDENTIFIER."_","",$clingo));
    }

    public function getShortNameAttribute()
    {
        if($this->abk != "")
            return $this->abk;
        return $this->name;
    }

    public function makeDummyModule()
    {
        if($this->dummyModule() == null)
        {
            $module = new Module;
            $module->name = $this->name;
            $module->examination_number = "dummy-".str_random(6);
            $module->save();
            $this->dummy_module_id = $module->id;
            $this->save();
        }
    }

    public function dummyModule()
    {
        return Module::find($this->dummy_module_id);
    }

    public function studies() // Studiums
    {
        return $this->belongsToMany("App\Studium", "fach_studium", "fach_id","studium_id");
    }

    public function loadSemesterInformation($module_id, $lehrplan_id)
    {
        if(!DB::table('modul_fach_curiculum')->where([
            'lehrplan_id' => $lehrplan_id,
            'module_id' => $module_id,
            'fach_id' => $this->id
        ])->exists())
        {
            $this->saveSemesterInformation($module_id,$lehrplan_id,1);
            return 1;
        }
        return DB::table('modul_fach_curiculum')->where([
            'lehrplan_id' => $lehrplan_id,
            'module_id' => $module_id,
            'fach_id' => $this->id
        ])->first()->semester_occurrence;
    }

    public function saveSemesterInformation($module_id, $lehrplan_id, $semester)
    {
        DB::table('modul_fach_curiculum')->where([
            'lehrplan_id' => $lehrplan_id,
            'module_id' => $module_id,
            'fach_id' => $this->id
        ])->delete();

        DB::table('modul_fach_curiculum')->insert([
            'lehrplan_id' => $lehrplan_id,
            'module_id' => $module_id,
            'fach_id' => $this->id,
            'semester_occurrence' => $semester
        ]);
    }

    public function getExtendedDisplayAttribute()
    {
        return "(".$this->lecture_number.") ".$this->name;
    }
}
