<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Schule extends Model
{
    public function facher()
    {
        return $this->hasMany('App\Fach');
    }

    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    public function studiengange()
    {
        return $this->belongsToMany('App\Studium');
    }

    public function schuljahre()
    {
        return $this->hasMany('App\Schuljahr')->orderBy("year");
    }

    public function formulare()
    {
        return $this->hasMany('StuPla\CloudSDK\formular\models\Formular');
    }

    public function raume()
    {
        return $this->belongsToMany('App\Raum');
    }

    public function lehrer()
    {
        return $this->belongsToMany('App\Lehrer');
    }

    public function fach()
    {
        return $this->hasMany('App\Fach');
    }

    public function schuler()
    {
        return $this->belongsToMany('App\Schuler',"schuler_schule");
    }

    public function getCurrentSchoolYear()
    {
        $currentTime = date("Y-m-d H:i");
        $schuljahr = Schuljahr::where('schule_id', '=', $this->id)->where('start', '<', $currentTime)->where('ende','>', $currentTime)->first();
        if($schuljahr != null)
        {
            return $schuljahr;
        }
        return $this->schuljahre()->first();
    }

    public function addinfo()
    {
        return $this->hasOne("App\AdditionalInfo","id","info_id");
    }

    public function getEinstellungen($key, $default = "")
    {
        $setting = SchulEinstellung::where('schule_id', '=', $this->id)->where('key', '=', $key)->first();
        if($setting == null)
        {
            return $default;
        }
        return $setting->value;
    }

    public function getAllSettingsAttribute()
    {
        return SchulEinstellung::where('schule_id', '=', $this->id)->get();
    }

    public function setEinstellungen($key, $value)
    {
        if($value == null)
        {
            SchulEinstellung::where('schule_id', '=', $this->id)->where('key', '=', $key)->delete();
            return;
        }
        $setting = SchulEinstellung::where('schule_id', '=', $this->id)->where('key', '=', $key)->first();
        if($setting != null)
        {
            $setting->value = $value;
            $setting->save();
        } else {
            $setting = new SchulEinstellung();
            $setting->schule_id = $this->id;
            $setting->key = $key;
            $setting->value = $value;
            $setting->save();
        }
    }

    public function kontakte()
    {
        return $this->belongsToMany('App\Kontakt',"kontakt_schule");
    }

    public function unternehmen()
    {
        return $this->belongsToMany('App\Kontakt',"kontakt_schule")->where('type', '=','unternehmen');
    }

    public function dokumente($parent_id = null)
    {
        $ids = DB::table('model_dokument')
            ->where('model_id', '=', $this->id)
            ->where('model_type', '=', 'schule')
            ->pluck('dokument_id')->toArray();
        if($parent_id == null)
        {
            return Dokument::find($ids);
        }
        return Dokument::where('parent_id', '=', '0')->whereIn('id', $ids)->get();
    }

    public function abk()
    {
        if($this->abk == null)
            return $this->name;
        return $this->abk;
    }

    public function kohorten()
    {
        return $this->hasMany('App\Kohorte')->orderBy("name");
    }

    public function getAddInfo()
    {
        if ($this->info_id == null) {
            $addInfo = new AdditionalInfo();
            $addInfo->save();
            $this->info_id = $addInfo->id;
            $this->save();
        }
        else{
            $addInfo = AdditionalInfo::find($this->info_id);
            if($addInfo == null)
            {
                // wer auch immer hier die Daten kaputt macht
                $this->info_id = null;
                $this->save();
                return $this->getAddInfo();
            }
        }
        return $addInfo;
    }

}
