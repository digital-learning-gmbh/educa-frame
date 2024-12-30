<?php

namespace StuPla\CloudSDK\formular\models;

use Illuminate\Database\Eloquent\Model;
use StuPla\CloudSDK\formular\controller\ViewerController;

class FormularRevision extends Model
{
    public function formular()
    {
        return $this->belongsTo('StuPla\CloudSDK\formular\models\Formular');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function html($formular_id, $values = "[]", $readonly = false)
    {
        $values = json_decode($values);
        return ViewerController::generateDisplayFromRevision($this->formular->id, $this, $formular_id, $values, $readonly);
    }
}
