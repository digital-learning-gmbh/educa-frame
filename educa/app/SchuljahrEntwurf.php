<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchuljahrEntwurf extends Model
{
    public function lessonPlan()
    {
        return $this->hasMany('App\LessonPlan');
    }

    public function schuljahr()
    {
        return $this->belongsTo('App\Schuljahr');
    }
}
