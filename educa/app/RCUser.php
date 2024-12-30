<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RCUser extends Model
{
    protected $table = "rc_users";

    protected $hidden = [
        'password', 'access_token'
    ];

    public function cloudID()
    {
        return $this->belongsTo('App\CloudID','cloudid');
    }
}
