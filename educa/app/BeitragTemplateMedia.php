<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BeitragTemplateMedia extends Model
{
    //

    public function delete()
    {
        if( Storage::disk('public')->delete($this->disk_name) );
            return parent::delete();
       return false;
    }
}
