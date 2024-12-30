<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    //
    public function fach()
    {
        return $this->belongsTo("App\Fach");
    }

    public function exam()
    {
        return $this->belongsTo("App\Exam");
    }

    public function schuljahr()
    {
        return $this->belongsTo("App\Schuljahr");
    }

    public function getBelongsObjectAttribute()
    {
        if($this->model_type == "modul")
            return Module::find($this->model_id);
        if($this->model_type == "subject")
            return Fach::find($this->model_id);
    }

    public function getPartNotenAttribute()
    {
        $noten = Note::where('belongs_to_note','=',$this->id)->get();
        $noten->each->append('linkedSubjects');
        return $noten;
    }

    public function getLinkedSubjectsAttribute()
    {
        if($this->linked_subjects_id == null)
            return null;
        if(str_contains($this->linked_subjects_id,",")) {
            $subjectIds = explode(",", $this->linked_subjects_id);
            if($this->model_type == "subject_part")
                return Fach::whereIn("id", $subjectIds)->get();
            else
                return Module::whereIn("id", $subjectIds)->get();
        }
        if($this->model_type == "modul_part")
            return Module::where("id",'=',$this->linked_subjects_id)->get();
        return Fach::where("id",'=',$this->linked_subjects_id)->get();
    }
}
