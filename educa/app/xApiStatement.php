<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class xApiStatement extends Model
{
    protected $casts = [
        "actor" => "array",
        "verb" => "array",
        "context" => "array",
        "object" => "array",
        "result" => "array"
    ];

    public function cloudId()
    {
        return $this->belongsTo(CloudID::class,"actor_id");
    }
}
