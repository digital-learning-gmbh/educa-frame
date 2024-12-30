<?php

namespace App;

use App\ClingoModels\ClingoMapper;
use App\Http\Controllers\Shared\ClingoConstant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Raum extends Model implements ClingoMapper
{
    CONST IDENTIFIER = "room";

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
    //
    public function loadFromClingo($string)
    {
        // TODO: Implement loadFromClingo() method.
    }

    public function getClingo( $withRelationship )
    {
        return ClingoConstant::basicClingoTranslate($this::IDENTIFIER,[$this->getClingoID(), $this->size]);
    }

    public function getClingoID()
    {
        return Raum::IDENTIFIER."_".$this->id;
    }

    public function getDisplayNameAttribute()
    {
        return $this->name;
    }

    public static function findByClingoId($clingo)
    {
        return Raum::findOrFail(str_replace(Raum::IDENTIFIER."_","",$clingo));
    }

    public function schulen()
    {
        return $this->belongsToMany("App\Schule");
    }

    public function delete() {
        $this->schulen()->sync([]);
        // Klassenraum zurücksetzen
        $klassen = Klasse::where('raum_id', $this->id)->get();
        foreach ($klassen as $klasse)
        {
            $klasse->raum_id = null;
            $klasse->save();
        }

        // Stundenplan
        DB::table('raum_lesson_plan')->where([
            'raum_id' => $this->id,
        ])->delete();

        // einzelene Stunden
        DB::table('raum_lesson')->where([
            'raum_id' => $this->id,
        ])->delete();

        parent::delete();
    }
}
