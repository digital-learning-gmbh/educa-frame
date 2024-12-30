<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    public function getTypeTranslation()
    {
        if($this->typ == "written")
            return "Schriftlich";
        if($this->typ == "oral")
            return "Mündlich";
        return "Unbekannt";
    }

    public function getModule()
    {
        return $this->belongsTo("App\LehrplanEinheit","lehrplan_einheit_id");
    }

    public function klasse()
    {
        return $this->belongsTo("App\Klasse","klasse_id");
    }
}
