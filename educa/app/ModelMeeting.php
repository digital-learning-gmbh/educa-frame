<?php

namespace App;

use App\Models\SectionMeeting;
use Illuminate\Database\Eloquent\Model;

class ModelMeeting extends Model
{
    public function getModel() : HasMeetings
    {
        switch($this->model_type)
        {
            case "event":
                return Appointment::find($this->model_id);

            case "group":
                return Group::find($this->model_id);

            case "task":
                return Task::find($this->model_id);

            case "sectionMeeting":
                return SectionMeeting::find($this->model_id);

            case "lessonPlan":
                $model_id2 = explode("_",$this->model_id);
                return LessonPlan::find($model_id2[1]);

            case "lesson":
                return Lesson::find($this->model_id);
        }
    }
}
