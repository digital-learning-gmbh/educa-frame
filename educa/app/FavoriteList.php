<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FavoriteList extends Model
{
    protected $table = 'cloud_id_favorite_lists';

    public function owner()
    {
        return $this->belongsTo('App\CloudID','cloudid');
    }

    public function items()
    {
        return $this->hasMany("App\Favorite", "list_id");
    }
}

