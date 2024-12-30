<?php

namespace App;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\V1\xAPI\HasxAPIStatments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Beitrag extends Model
{
    use HasxAPIStatments;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName("beitrag");
    }

    private static $BLOCKED_IDS_CACHE = null;

    public function author()
    {
        return $this->belongsTo('App\CloudID','cloudid');
    }

    public function comments()
    {
        return $this->hasMany('App\BeitragComment');

    }

    public function commentsFiltered($cloud_user = null) {
        if($cloud_user == null) {
            $cloud_user = ApiController::getUserForToken(request());
            if($cloud_user == null) {
                $this->comments = $this->comments()->get();
                return;
            }
        }

        if(self::$BLOCKED_IDS_CACHE == null) {
        self::$BLOCKED_IDS_CACHE = DB::table('blocked_users')
            ->where('by_cloudid', '=', $cloud_user->id)->pluck("cloudid")->toArray();
        }
        $this->comments = $this->comments()->get()->filter(function($o,$key) {
           return !in_array($o->cloudid, self::$BLOCKED_IDS_CACHE);
        })->values();
    }

    public function likes()
    {
        return $this->hasMany('App\BeitragLike');
    }

    public function media()
    {
        return $this->hasMany('App\BeitragMedia');
    }

    public function mentions()
    {
        return $this->hasMany('App\Models\BeitragMention');
    }

    public function section()
    {
        $app = DB::table('section_group_apps')->where('id','=', $this->app_id)->first();
        return Section::find($app->section_id);
    }

    public function delete()
    {
        foreach ($this->comments as $comment)
        {
            $comment->delete();
        }
        foreach ($this->likes as $like)
        {
            $like->delete();
        }
        foreach ($this->media as $media)
        {
            $media->delete();
        }
        foreach ($this->mentions as $mention)
        {
            $mention->delete();
        }
        return parent::delete();
    }

    function getObjectType()
    {
        return "announcement";
    }
}
