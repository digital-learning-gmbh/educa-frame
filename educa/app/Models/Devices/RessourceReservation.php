<?php

namespace App\Models\Devices;

use Illuminate\Database\Eloquent\Model;

class RessourceReservation extends Model
{
    //
    public function user()
    {
        return $this->belongsTo('App\CloudID', "user_id")->withTrashed();
    }

    public function ressource()
    {
        return $this->belongsTo('App\Models\Devices\Ressource', "ressource_id");
    }
}
