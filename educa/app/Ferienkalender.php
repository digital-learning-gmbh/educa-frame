<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ferienkalender extends Model
{
    public function ferienzeits()
    {
        return $this->hasMany("App\Ferienzeit", "ferienkalender_id");
    }

    public static function boot() {
        parent::boot();
        static::deleting(function($kalender) { // before delete() method call this
            $kalender->ferienzeits()->delete();
        });
    }
}
