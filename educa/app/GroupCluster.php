<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupCluster extends Model
{

    public function groups()
    {
        return $this->belongsToMany('App\Group')->where('archived','=',0);
    }



}
