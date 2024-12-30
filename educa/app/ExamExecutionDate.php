<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use StuPla\CloudSDK\calendarful\Event\EventInterface;

class ExamExecutionDate extends Model implements EventInterface
{
    public function examExecution()
    {
        return $this->belongsTo('App\ModulExamExecution',"modul_exam_execution_id");
    }

    public function rooms()
    {
        return $this->belongsToMany("App\Raum","raum_exam_execution_date");
    }

    public function teacher()
    {
        return $this->belongsToMany("App\Lehrer","lehrer_exam_execution_date");
    }

    public function dozentname()
    {
        return join(", ", $this->teacher->pluck('displayName')->toArray());
    }

    public function raumname()
    {
        return join(", ", $this->rooms->pluck('name')->toArray());
    }


    public function getTeacherMappedAttribute()
    {
        $teachers =  DB::table("lehrer_exam_execution_date")->where("exam_execution_date_id","=",$this->id)->orderBy("part_exam_id")->get();
        $array = [];
        foreach ($teachers as $teacher)
        {
            if(!array_key_exists($teacher->part_exam_id,$array))
            {
                $array[$teacher->part_exam_id] = [];
            }
            $array[$teacher->part_exam_id][] = Lehrer::find($teacher->lehrer_id);
        }
        return $array;
    }

    public function getExamPartsAttribute()
    {
        return ModulPartExam::where('modul_exam_id','=',$this->examExecution->modulExam->id)
            ->where('group','=',$this->group)->with("examPartLabel")->with("subjects")->get();
    }

    public function delete()
    {
        $this->rooms()->sync([]);
        $this->teacher()->sync([]);
        return parent::delete();
    }

    public function getStartDate()
    {
        return $this->start == null ? null : Carbon::parse($this->start)->toDateTime();
    }

    public function getEndDate()
    {
        return $this->end == null ? null : Carbon::parse($this->end)->toDateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setStartDate(\DateTime $startDate)
    {
        $this->start = $startDate;
    }

    public function setEndDate(\DateTime $endDate)
    {
        $this->end = $endDate;
    }

    public function getDuration()
    {
        return $this->getStartDate()->diff($this->getEndDate());
    }

    public function getParentId()
    {
        return null;
    }

    public function getOccurrenceDate()
    {
        if($this->occurrenceDate == null)
        {
            return null;
        }

        if($this->occurrenceDateCache == null)
        {
            $this->occurrenceDateCache = new \DateTime($this->getStartDate());
        }
        return $this->occurrenceDateCache;
    }
}
