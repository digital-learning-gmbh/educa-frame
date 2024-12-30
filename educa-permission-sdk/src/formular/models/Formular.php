<?php

namespace StuPla\CloudSDK\formular\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Formular extends Model
{
    use SoftDeletes;

    public static function findWithName($name)
    {
        return Formular::where('name','=', $name)->first();
    }

    public function revisions()
{
    return $this->hasMany('StuPla\CloudSDK\formular\models\FormularRevision');
}

    public function getLastRevisionAttribute()
    {
        $lastRevision = FormularRevision::where('formular_id', 'LIKE', $this->id)->orderBy('created_at', 'DESC')->first();   // Noch keine Version vorhanden
        if($lastRevision == null)
        {
            $lastRevision = new FormularRevision();
            $lastRevision->data = "";
            $lastRevision->number = 0;
        }
        return $lastRevision;
    }

    public function schule()
    {
        return $this->belongsTo('App\Schule');
    }

    public function deleteAll()
    {
        foreach ($this->revisions as $revision)
        {
            // $revision->execution()->delete();
            $revision->delete();
        }
        $this->save();
        $this->forceDelete();
    }


}
