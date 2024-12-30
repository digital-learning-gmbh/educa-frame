<?php

namespace App;

use App\ClingoModels\ClingoMapper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use StuPla\CloudSDK\formular\models\Formular;
use StuPla\CloudSDK\formular\models\FormularRevision;

class Schuljahr extends Model implements ClingoMapper
{
    public static function findForDate($standort_id, Carbon $date)
    {
        return Schuljahr::where('schule_id','=',$standort_id)->where('start','<=', $date->format("Y-m-d 23:59"))->where('ende','>=',$date->format("Y-m-d 00:00"))->first();
    }

    public function klassen()
    {
        return $this->hasMany('App\Klasse');
    }

    public function einstellungen()
    {
        return $this->hasMany('App\SchuljahrEinstellungen');
    }

    public function getEinstellungen($key, $default = "")
    {
        $setting = SchuljahrEinstellungen::where('schuljahr_id', '=', $this->id)->where('key', '=', $key)->first();
        if($setting == null)
        {
            return $default;
        }
        return $setting->value;
    }

    public function setEinstellungen($key, $value)
    {
        if($value == null)
        {
            return;
        }
        $setting = SchuljahrEinstellungen::where('schuljahr_id', '=', $this->id)->where('key', '=', $key)->first();
        if($setting != null)
        {
            $setting->value = $value;
            $setting->save();
        } else {
            $setting = new SchuljahrEinstellungen();
            $setting->schuljahr_id = $this->id;
            $setting->key = $key;
            $setting->value = $value;
            $setting->save();
        }
    }

    function getEinstellungenFormular($key)
    {
        $setting = SchuljahrEinstellungen::where('schuljahr_id', '=', $this->id)->where('key', '=', $key)->first();
        if($setting == null)
            return null;
        return FormularRevision::where('id', $setting->value)->first();
    }

    function setEinstellungenFormular($key, $formular)
    {
        if($formular == null)
        {
            $formular = Formular::all()->first()->id;
        }
        $this->setEinstellungen($key, $formular);
    }

    public function schule()
    {
        return $this->belongsTo('App\Schule');
    }

    public function entwurfe()
    {
        return $this->hasMany('App\SchuljahrEntwurf');
    }

    public function fehlzeiten()
    {
        return $this->hasMany('App\FehlzeitTyp')->orderBy('standart', 'DESC');
    }

    public function loadFromClingo($string)
    {
       // hier bleibt alles wie es ist!
    }

    public function getClingo($withRelationShips)
    {
        $string = "schuljahr(".$this->getClingoID().").\n";
        $string .="timesteps(1..".$this->getTimesteps().").\n";
        return $string;
    }

    public function getTimesteps()
    {
        return $this->period_length * $this->hours_day;
    }

    public function getTimeslots()
    {
        return $this->hasMany('App\Timeslot')->orderBy('begin');
    }

    public function getClingoID()
    {
        return "schuljahr".$this->id;
    }

    public function nextSchuljahr()
    {
        return $this->schule->schuljahre()->where('start','>',$this->start)->orderBy('year','ASC')->first();
    }

    public function previousSchuljahr()
    {
        return $this->schule->schuljahre()->where('start','<',$this->start)->orderBy('year','DESC')->first();
    }

    public function fsForKohorte($kohorte)
    {
        $fs = 1;
        $schuljar = $kohorte->schuljahr;
        if($schuljar == null)
        {
            Log::info($kohorte->name." Schuljahr ist null! (".$kohorte->schuljahr_id.")");
            return 1;
        }
        if($schuljar->schule_id != $this->schule_id) {
            Log::info("error");
        }
        for($i = 0; $i < 100; $i++)
        {
            if($schuljar == null || $schuljar->id == $this->id)
                return $fs;
            $fs++;
            $schuljar = $schuljar->nextSchuljahr();
        }
    }
}
