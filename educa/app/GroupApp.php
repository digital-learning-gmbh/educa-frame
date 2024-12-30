<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GroupApp extends Model
{
    protected $table = "group_apps";

    function loadConfig($parameters) {}

    function getNameOfClass() {}

    function needsCloudID()
    {
        return false;
    }

    function saveConfig($parameters) {
        DB::table('group_apps')
            ->where('id','=', $this->dbReiter->id)
            ->update([ "parameters" => json_encode($parameters)]);
    }

    function getRechte() {}

}
