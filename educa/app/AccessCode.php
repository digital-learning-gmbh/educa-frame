<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccessCode extends Model
{
    public function group()
    {
       return $this->belongsTo('App\Group','model_id');
    }
}
