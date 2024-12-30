<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SystemEinstellung extends Model
{
    public static function getEinstellungen($key, $default = "")
    {
        $setting = SystemEinstellung::where('key', '=', $key)->first();
        if($setting == null)
        {
            return $default;
        }
        return $setting->value;
    }

    public static function setEinstellungen($key, $value)
    {
        $setting = SystemEinstellung::where('key', '=', $key)->first();
        if($setting != null)
        {
            $setting->value = $value;
            $setting->save();
        } else {
            $setting = new SystemEinstellung();
            $setting->key = $key;
            $setting->value = $value;
            $setting->save();
        }
    }
}
