<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BBBServer extends Model
{
    protected $fillable = [
        'active', 'base_url', 'secret', 'load',
    ];
}
