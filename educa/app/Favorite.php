<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $table = 'favorite_lists_favorites';
    public function list()
    {
        return $this->belongsTo('App\FavoriteList','list_id');
    }
}
