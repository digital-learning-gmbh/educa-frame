<?php

namespace App\Models\Devices;

use Illuminate\Database\Eloquent\Model;

class Ressource extends Model
{
    //
    public function reservations()
    {
        return $this->hasMany('App\Models\Devices\RessourceReservation','ressource_id');
    }
}
