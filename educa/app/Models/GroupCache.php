<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupCache extends Model
{
    use HasFactory;

    protected $casts = ["cache" => "array"];


    public static function clearGroup($group_id)
    {
        GroupCache::where("groupid","=",$group_id)->delete();
    }
}
