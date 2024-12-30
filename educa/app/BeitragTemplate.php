<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BeitragTemplate extends Model
{
    public function media()
    {
        return $this->hasMany('App\BeitragTemplateMedia');
    }
    public function delete()
    {
        foreach ($this->media as $media)
        {
            $media->delete();
        }
        return parent::delete();
    }
}
